<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\BookIssue;
use App\Models\Student;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LibraryController extends Controller
{
    /**
     * Get the authenticated student.
     */
    private function getStudent()
    {
        return Student::where('user_id', Auth::id())->first();
    }

    /**
     * Display library dashboard for student.
     */
    public function index()
    {
        $student = $this->getStudent();

        if (!$student) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Student profile not found.');
        }

        // Current issued books
        $currentIssues = BookIssue::with(['book.category'])
            ->where('student_id', $student->id)
            ->issued()
            ->orderBy('due_date', 'asc')
            ->get();

        // Overdue books
        $overdueBooks = BookIssue::with(['book'])
            ->where('student_id', $student->id)
            ->overdue()
            ->get();

        // Stats
        $stats = [
            'current_books' => $currentIssues->count(),
            'overdue_books' => $overdueBooks->count(),
            'total_borrowed' => BookIssue::where('student_id', $student->id)->count(),
            'total_fine_paid' => BookIssue::where('student_id', $student->id)
                ->returned()
                ->sum('fine_amount'),
        ];

        // Calculate pending fine
        $pendingFine = 0;
        foreach ($overdueBooks as $issue) {
            $pendingFine += $issue->calculated_fine;
        }
        $stats['pending_fine'] = $pendingFine;

        // Fine per day setting
        $finePerDay = Setting::get('library_fine_per_day', 2);

        return view('portal.library.index', compact('student', 'currentIssues', 'overdueBooks', 'stats', 'finePerDay'));
    }

    /**
     * Display borrowing history.
     */
    public function history(Request $request)
    {
        $student = $this->getStudent();

        if (!$student) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Student profile not found.');
        }

        $query = BookIssue::with(['book.category'])
            ->where('student_id', $student->id);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('issue_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('issue_date', '<=', $request->to_date);
        }

        $history = $query->orderBy('issue_date', 'desc')->paginate(15);

        // Summary
        $summary = [
            'total_borrowed' => BookIssue::where('student_id', $student->id)->count(),
            'total_returned' => BookIssue::where('student_id', $student->id)->returned()->count(),
            'total_fine' => BookIssue::where('student_id', $student->id)->sum('fine_amount'),
        ];

        return view('portal.library.history', compact('student', 'history', 'summary'));
    }

    /**
     * Search available books.
     */
    public function search(Request $request)
    {
        $student = $this->getStudent();

        if (!$student) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Student profile not found.');
        }

        $query = Book::with('category')->active();

        // Search by title, author, or ISBN
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('book_category_id', $request->category_id);
        }

        // Filter by availability
        if ($request->filled('available')) {
            $query->where('available_copies', '>', 0);
        }

        $books = $query->orderBy('title')->paginate(15);
        $categories = BookCategory::active()->orderBy('name')->get();

        return view('portal.library.search', compact('student', 'books', 'categories'));
    }

    /**
     * View book details.
     */
    public function show(Book $book)
    {
        $student = $this->getStudent();

        if (!$student) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Student profile not found.');
        }

        $book->load('category');

        // Check if student has already borrowed this book
        $currentIssue = BookIssue::where('student_id', $student->id)
            ->where('book_id', $book->id)
            ->issued()
            ->first();

        // Get borrow history for this book by student
        $borrowHistory = BookIssue::where('student_id', $student->id)
            ->where('book_id', $book->id)
            ->orderBy('issue_date', 'desc')
            ->get();

        return view('portal.library.show', compact('student', 'book', 'currentIssue', 'borrowHistory'));
    }
}
