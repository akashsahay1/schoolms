<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\BookIssue;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LibraryReportController extends Controller
{
    /**
     * Display library reports dashboard.
     */
    public function index()
    {
        // Summary statistics
        $stats = [
            'total_books' => Book::count(),
            'available_books' => Book::sum('available_copies'),
            'issued_books' => BookIssue::issued()->count(),
            'overdue_books' => BookIssue::overdue()->count(),
            'total_categories' => BookCategory::count(),
            'total_fine_collected' => BookIssue::returned()->sum('fine_amount'),
        ];

        // Recent issues
        $recentIssues = BookIssue::with(['book', 'student'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Overdue books
        $overdueBooks = BookIssue::with(['book', 'student'])
            ->overdue()
            ->orderBy('due_date', 'asc')
            ->limit(10)
            ->get();

        // Category-wise book count
        $categoryStats = BookCategory::withCount('books')
            ->orderBy('books_count', 'desc')
            ->get();

        return view('admin.library.reports.index', compact('stats', 'recentIssues', 'overdueBooks', 'categoryStats'));
    }

    /**
     * Book issue report with date filters.
     */
    public function issues(Request $request)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->format('Y-m-d'));

        $query = BookIssue::with(['book', 'student', 'issuedBy'])
            ->whereBetween('issue_date', [$fromDate, $toDate]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        $issues = $query->orderBy('issue_date', 'desc')->paginate(20);

        // Summary for the period
        $summary = [
            'total_issued' => BookIssue::whereBetween('issue_date', [$fromDate, $toDate])->count(),
            'total_returned' => BookIssue::whereBetween('return_date', [$fromDate, $toDate])->returned()->count(),
            'total_fine' => BookIssue::whereBetween('return_date', [$fromDate, $toDate])->sum('fine_amount'),
            'overdue_count' => BookIssue::whereBetween('issue_date', [$fromDate, $toDate])->overdue()->count(),
        ];

        $students = Student::where('status', 'active')->orderBy('first_name')->get();

        return view('admin.library.reports.issues', compact('issues', 'summary', 'students', 'fromDate', 'toDate'));
    }

    /**
     * Overdue books report.
     */
    public function overdue(Request $request)
    {
        $query = BookIssue::with(['book', 'student'])
            ->overdue();

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        $overdueIssues = $query->orderBy('due_date', 'asc')->paginate(20);

        // Calculate total pending fine
        $totalPendingFine = 0;
        foreach ($overdueIssues as $issue) {
            $totalPendingFine += $issue->calculated_fine;
        }

        $students = Student::where('status', 'active')->orderBy('first_name')->get();

        return view('admin.library.reports.overdue', compact('overdueIssues', 'totalPendingFine', 'students'));
    }

    /**
     * Book inventory report.
     */
    public function inventory(Request $request)
    {
        $query = Book::with('category');

        if ($request->filled('category_id')) {
            $query->where('book_category_id', $request->category_id);
        }

        if ($request->filled('availability')) {
            if ($request->availability === 'available') {
                $query->where('available_copies', '>', 0);
            } else {
                $query->where('available_copies', '<=', 0);
            }
        }

        $books = $query->orderBy('title')->paginate(20);

        // Summary
        $summary = [
            'total_titles' => Book::count(),
            'total_copies' => Book::sum('total_copies'),
            'available_copies' => Book::sum('available_copies'),
            'issued_copies' => Book::sum('total_copies') - Book::sum('available_copies'),
            'total_value' => Book::selectRaw('SUM(price * total_copies) as total')->first()->total ?? 0,
        ];

        $categories = BookCategory::active()->orderBy('name')->get();

        return view('admin.library.reports.inventory', compact('books', 'summary', 'categories'));
    }

    /**
     * Fine collection report.
     */
    public function fines(Request $request)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->format('Y-m-d'));

        $query = BookIssue::with(['book', 'student'])
            ->where('fine_amount', '>', 0)
            ->whereBetween('return_date', [$fromDate, $toDate]);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        $fineRecords = $query->orderBy('return_date', 'desc')->paginate(20);

        // Summary
        $summary = [
            'total_fine_collected' => BookIssue::where('fine_amount', '>', 0)
                ->whereBetween('return_date', [$fromDate, $toDate])
                ->sum('fine_amount'),
            'total_records' => BookIssue::where('fine_amount', '>', 0)
                ->whereBetween('return_date', [$fromDate, $toDate])
                ->count(),
            'pending_fine' => 0, // Will calculate from overdue books
        ];

        // Calculate pending fine from overdue books
        $overdueBooks = BookIssue::overdue()->get();
        foreach ($overdueBooks as $issue) {
            $summary['pending_fine'] += $issue->calculated_fine;
        }

        $students = Student::where('status', 'active')->orderBy('first_name')->get();

        return view('admin.library.reports.fines', compact('fineRecords', 'summary', 'students', 'fromDate', 'toDate'));
    }

    /**
     * Student-wise library report.
     */
    public function studentWise(Request $request)
    {
        $query = Student::with(['schoolClass', 'section'])
            ->withCount(['bookIssues', 'bookIssues as current_issues_count' => function ($q) {
                $q->issued();
            }])
            ->withSum('bookIssues', 'fine_amount')
            ->where('status', 'active');

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        $students = $query->orderBy('first_name')->paginate(20);

        $classes = \App\Models\SchoolClass::active()->ordered()->get();

        return view('admin.library.reports.student-wise', compact('students', 'classes'));
    }

    /**
     * Export report to Excel.
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'issues');
        $fromDate = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->format('Y-m-d'));

        // This would use Laravel Excel package
        // For now, return CSV download
        $filename = "library_{$type}_report_{$fromDate}_to_{$toDate}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($type, $fromDate, $toDate) {
            $file = fopen('php://output', 'w');

            switch ($type) {
                case 'issues':
                    fputcsv($file, ['Issue Date', 'Book Title', 'Student Name', 'Due Date', 'Return Date', 'Fine Amount', 'Status']);
                    $records = BookIssue::with(['book', 'student'])
                        ->whereBetween('issue_date', [$fromDate, $toDate])
                        ->get();
                    foreach ($records as $record) {
                        fputcsv($file, [
                            $record->issue_date->format('Y-m-d'),
                            $record->book->title ?? 'N/A',
                            $record->student->full_name ?? 'N/A',
                            $record->due_date->format('Y-m-d'),
                            $record->return_date ? $record->return_date->format('Y-m-d') : '-',
                            $record->fine_amount ?? 0,
                            ucfirst($record->status),
                        ]);
                    }
                    break;

                case 'overdue':
                    fputcsv($file, ['Book Title', 'Student Name', 'Issue Date', 'Due Date', 'Overdue Days', 'Calculated Fine']);
                    $records = BookIssue::with(['book', 'student'])->overdue()->get();
                    foreach ($records as $record) {
                        fputcsv($file, [
                            $record->book->title ?? 'N/A',
                            $record->student->full_name ?? 'N/A',
                            $record->issue_date->format('Y-m-d'),
                            $record->due_date->format('Y-m-d'),
                            $record->overdue_days,
                            $record->calculated_fine,
                        ]);
                    }
                    break;

                case 'inventory':
                    fputcsv($file, ['Title', 'Author', 'ISBN', 'Category', 'Total Copies', 'Available', 'Price']);
                    $records = Book::with('category')->get();
                    foreach ($records as $record) {
                        fputcsv($file, [
                            $record->title,
                            $record->author,
                            $record->isbn,
                            $record->category->name ?? 'N/A',
                            $record->total_copies,
                            $record->available_copies,
                            $record->price,
                        ]);
                    }
                    break;

                case 'fines':
                    fputcsv($file, ['Return Date', 'Book Title', 'Student Name', 'Overdue Days', 'Fine Amount']);
                    $records = BookIssue::with(['book', 'student'])
                        ->where('fine_amount', '>', 0)
                        ->whereBetween('return_date', [$fromDate, $toDate])
                        ->get();
                    foreach ($records as $record) {
                        fputcsv($file, [
                            $record->return_date->format('Y-m-d'),
                            $record->book->title ?? 'N/A',
                            $record->student->full_name ?? 'N/A',
                            $record->overdue_days,
                            $record->fine_amount,
                        ]);
                    }
                    break;
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
