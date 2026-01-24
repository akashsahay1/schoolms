<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\LeaveApplication;
use App\Models\LeaveType;
use App\Models\Staff;
use App\Models\StaffLeaveBalance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class StaffLeaveController extends Controller
{
    /**
     * Display staff leave applications
     */
    public function index(Request $request)
    {
        $query = LeaveApplication::with(['appliedByUser', 'approvedByUser'])
            ->where('applicant_type', 'App\\Models\\Staff')
            ->orWhere(function ($q) {
                $q->whereNull('student_id')
                    ->where('applicant_type', 'App\\Models\\User');
            });

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('leave_type')) {
            $query->where('leave_type', $request->leave_type);
        }

        if ($request->filled('department')) {
            $query->whereHas('appliedByUser.staff', function ($q) use ($request) {
                $q->where('department_id', $request->department);
            });
        }

        if ($request->filled('from_date')) {
            $query->where('from_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->where('to_date', '<=', $request->to_date);
        }

        $leaves = $query->latest()->paginate(15);

        $leaveTypes = LeaveType::active()->forStaff()->orderBy('name')->get();
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $statuses = LeaveApplication::STATUSES;

        // Stats
        $pendingCount = LeaveApplication::where('applicant_type', 'App\\Models\\Staff')
            ->orWhere(function ($q) {
                $q->whereNull('student_id')->where('applicant_type', 'App\\Models\\User');
            })->pending()->count();
        $approvedCount = LeaveApplication::where('applicant_type', 'App\\Models\\Staff')
            ->orWhere(function ($q) {
                $q->whereNull('student_id')->where('applicant_type', 'App\\Models\\User');
            })->approved()->count();
        $rejectedCount = LeaveApplication::where('applicant_type', 'App\\Models\\Staff')
            ->orWhere(function ($q) {
                $q->whereNull('student_id')->where('applicant_type', 'App\\Models\\User');
            })->rejected()->count();

        return view('admin.staff-leaves.index', compact(
            'leaves',
            'leaveTypes',
            'departments',
            'statuses',
            'pendingCount',
            'approvedCount',
            'rejectedCount'
        ));
    }

    /**
     * Show create leave form
     */
    public function create()
    {
        $staff = Staff::with('user', 'department', 'designation')
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get();
        $leaveTypes = LeaveType::active()->forStaff()->orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        return view('admin.staff-leaves.create', compact('staff', 'leaveTypes', 'academicYears', 'currentAcademicYear'));
    }

    /**
     * Store a new leave application
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'staff_id' => ['required', 'exists:staff,id'],
            'leave_type' => ['required', 'string', 'max:50'],
            'from_date' => ['required', 'date', 'after_or_equal:today'],
            'to_date' => ['required', 'date', 'after_or_equal:from_date'],
            'reason' => ['required', 'string', 'max:1000'],
            'attachment' => ['nullable', 'file', 'max:5120', 'mimes:pdf,jpg,jpeg,png'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
        ]);

        try {
            $staff = Staff::findOrFail($validated['staff_id']);
            $fromDate = \Carbon\Carbon::parse($validated['from_date']);
            $toDate = \Carbon\Carbon::parse($validated['to_date']);
            $totalDays = $fromDate->diffInDays($toDate) + 1;

            // Check leave balance
            $leaveType = LeaveType::where('code', $validated['leave_type'])
                ->orWhere('name', $validated['leave_type'])
                ->first();

            if ($leaveType) {
                $balance = StaffLeaveBalance::where('staff_id', $staff->id)
                    ->where('leave_type_id', $leaveType->id)
                    ->where('academic_year_id', $validated['academic_year_id'])
                    ->first();

                if ($balance && !$balance->canTakeLeave($totalDays)) {
                    return back()->with('error', 'Insufficient leave balance. Available: ' . $balance->remaining_days . ' days')->withInput();
                }
            }

            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('leave-attachments', 'public');
            }

            LeaveApplication::create([
                'applicant_type' => 'App\\Models\\Staff',
                'applicant_id' => $staff->id,
                'student_id' => null,
                'applied_by' => Auth::id(),
                'leave_type' => $validated['leave_type'],
                'from_date' => $validated['from_date'],
                'to_date' => $validated['to_date'],
                'total_days' => $totalDays,
                'reason' => $validated['reason'],
                'attachment' => $attachmentPath,
                'status' => 'pending',
                'academic_year_id' => $validated['academic_year_id'],
            ]);

            return redirect()->route('admin.staff-leaves.index')->with('success', 'Leave application submitted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show leave application details
     */
    public function show(LeaveApplication $leave)
    {
        $leave->load(['appliedByUser', 'approvedByUser', 'academicYear']);

        // Get staff info
        if ($leave->applicant_type === 'App\\Models\\Staff') {
            $staff = Staff::with('department', 'designation')->find($leave->applicant_id);
        } else {
            $staff = Staff::with('department', 'designation')->where('user_id', $leave->applied_by)->first();
        }

        return view('admin.staff-leaves.show', compact('leave', 'staff'));
    }

    /**
     * Approve leave application
     */
    public function approve(Request $request, LeaveApplication $leave)
    {
        if ($leave->status !== 'pending') {
            return back()->with('error', 'Only pending applications can be approved.');
        }

        $validated = $request->validate([
            'admin_remarks' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $leave->update([
                'status' => 'approved',
                'admin_remarks' => $validated['admin_remarks'] ?? null,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            // Deduct from leave balance
            $this->deductLeaveBalance($leave);

            DB::commit();

            return redirect()->route('admin.staff-leaves.index')
                ->with('success', 'Leave application approved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Reject leave application
     */
    public function reject(Request $request, LeaveApplication $leave)
    {
        if ($leave->status !== 'pending') {
            return back()->with('error', 'Only pending applications can be rejected.');
        }

        $validated = $request->validate([
            'admin_remarks' => 'required|string|max:500',
        ]);

        $leave->update([
            'status' => 'rejected',
            'admin_remarks' => $validated['admin_remarks'],
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('admin.staff-leaves.index')
            ->with('success', 'Leave application rejected.');
    }

    /**
     * Cancel leave application
     */
    public function cancel(Request $request, LeaveApplication $leave)
    {
        if ($leave->status === 'cancelled') {
            return back()->with('error', 'Leave is already cancelled.');
        }

        try {
            DB::beginTransaction();

            $wasApproved = $leave->status === 'approved';

            $leave->update([
                'status' => 'cancelled',
                'admin_remarks' => ($leave->admin_remarks ?? '') . "\n[Cancelled on " . now()->format('M d, Y') . "]",
            ]);

            // Restore leave balance if was approved
            if ($wasApproved) {
                $this->restoreLeaveBalance($leave);
            }

            DB::commit();

            return redirect()->route('admin.staff-leaves.index')
                ->with('success', 'Leave application cancelled successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Bulk approve leaves
     */
    public function bulkApprove(Request $request)
    {
        $leaveIds = json_decode($request->leave_ids, true);

        if (empty($leaveIds)) {
            return back()->with('error', 'No leave applications selected.');
        }

        try {
            DB::beginTransaction();

            $leaves = LeaveApplication::whereIn('id', $leaveIds)->where('status', 'pending')->get();

            foreach ($leaves as $leave) {
                $leave->update([
                    'status' => 'approved',
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                ]);
                $this->deductLeaveBalance($leave);
            }

            DB::commit();

            return back()->with('success', count($leaves) . ' leave application(s) approved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Bulk reject leaves
     */
    public function bulkReject(Request $request)
    {
        $leaveIds = json_decode($request->leave_ids, true);

        if (empty($leaveIds)) {
            return back()->with('error', 'No leave applications selected.');
        }

        $validated = $request->validate([
            'admin_remarks' => 'required|string|max:500',
        ]);

        $count = LeaveApplication::whereIn('id', $leaveIds)
            ->where('status', 'pending')
            ->update([
                'status' => 'rejected',
                'admin_remarks' => $validated['admin_remarks'],
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

        return back()->with('success', "{$count} leave application(s) rejected.");
    }

    /**
     * Leave balances
     */
    public function balances(Request $request)
    {
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        $selectedYear = $request->academic_year_id ?? $currentAcademicYear?->id;
        $selectedDepartment = $request->department_id;

        $query = Staff::with(['user', 'department', 'designation', 'leaveBalances' => function ($q) use ($selectedYear) {
            $q->with('leaveType')->where('academic_year_id', $selectedYear);
        }])->where('status', 'active');

        if ($selectedDepartment) {
            $query->where('department_id', $selectedDepartment);
        }

        $staffList = $query->orderBy('first_name')->paginate(15);
        $leaveTypes = LeaveType::active()->forStaff()->orderBy('name')->get();

        return view('admin.staff-leaves.balances', compact(
            'staffList',
            'leaveTypes',
            'academicYears',
            'departments',
            'selectedYear',
            'selectedDepartment',
            'currentAcademicYear'
        ));
    }

    /**
     * Show allocation form
     */
    public function allocate()
    {
        $staff = Staff::with('user', 'department')
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get();
        $leaveTypes = LeaveType::active()->forStaff()->orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        return view('admin.staff-leaves.allocate', compact('staff', 'leaveTypes', 'academicYears', 'currentAcademicYear'));
    }

    /**
     * Store leave allocation
     */
    public function storeAllocation(Request $request)
    {
        $validated = $request->validate([
            'staff_ids' => ['required', 'array'],
            'staff_ids.*' => ['exists:staff,id'],
            'leave_type_id' => ['required', 'exists:leave_types,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'allocated_days' => ['required', 'integer', 'min:0', 'max:365'],
            'carried_forward' => ['nullable', 'integer', 'min:0', 'max:365'],
        ]);

        try {
            DB::beginTransaction();

            foreach ($validated['staff_ids'] as $staffId) {
                StaffLeaveBalance::updateOrCreate(
                    [
                        'staff_id' => $staffId,
                        'leave_type_id' => $validated['leave_type_id'],
                        'academic_year_id' => $validated['academic_year_id'],
                    ],
                    [
                        'allocated_days' => $validated['allocated_days'],
                        'carried_forward' => $validated['carried_forward'] ?? 0,
                    ]
                );
            }

            DB::commit();

            return redirect()->route('admin.staff-leaves.balances')
                ->with('success', 'Leave balance allocated to ' . count($validated['staff_ids']) . ' staff member(s).');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Leave reports
     */
    public function reports(Request $request)
    {
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $leaveTypes = LeaveType::active()->forStaff()->orderBy('name')->get();

        $selectedYear = $request->academic_year_id ?? $currentAcademicYear?->id;

        // Leave summary by type
        $leaveByType = LeaveApplication::where('applicant_type', 'App\\Models\\Staff')
            ->where('status', 'approved')
            ->where('academic_year_id', $selectedYear)
            ->selectRaw('leave_type, COUNT(*) as count, SUM(total_days) as total_days')
            ->groupBy('leave_type')
            ->get();

        // Leave summary by department
        $leaveByDepartment = Staff::with(['department'])
            ->join('leave_applications', function ($join) use ($selectedYear) {
                $join->on('staff.id', '=', 'leave_applications.applicant_id')
                    ->where('leave_applications.applicant_type', '=', 'App\\Models\\Staff')
                    ->where('leave_applications.status', '=', 'approved')
                    ->where('leave_applications.academic_year_id', '=', $selectedYear);
            })
            ->selectRaw('staff.department_id, COUNT(*) as count, SUM(leave_applications.total_days) as total_days')
            ->groupBy('staff.department_id')
            ->get();

        return view('admin.staff-leaves.reports', compact(
            'academicYears',
            'departments',
            'leaveTypes',
            'leaveByType',
            'leaveByDepartment',
            'selectedYear',
            'currentAcademicYear'
        ));
    }

    /**
     * Export report
     */
    public function exportReport(Request $request)
    {
        $selectedYear = $request->academic_year_id;

        $leaves = LeaveApplication::with(['appliedByUser'])
            ->where('applicant_type', 'App\\Models\\Staff')
            ->where('academic_year_id', $selectedYear)
            ->where('status', 'approved')
            ->orderBy('from_date')
            ->get();

        $filename = 'staff_leave_report_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($leaves) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['#', 'Staff Name', 'Leave Type', 'From Date', 'To Date', 'Days', 'Status', 'Reason']);

            $count = 1;
            foreach ($leaves as $leave) {
                $staff = Staff::find($leave->applicant_id);
                fputcsv($file, [
                    $count++,
                    $staff ? $staff->first_name . ' ' . $staff->last_name : $leave->appliedByUser->name ?? 'N/A',
                    $leave->leave_type,
                    $leave->from_date->format('Y-m-d'),
                    $leave->to_date->format('Y-m-d'),
                    $leave->total_days,
                    $leave->status,
                    $leave->reason,
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Deduct leave balance
     */
    private function deductLeaveBalance(LeaveApplication $leave)
    {
        if ($leave->applicant_type !== 'App\\Models\\Staff' || !$leave->academic_year_id) {
            return;
        }

        $leaveType = LeaveType::where('code', $leave->leave_type)
            ->orWhere('name', $leave->leave_type)
            ->first();

        if (!$leaveType) {
            return;
        }

        $balance = StaffLeaveBalance::where('staff_id', $leave->applicant_id)
            ->where('leave_type_id', $leaveType->id)
            ->where('academic_year_id', $leave->academic_year_id)
            ->first();

        if ($balance) {
            $balance->deductDays($leave->total_days);
        }
    }

    /**
     * Restore leave balance
     */
    private function restoreLeaveBalance(LeaveApplication $leave)
    {
        if ($leave->applicant_type !== 'App\\Models\\Staff' || !$leave->academic_year_id) {
            return;
        }

        $leaveType = LeaveType::where('code', $leave->leave_type)
            ->orWhere('name', $leave->leave_type)
            ->first();

        if (!$leaveType) {
            return;
        }

        $balance = StaffLeaveBalance::where('staff_id', $leave->applicant_id)
            ->where('leave_type_id', $leaveType->id)
            ->where('academic_year_id', $leave->academic_year_id)
            ->first();

        if ($balance) {
            $balance->addDays($leave->total_days);
        }
    }
}
