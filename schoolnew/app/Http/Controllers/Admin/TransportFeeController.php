<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\RouteAssignment;
use App\Models\Student;
use App\Models\TransportFee;
use App\Models\TransportFeeCollection;
use App\Models\TransportRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransportFeeCollectionExport;

class TransportFeeController extends Controller
{
    /**
     * Display transport fee structure.
     */
    public function index(Request $request)
    {
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $currentYear = AcademicYear::where('is_active', true)->first();
        $selectedYear = $request->academic_year_id ?? $currentYear?->id;

        // Auto-sync: ensure all active routes have a TransportFee for the active year
        if ($currentYear) {
            $routesWithoutFee = TransportRoute::where('is_active', true)
                ->whereDoesntHave('fees', fn($q) => $q->where('academic_year_id', $currentYear->id)->where('fee_type', 'monthly'))
                ->get();

            foreach ($routesWithoutFee as $route) {
                TransportFee::create([
                    'transport_route_id' => $route->id,
                    'academic_year_id' => $currentYear->id,
                    'fee_type' => 'monthly',
                    'amount' => $route->fare_amount,
                    'fine_per_day' => 0,
                    'fine_grace_days' => 0,
                    'is_active' => true,
                    'description' => 'Monthly transport fee for ' . $route->route_name,
                ]);
            }
        }

        $fees = TransportFee::with(['route', 'academicYear'])
            ->when($selectedYear, fn($q) => $q->where('academic_year_id', $selectedYear))
            ->latest()
            ->paginate(15);

        return view('admin.transport.fees.index', compact('fees', 'academicYears', 'selectedYear'));
    }

    /**
     * Show form for creating transport fee.
     */
    public function create()
    {
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $currentYear = AcademicYear::where('is_active', true)->first();
        $routes = TransportRoute::active()->get();

        return view('admin.transport.fees.create', compact('academicYears', 'currentYear', 'routes'));
    }

    /**
     * Store transport fee.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'transport_route_id' => 'required|exists:transport_routes,id',
            'fee_type' => 'required|in:monthly,quarterly,half_yearly,yearly,one_time',
            'amount' => 'required|numeric|min:0',
            'fine_per_day' => 'nullable|numeric|min:0',
            'fine_grace_days' => 'nullable|integer|min:0',
            'due_date' => 'nullable|date',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['fine_per_day'] = $validated['fine_per_day'] ?? 0;
        $validated['fine_grace_days'] = $validated['fine_grace_days'] ?? 0;

        TransportFee::updateOrCreate(
            [
                'academic_year_id' => $validated['academic_year_id'],
                'transport_route_id' => $validated['transport_route_id'],
                'fee_type' => $validated['fee_type'],
            ],
            [
                'amount' => $validated['amount'],
                'fine_per_day' => $validated['fine_per_day'],
                'fine_grace_days' => $validated['fine_grace_days'],
                'due_date' => $validated['due_date'] ?? null,
                'description' => $validated['description'] ?? null,
                'is_active' => $validated['is_active'],
            ]
        );

        return redirect()->route('admin.transport.fees.index')
            ->with('success', 'Transport fee saved successfully.');
    }

    /**
     * Show form for editing transport fee.
     */
    public function edit(TransportFee $fee)
    {
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $routes = TransportRoute::active()->get();

        return view('admin.transport.fees.edit', compact('fee', 'academicYears', 'routes'));
    }

    /**
     * Update transport fee.
     */
    public function update(Request $request, TransportFee $fee)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'transport_route_id' => 'required|exists:transport_routes,id',
            'fee_type' => 'required|in:monthly,quarterly,half_yearly,yearly,one_time',
            'amount' => 'required|numeric|min:0',
            'fine_per_day' => 'nullable|numeric|min:0',
            'fine_grace_days' => 'nullable|integer|min:0',
            'due_date' => 'nullable|date',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['fine_per_day'] = $validated['fine_per_day'] ?? 0;
        $validated['fine_grace_days'] = $validated['fine_grace_days'] ?? 0;

        $fee->update($validated);

        return redirect()->route('admin.transport.fees.index')
            ->with('success', 'Transport fee updated successfully.');
    }

    /**
     * Delete transport fee.
     */
    public function destroy(TransportFee $fee)
    {
        if ($fee->collections()->exists()) {
            return back()->with('error', 'Cannot delete fee. There are existing collections.');
        }

        $fee->delete();

        return redirect()->route('admin.transport.fees.index')
            ->with('success', 'Transport fee deleted successfully.');
    }

    /**
     * Display fee collection dashboard.
     */
    public function collections(Request $request)
    {
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $currentYear = AcademicYear::where('is_active', true)->first();
        $selectedYear = $request->academic_year_id ?? $currentYear?->id;

        $routes = TransportRoute::active()->get();
        $selectedRoute = $request->route_id;

        $query = TransportFeeCollection::with(['student', 'transportFee.route', 'routeAssignment'])
            ->whereHas('transportFee', function ($q) use ($selectedYear) {
                $q->where('academic_year_id', $selectedYear);
            });

        if ($selectedRoute) {
            $query->whereHas('transportFee', function ($q) use ($selectedRoute) {
                $q->where('transport_route_id', $selectedRoute);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('admission_no', 'like', "%{$search}%");
            });
        }

        $collections = $query->latest()->paginate(20);

        // Statistics
        $stats = [
            'total_due' => TransportFeeCollection::pending()
                ->whereHas('transportFee', fn($q) => $q->where('academic_year_id', $selectedYear))
                ->sum(DB::raw('amount + fine - discount - paid_amount')),
            'total_collected' => TransportFeeCollection::paid()
                ->whereHas('transportFee', fn($q) => $q->where('academic_year_id', $selectedYear))
                ->sum('paid_amount'),
            'pending_count' => TransportFeeCollection::pending()
                ->whereHas('transportFee', fn($q) => $q->where('academic_year_id', $selectedYear))
                ->count(),
        ];

        return view('admin.transport.fees.collections', compact(
            'collections', 'academicYears', 'selectedYear', 'routes', 'selectedRoute', 'stats'
        ));
    }

    /**
     * Show collect fee form for a student.
     */
    public function collectForm(Student $student)
    {
        $currentYear = AcademicYear::where('is_active', true)->first();

        // Get student's route assignment
        $assignment = RouteAssignment::where('student_id', $student->id)
            ->where('academic_year_id', $currentYear?->id)
            ->where('is_active', true)
            ->with('route')
            ->first();

        if (!$assignment) {
            return back()->with('error', 'Student is not assigned to any transport route.');
        }

        // Get transport fee for this route
        $transportFee = TransportFee::where('transport_route_id', $assignment->transport_route_id)
            ->where('academic_year_id', $currentYear?->id)
            ->active()
            ->first();

        if (!$transportFee) {
            return back()->with('error', 'No transport fee defined for this route.');
        }

        // Get existing collections
        $existingCollections = TransportFeeCollection::where('student_id', $student->id)
            ->where('transport_fee_id', $transportFee->id)
            ->get();

        // Generate pending months if monthly fee
        $pendingMonths = [];
        if ($transportFee->fee_type === 'monthly') {
            $startMonth = $currentYear->start_date->format('Y-m');
            $endMonth = now()->format('Y-m');
            $collectedMonths = $existingCollections->pluck('month')->toArray();

            $current = $currentYear->start_date->copy();
            while ($current->format('Y-m') <= $endMonth) {
                $monthKey = $current->format('Y-m');
                if (!in_array($monthKey, $collectedMonths)) {
                    $pendingMonths[$monthKey] = $current->format('F Y');
                }
                $current->addMonth();
            }
        }

        return view('admin.transport.fees.collect', compact(
            'student', 'assignment', 'transportFee', 'existingCollections', 'pendingMonths'
        ));
    }

    /**
     * Process fee collection.
     */
    public function collect(Request $request, Student $student)
    {
        $validated = $request->validate([
            'transport_fee_id' => 'required|exists:transport_fees,id',
            'route_assignment_id' => 'required|exists:route_assignments,id',
            'month' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'fine' => 'nullable|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'payment_mode' => 'required|in:cash,online,cheque,bank_transfer,upi,card',
            'payment_date' => 'required|date',
            'transaction_id' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $transportFee = TransportFee::findOrFail($validated['transport_fee_id']);
        $totalPayable = ($validated['amount'] + ($validated['fine'] ?? 0)) - ($validated['discount'] ?? 0);

        // Determine status
        $status = 'pending';
        if ($validated['paid_amount'] >= $totalPayable) {
            $status = 'paid';
        } elseif ($validated['paid_amount'] > 0) {
            $status = 'partial';
        }

        TransportFeeCollection::create([
            'transport_fee_id' => $validated['transport_fee_id'],
            'student_id' => $student->id,
            'route_assignment_id' => $validated['route_assignment_id'],
            'month' => $validated['month'],
            'amount' => $validated['amount'],
            'discount' => $validated['discount'] ?? 0,
            'fine' => $validated['fine'] ?? 0,
            'paid_amount' => $validated['paid_amount'],
            'payment_date' => $validated['payment_date'],
            'payment_mode' => $validated['payment_mode'],
            'receipt_number' => TransportFeeCollection::generateReceiptNumber(),
            'transaction_id' => $validated['transaction_id'],
            'status' => $status,
            'remarks' => $validated['remarks'],
            'collected_by' => Auth::id(),
        ]);

        return redirect()->route('admin.transport.fees.collections')
            ->with('success', 'Transport fee collected successfully.');
    }

    /**
     * Generate monthly fees for all assigned students.
     */
    public function generateMonthlyFees(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'month' => 'required|string',
        ]);

        $academicYearId = $request->academic_year_id;
        $month = $request->month;

        // Get all active route assignments
        $assignments = RouteAssignment::where('academic_year_id', $academicYearId)
            ->where('is_active', true)
            ->with(['route', 'student'])
            ->get();

        $created = 0;
        $skipped = 0;

        foreach ($assignments as $assignment) {
            // Get transport fee for this route
            $transportFee = TransportFee::where('transport_route_id', $assignment->transport_route_id)
                ->where('academic_year_id', $academicYearId)
                ->where('fee_type', 'monthly')
                ->active()
                ->first();

            if (!$transportFee) {
                $skipped++;
                continue;
            }

            // Check if already exists
            $exists = TransportFeeCollection::where('student_id', $assignment->student_id)
                ->where('transport_fee_id', $transportFee->id)
                ->where('month', $month)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            // Create collection entry
            TransportFeeCollection::create([
                'transport_fee_id' => $transportFee->id,
                'student_id' => $assignment->student_id,
                'route_assignment_id' => $assignment->id,
                'month' => $month,
                'amount' => $transportFee->amount,
                'discount' => 0,
                'fine' => 0,
                'paid_amount' => 0,
                'status' => 'pending',
            ]);

            $created++;
        }

        return back()->with('success', "Generated {$created} fee entries. Skipped {$skipped} (already exists or no fee defined).");
    }

    /**
     * Transport fee reports.
     */
    public function reports(Request $request)
    {
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $currentYear = AcademicYear::where('is_active', true)->first();
        $selectedYear = $request->academic_year_id ?? $currentYear?->id;
        $routes = TransportRoute::active()->orderBy('route_name')->get();

        // All fee IDs for this academic year
        $allFeeIds = TransportFee::where('academic_year_id', $selectedYear)->pluck('id');

        // Build base collection query with filters
        $baseQuery = TransportFeeCollection::whereIn('transport_fee_id', $allFeeIds);

        if ($request->filled('route_id')) {
            $routeFeeIds = TransportFee::where('academic_year_id', $selectedYear)
                ->where('transport_route_id', $request->route_id)
                ->pluck('id');
            $baseQuery->whereIn('transport_fee_id', $routeFeeIds);
        }

        if ($request->filled('month')) {
            $baseQuery->where('month', $request->month);
        }

        // Summary stats
        $stats = [
            'total_students' => (clone $baseQuery)->distinct('student_id')->count('student_id'),
            'total_due' => (clone $baseQuery)->sum(DB::raw('amount + fine - discount')),
            'total_collected' => (clone $baseQuery)->sum('paid_amount'),
            'total_pending' => (clone $baseQuery)->pending()->count(),
            'total_paid' => (clone $baseQuery)->where('status', 'paid')->count(),
            'total_records' => (clone $baseQuery)->count(),
        ];
        $stats['collection_pct'] = $stats['total_due'] > 0
            ? round(($stats['total_collected'] / $stats['total_due']) * 100, 1) : 0;

        // Route-wise collection summary
        $routeSummary = TransportRoute::withCount([
            'assignments' => fn($q) => $q->where('academic_year_id', $selectedYear)->where('is_active', true),
        ])->with(['fees' => fn($q) => $q->where('academic_year_id', $selectedYear)])
            ->get()
            ->map(function ($route) use ($request) {
                $feeIds = $route->fees->pluck('id');
                $collections = TransportFeeCollection::whereIn('transport_fee_id', $feeIds);

                if ($request->filled('month')) {
                    $collections->where('month', $request->month);
                }

                $totalDue = (clone $collections)->sum(DB::raw('amount + fine - discount'));
                $totalCollected = (clone $collections)->sum('paid_amount');

                return [
                    'route' => $route,
                    'students_count' => $route->assignments_count,
                    'fare' => $route->fare_amount,
                    'total_due' => $totalDue,
                    'total_collected' => $totalCollected,
                    'pending_count' => (clone $collections)->pending()->count(),
                    'paid_count' => (clone $collections)->where('status', 'paid')->count(),
                ];
            });

        // Monthly collection trend
        $monthlyTrend = TransportFeeCollection::whereIn('transport_fee_id', $allFeeIds)
            ->where('status', 'paid')
            ->whereNotNull('payment_date')
            ->selectRaw('DATE_FORMAT(payment_date, "%Y-%m") as month, SUM(paid_amount) as total, COUNT(*) as count')
            ->groupByRaw('DATE_FORMAT(payment_date, "%Y-%m")')
            ->orderBy('month')
            ->get();

        // Available months for filter
        $availableMonths = TransportFeeCollection::whereIn('transport_fee_id', $allFeeIds)
            ->distinct()
            ->orderBy('month')
            ->pluck('month');

        return view('admin.transport.fees.reports', compact(
            'academicYears', 'selectedYear', 'routes', 'routeSummary',
            'monthlyTrend', 'stats', 'availableMonths'
        ));
    }

    /**
     * Export collection report.
     */
    public function exportCollections(Request $request)
    {
        $academicYearId = $request->academic_year_id ?? AcademicYear::where('is_active', true)->first()?->id;

        $collections = TransportFeeCollection::with(['student', 'transportFee.route'])
            ->whereHas('transportFee', fn($q) => $q->where('academic_year_id', $academicYearId))
            ->get();

        $filename = 'transport_fee_collections_' . date('Y-m-d') . '.xlsx';
        return Excel::download(new TransportFeeCollectionExport($collections), $filename);
    }
}
