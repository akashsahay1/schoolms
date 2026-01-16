<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeCollection;
use App\Models\FeeStructure;
use App\Models\FeeType;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\FeeCollectionExport;
use App\Exports\OutstandingFeesExport;
use App\Exports\DailyCollectionExport;

class FeeReportController extends Controller
{
    /**
     * Display financial analytics dashboard.
     */
    public function index()
    {
        $activeYear = AcademicYear::getActive();
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Overall Statistics
        $stats = [
            'total_collected' => FeeCollection::when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))->sum('paid_amount'),
            'total_discount' => FeeCollection::when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))->sum('discount_amount'),
            'total_fine' => FeeCollection::when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))->sum('fine_amount'),
            'this_month' => FeeCollection::whereMonth('payment_date', $currentMonth)->whereYear('payment_date', $currentYear)->sum('paid_amount'),
            'today' => FeeCollection::whereDate('payment_date', today())->sum('paid_amount'),
            'total_students' => Student::where('status', 'active')->count(),
        ];

        // Calculate outstanding
        $totalFeeAmount = FeeStructure::where('is_active', true)
            ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
            ->sum('amount');
        $stats['total_outstanding'] = max(0, $totalFeeAmount - $stats['total_collected']);

        // Monthly collection data for chart (last 12 months)
        $monthlyData = FeeCollection::select(
                DB::raw('YEAR(payment_date) as year'),
                DB::raw('MONTH(payment_date) as month'),
                DB::raw('SUM(paid_amount) as total')
            )
            ->where('payment_date', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $chartLabels = [];
        $chartData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $chartLabels[] = $date->format('M Y');
            $found = $monthlyData->first(fn($item) => $item->year == $date->year && $item->month == $date->month);
            $chartData[] = $found ? (float)$found->total : 0;
        }

        // Collection by payment mode
        $paymentModeData = FeeCollection::select('payment_mode', DB::raw('SUM(paid_amount) as total'))
            ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
            ->groupBy('payment_mode')
            ->get();

        // Collection by fee type
        $feeTypeData = FeeCollection::select('fee_structures.fee_type_id', 'fee_types.name', DB::raw('SUM(fee_collections.paid_amount) as total'))
            ->join('fee_structures', 'fee_collections.fee_structure_id', '=', 'fee_structures.id')
            ->join('fee_types', 'fee_structures.fee_type_id', '=', 'fee_types.id')
            ->when($activeYear, fn($q) => $q->where('fee_collections.academic_year_id', $activeYear->id))
            ->groupBy('fee_structures.fee_type_id', 'fee_types.name')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        // Recent collections
        $recentCollections = FeeCollection::with(['student', 'feeStructure.feeType'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Class-wise collection summary
        $classWiseData = FeeCollection::select('students.class_id', 'school_classes.name as class_name', DB::raw('SUM(fee_collections.paid_amount) as total'))
            ->join('students', 'fee_collections.student_id', '=', 'students.id')
            ->join('school_classes', 'students.class_id', '=', 'school_classes.id')
            ->when($activeYear, fn($q) => $q->where('fee_collections.academic_year_id', $activeYear->id))
            ->groupBy('students.class_id', 'school_classes.name')
            ->orderBy('school_classes.display_order')
            ->get();

        return view('admin.fees.reports.index', compact(
            'stats', 'chartLabels', 'chartData', 'paymentModeData',
            'feeTypeData', 'recentCollections', 'classWiseData', 'activeYear'
        ));
    }

    /**
     * Collection report with date filters.
     */
    public function collection(Request $request)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->format('Y-m-d'));

        $query = FeeCollection::with(['student.schoolClass', 'feeStructure.feeType', 'collectedBy'])
            ->whereBetween('payment_date', [$fromDate, $toDate]);

        // Filters
        if ($request->filled('class_id')) {
            $query->whereHas('student', fn($q) => $q->where('class_id', $request->class_id));
        }

        if ($request->filled('fee_type_id')) {
            $query->whereHas('feeStructure', fn($q) => $q->where('fee_type_id', $request->fee_type_id));
        }

        if ($request->filled('payment_mode')) {
            $query->where('payment_mode', $request->payment_mode);
        }

        $collections = $query->orderBy('payment_date', 'desc')->paginate(20);

        // Summary
        $summaryQuery = FeeCollection::whereBetween('payment_date', [$fromDate, $toDate]);
        if ($request->filled('class_id')) {
            $summaryQuery->whereHas('student', fn($q) => $q->where('class_id', $request->class_id));
        }

        $summary = [
            'total_amount' => (clone $summaryQuery)->sum('paid_amount'),
            'total_discount' => (clone $summaryQuery)->sum('discount_amount'),
            'total_fine' => (clone $summaryQuery)->sum('fine_amount'),
            'total_transactions' => (clone $summaryQuery)->count(),
        ];

        // Daily breakdown for the period
        $dailyData = FeeCollection::select(
                DB::raw('DATE(payment_date) as date'),
                DB::raw('SUM(paid_amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('payment_date', [$fromDate, $toDate])
            ->groupBy(DB::raw('DATE(payment_date)'))
            ->orderBy('date')
            ->get();

        $classes = SchoolClass::active()->ordered()->get();
        $feeTypes = FeeType::active()->get();

        return view('admin.fees.reports.collection', compact(
            'collections', 'summary', 'dailyData', 'classes', 'feeTypes', 'fromDate', 'toDate'
        ));
    }

    /**
     * Outstanding fees report.
     */
    public function outstanding(Request $request)
    {
        $activeYear = AcademicYear::getActive();

        $query = Student::with(['schoolClass', 'section'])
            ->where('status', 'active');

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        $students = $query->orderBy('first_name')->get();

        // Calculate outstanding for each student
        $outstandingData = [];
        $totalOutstanding = 0;

        foreach ($students as $student) {
            // Get fee structures for student's class
            $feeStructures = FeeStructure::where('class_id', $student->class_id)
                ->where('is_active', true)
                ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
                ->get();

            $totalFee = $feeStructures->sum('amount');

            // Get paid amount
            $paidAmount = FeeCollection::where('student_id', $student->id)
                ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
                ->sum('paid_amount');

            $discountAmount = FeeCollection::where('student_id', $student->id)
                ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
                ->sum('discount_amount');

            $outstanding = max(0, $totalFee - $paidAmount - $discountAmount);

            if ($outstanding > 0 || !$request->filled('hide_paid')) {
                $outstandingData[] = [
                    'student' => $student,
                    'total_fee' => $totalFee,
                    'paid_amount' => $paidAmount,
                    'discount' => $discountAmount,
                    'outstanding' => $outstanding,
                ];
                $totalOutstanding += $outstanding;
            }
        }

        // Sort by outstanding amount descending
        usort($outstandingData, fn($a, $b) => $b['outstanding'] <=> $a['outstanding']);

        // Paginate manually
        $page = $request->get('page', 1);
        $perPage = 20;
        $total = count($outstandingData);
        $outstandingData = array_slice($outstandingData, ($page - 1) * $perPage, $perPage);

        $classes = SchoolClass::active()->ordered()->get();

        // Summary by class
        $classSummary = [];
        foreach ($classes as $class) {
            $classStudents = Student::where('class_id', $class->id)->where('status', 'active')->pluck('id');

            $classFee = FeeStructure::where('class_id', $class->id)
                ->where('is_active', true)
                ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
                ->sum('amount') * $classStudents->count();

            $classPaid = FeeCollection::whereIn('student_id', $classStudents)
                ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
                ->sum('paid_amount');

            $classSummary[] = [
                'class' => $class,
                'total_fee' => $classFee,
                'paid' => $classPaid,
                'outstanding' => max(0, $classFee - $classPaid),
                'percentage' => $classFee > 0 ? round(($classPaid / $classFee) * 100, 1) : 0,
            ];
        }

        return view('admin.fees.reports.outstanding', compact(
            'outstandingData', 'totalOutstanding', 'classes', 'classSummary', 'total', 'page', 'perPage'
        ));
    }

    /**
     * Fee type wise collection report.
     */
    public function feeTypeWise(Request $request)
    {
        $fromDate = $request->get('from_date', now()->startOfYear()->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->format('Y-m-d'));

        $feeTypeData = FeeCollection::select(
                'fee_types.id',
                'fee_types.name',
                DB::raw('SUM(fee_collections.paid_amount) as total_collected'),
                DB::raw('COUNT(DISTINCT fee_collections.student_id) as student_count'),
                DB::raw('COUNT(fee_collections.id) as transaction_count')
            )
            ->join('fee_structures', 'fee_collections.fee_structure_id', '=', 'fee_structures.id')
            ->join('fee_types', 'fee_structures.fee_type_id', '=', 'fee_types.id')
            ->whereBetween('fee_collections.payment_date', [$fromDate, $toDate])
            ->groupBy('fee_types.id', 'fee_types.name')
            ->orderBy('total_collected', 'desc')
            ->get();

        $totalCollected = $feeTypeData->sum('total_collected');

        return view('admin.fees.reports.fee-type-wise', compact('feeTypeData', 'totalCollected', 'fromDate', 'toDate'));
    }

    /**
     * Class wise collection report.
     */
    public function classWise(Request $request)
    {
        $fromDate = $request->get('from_date', now()->startOfYear()->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->format('Y-m-d'));
        $activeYear = AcademicYear::getActive();

        $classes = SchoolClass::active()->ordered()->get();
        $classData = [];

        foreach ($classes as $class) {
            $studentIds = Student::where('class_id', $class->id)
                ->where('status', 'active')
                ->pluck('id');

            $collected = FeeCollection::whereIn('student_id', $studentIds)
                ->whereBetween('payment_date', [$fromDate, $toDate])
                ->sum('paid_amount');

            $studentCount = $studentIds->count();

            // Total fee for class
            $totalFee = FeeStructure::where('class_id', $class->id)
                ->where('is_active', true)
                ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
                ->sum('amount') * $studentCount;

            $classData[] = [
                'class' => $class,
                'student_count' => $studentCount,
                'total_fee' => $totalFee,
                'collected' => $collected,
                'outstanding' => max(0, $totalFee - $collected),
                'percentage' => $totalFee > 0 ? round(($collected / $totalFee) * 100, 1) : 0,
            ];
        }

        $totalCollected = collect($classData)->sum('collected');
        $totalOutstanding = collect($classData)->sum('outstanding');

        return view('admin.fees.reports.class-wise', compact('classData', 'totalCollected', 'totalOutstanding', 'fromDate', 'toDate'));
    }

    /**
     * Daily collection report.
     */
    public function daily(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));

        $collections = FeeCollection::with(['student.schoolClass', 'feeStructure.feeType', 'collectedBy'])
            ->whereDate('payment_date', $date)
            ->orderBy('created_at', 'desc')
            ->get();

        $summary = [
            'total' => $collections->sum('paid_amount'),
            'cash' => $collections->where('payment_mode', 'cash')->sum('paid_amount'),
            'online' => $collections->where('payment_mode', 'online')->sum('paid_amount'),
            'cheque' => $collections->where('payment_mode', 'cheque')->sum('paid_amount'),
            'card' => $collections->where('payment_mode', 'card')->sum('paid_amount'),
            'bank_transfer' => $collections->where('payment_mode', 'bank_transfer')->sum('paid_amount'),
            'count' => $collections->count(),
        ];

        return view('admin.fees.reports.daily', compact('collections', 'summary', 'date'));
    }

    /**
     * Export collection report.
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'collection');
        $fromDate = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->format('Y-m-d'));

        $filename = "fee_{$type}_report_{$fromDate}_to_{$toDate}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($type, $fromDate, $toDate) {
            $file = fopen('php://output', 'w');

            switch ($type) {
                case 'collection':
                    fputcsv($file, ['Receipt No', 'Date', 'Student', 'Class', 'Fee Type', 'Amount', 'Discount', 'Fine', 'Paid', 'Mode']);
                    $records = FeeCollection::with(['student.schoolClass', 'feeStructure.feeType'])
                        ->whereBetween('payment_date', [$fromDate, $toDate])
                        ->orderBy('payment_date')
                        ->get();
                    foreach ($records as $record) {
                        fputcsv($file, [
                            $record->receipt_no,
                            $record->payment_date->format('Y-m-d'),
                            $record->student->full_name ?? 'N/A',
                            $record->student->schoolClass->name ?? 'N/A',
                            $record->feeStructure->feeType->name ?? 'N/A',
                            $record->amount,
                            $record->discount_amount,
                            $record->fine_amount,
                            $record->paid_amount,
                            ucfirst(str_replace('_', ' ', $record->payment_mode)),
                        ]);
                    }
                    break;

                case 'outstanding':
                    fputcsv($file, ['Student', 'Admission No', 'Class', 'Total Fee', 'Paid', 'Discount', 'Outstanding']);
                    $activeYear = AcademicYear::getActive();
                    $students = Student::with('schoolClass')->where('status', 'active')->get();
                    foreach ($students as $student) {
                        $totalFee = FeeStructure::where('class_id', $student->class_id)
                            ->where('is_active', true)
                            ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
                            ->sum('amount');
                        $paid = FeeCollection::where('student_id', $student->id)
                            ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
                            ->sum('paid_amount');
                        $discount = FeeCollection::where('student_id', $student->id)
                            ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
                            ->sum('discount_amount');
                        $outstanding = max(0, $totalFee - $paid - $discount);

                        if ($outstanding > 0) {
                            fputcsv($file, [
                                $student->full_name,
                                $student->admission_no,
                                $student->schoolClass->name ?? 'N/A',
                                $totalFee,
                                $paid,
                                $discount,
                                $outstanding,
                            ]);
                        }
                    }
                    break;

                case 'daily':
                    $date = request('date', now()->format('Y-m-d'));
                    fputcsv($file, ['Receipt No', 'Time', 'Student', 'Fee Type', 'Amount', 'Mode', 'Collected By']);
                    $records = FeeCollection::with(['student', 'feeStructure.feeType', 'collectedBy'])
                        ->whereDate('payment_date', $date)
                        ->orderBy('created_at')
                        ->get();
                    foreach ($records as $record) {
                        fputcsv($file, [
                            $record->receipt_no,
                            $record->created_at->format('H:i:s'),
                            $record->student->full_name ?? 'N/A',
                            $record->feeStructure->feeType->name ?? 'N/A',
                            $record->paid_amount,
                            ucfirst(str_replace('_', ' ', $record->payment_mode)),
                            $record->collectedBy->name ?? 'N/A',
                        ]);
                    }
                    break;
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get chart data for AJAX requests.
     */
    public function chartData(Request $request)
    {
        $type = $request->get('type', 'monthly');
        $year = $request->get('year', now()->year);

        switch ($type) {
            case 'monthly':
                $data = FeeCollection::select(
                        DB::raw('MONTH(payment_date) as month'),
                        DB::raw('SUM(paid_amount) as total')
                    )
                    ->whereYear('payment_date', $year)
                    ->groupBy(DB::raw('MONTH(payment_date)'))
                    ->orderBy('month')
                    ->get();

                $labels = [];
                $values = [];
                for ($m = 1; $m <= 12; $m++) {
                    $labels[] = date('M', mktime(0, 0, 0, $m, 1));
                    $found = $data->first(fn($item) => $item->month == $m);
                    $values[] = $found ? (float)$found->total : 0;
                }
                break;

            case 'daily':
                $month = $request->get('month', now()->month);
                $data = FeeCollection::select(
                        DB::raw('DAY(payment_date) as day'),
                        DB::raw('SUM(paid_amount) as total')
                    )
                    ->whereYear('payment_date', $year)
                    ->whereMonth('payment_date', $month)
                    ->groupBy(DB::raw('DAY(payment_date)'))
                    ->orderBy('day')
                    ->get();

                $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                $labels = [];
                $values = [];
                for ($d = 1; $d <= $daysInMonth; $d++) {
                    $labels[] = $d;
                    $found = $data->first(fn($item) => $item->day == $d);
                    $values[] = $found ? (float)$found->total : 0;
                }
                break;

            default:
                $labels = [];
                $values = [];
        }

        return response()->json([
            'labels' => $labels,
            'data' => $values,
        ]);
    }

    /**
     * Export collection report to Excel.
     */
    public function exportExcel(Request $request)
    {
        $type = $request->get('type', 'collection');
        $fromDate = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->format('Y-m-d'));

        switch ($type) {
            case 'collection':
                $filename = "fee_collection_{$fromDate}_to_{$toDate}.xlsx";
                return Excel::download(
                    new FeeCollectionExport(
                        $fromDate,
                        $toDate,
                        $request->get('class_id'),
                        $request->get('fee_type_id'),
                        $request->get('payment_mode')
                    ),
                    $filename
                );

            case 'outstanding':
                $filename = "outstanding_fees_" . now()->format('Y-m-d') . ".xlsx";
                return Excel::download(
                    new OutstandingFeesExport(
                        $request->get('class_id'),
                        $request->boolean('hide_paid')
                    ),
                    $filename
                );

            case 'daily':
                $date = $request->get('date', now()->format('Y-m-d'));
                $filename = "daily_collection_{$date}.xlsx";
                return Excel::download(new DailyCollectionExport($date), $filename);

            default:
                return back()->with('error', 'Invalid export type');
        }
    }

    /**
     * Export collection report to PDF.
     */
    public function exportPdf(Request $request)
    {
        $type = $request->get('type', 'collection');
        $fromDate = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->format('Y-m-d'));

        switch ($type) {
            case 'collection':
                $query = FeeCollection::with(['student.schoolClass', 'feeStructure.feeType', 'collectedBy'])
                    ->whereBetween('payment_date', [$fromDate, $toDate]);

                if ($request->filled('class_id')) {
                    $query->whereHas('student', fn($q) => $q->where('class_id', $request->class_id));
                }

                if ($request->filled('fee_type_id')) {
                    $query->whereHas('feeStructure', fn($q) => $q->where('fee_type_id', $request->fee_type_id));
                }

                if ($request->filled('payment_mode')) {
                    $query->where('payment_mode', $request->payment_mode);
                }

                $collections = $query->orderBy('payment_date', 'desc')->get();

                $summary = [
                    'total_amount' => $collections->sum('paid_amount'),
                    'total_discount' => $collections->sum('discount_amount'),
                    'total_fine' => $collections->sum('fine_amount'),
                    'total_transactions' => $collections->count(),
                ];

                $pdf = Pdf::loadView('admin.fees.reports.pdf.collection', compact('collections', 'summary', 'fromDate', 'toDate'));
                $pdf->setPaper('a4', 'landscape');
                return $pdf->download("fee_collection_{$fromDate}_to_{$toDate}.pdf");

            case 'outstanding':
                $activeYear = AcademicYear::getActive();

                $query = Student::with(['schoolClass', 'section'])
                    ->where('status', 'active');

                if ($request->filled('class_id')) {
                    $query->where('class_id', $request->class_id);
                }

                $students = $query->orderBy('first_name')->get();
                $outstandingData = [];
                $totalOutstanding = 0;

                foreach ($students as $student) {
                    $feeStructures = FeeStructure::where('class_id', $student->class_id)
                        ->where('is_active', true)
                        ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
                        ->get();

                    $totalFee = $feeStructures->sum('amount');

                    $paidAmount = FeeCollection::where('student_id', $student->id)
                        ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
                        ->sum('paid_amount');

                    $discountAmount = FeeCollection::where('student_id', $student->id)
                        ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
                        ->sum('discount_amount');

                    $outstanding = max(0, $totalFee - $paidAmount - $discountAmount);

                    if ($outstanding > 0 || !$request->boolean('hide_paid')) {
                        $outstandingData[] = [
                            'student' => $student,
                            'total_fee' => $totalFee,
                            'paid_amount' => $paidAmount,
                            'discount' => $discountAmount,
                            'outstanding' => $outstanding,
                        ];
                        $totalOutstanding += $outstanding;
                    }
                }

                usort($outstandingData, fn($a, $b) => $b['outstanding'] <=> $a['outstanding']);

                $pdf = Pdf::loadView('admin.fees.reports.pdf.outstanding', compact('outstandingData', 'totalOutstanding', 'activeYear'));
                $pdf->setPaper('a4', 'portrait');
                return $pdf->download("outstanding_fees_" . now()->format('Y-m-d') . ".pdf");

            case 'daily':
                $date = $request->get('date', now()->format('Y-m-d'));

                $collections = FeeCollection::with(['student.schoolClass', 'feeStructure.feeType', 'collectedBy'])
                    ->whereDate('payment_date', $date)
                    ->orderBy('created_at', 'desc')
                    ->get();

                $summary = [
                    'total' => $collections->sum('paid_amount'),
                    'cash' => $collections->where('payment_mode', 'cash')->sum('paid_amount'),
                    'online' => $collections->where('payment_mode', 'online')->sum('paid_amount'),
                    'cheque' => $collections->where('payment_mode', 'cheque')->sum('paid_amount'),
                    'card' => $collections->where('payment_mode', 'card')->sum('paid_amount'),
                    'bank_transfer' => $collections->where('payment_mode', 'bank_transfer')->sum('paid_amount'),
                    'count' => $collections->count(),
                ];

                $pdf = Pdf::loadView('admin.fees.reports.pdf.daily', compact('collections', 'summary', 'date'));
                $pdf->setPaper('a4', 'landscape');
                return $pdf->download("daily_collection_{$date}.pdf");

            default:
                return back()->with('error', 'Invalid export type');
        }
    }
}
