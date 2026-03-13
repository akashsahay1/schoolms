<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\LeaveApplication;
use App\Models\LeaveType;
use App\Models\StaffLeaveBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    protected function getStaff()
    {
        return Staff::where('user_id', Auth::id())->first();
    }

    /**
     * List leave applications.
     */
    public function index()
    {
        $staff = $this->getStaff();
        if (!$staff) {
            return redirect()->route('teacher.dashboard')->with('error', 'Staff profile not found.');
        }

        $leaves = LeaveApplication::where('applicant_type', Staff::class)
            ->where('applicant_id', $staff->id)
            ->latest()
            ->paginate(15);

        return view('teacher.leaves.index', compact('staff', 'leaves'));
    }

    /**
     * Show leave application form.
     */
    public function create()
    {
        $staff = $this->getStaff();
        if (!$staff) {
            return redirect()->route('teacher.dashboard')->with('error', 'Staff profile not found.');
        }

        $leaveTypes = LeaveApplication::LEAVE_TYPES;

        return view('teacher.leaves.create', compact('staff', 'leaveTypes'));
    }

    /**
     * Store leave application.
     */
    public function store(Request $request)
    {
        $staff = $this->getStaff();
        if (!$staff) {
            return redirect()->route('teacher.dashboard')->with('error', 'Staff profile not found.');
        }

        $validated = $request->validate([
            'leave_type' => 'required|string|in:' . implode(',', array_keys(LeaveApplication::LEAVE_TYPES)),
            'from_date' => 'required|date|after_or_equal:today',
            'to_date' => 'required|date|after_or_equal:from_date',
            'reason' => 'required|string|max:1000',
            'attachment' => 'nullable|file|max:5120',
        ]);

        $validated['applicant_type'] = Staff::class;
        $validated['applicant_id'] = $staff->id;
        $validated['applied_by'] = Auth::id();
        $validated['status'] = 'pending';
        $validated['total_days'] = \Carbon\Carbon::parse($validated['from_date'])->diffInDays(\Carbon\Carbon::parse($validated['to_date'])) + 1;

        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment')->store('leave-attachments', 'public');
        }

        LeaveApplication::create($validated);

        return redirect()->route('teacher.leaves.index')
            ->with('success', 'Leave application submitted successfully.');
    }

    /**
     * View a leave application.
     */
    public function show(LeaveApplication $leave)
    {
        $staff = $this->getStaff();
        if (!$staff || $leave->applicant_type !== Staff::class || $leave->applicant_id !== $staff->id) {
            return redirect()->route('teacher.leaves.index')->with('error', 'Unauthorized access.');
        }

        return view('teacher.leaves.show', compact('staff', 'leave'));
    }

    /**
     * Cancel a pending leave application.
     */
    public function cancel(LeaveApplication $leave)
    {
        $staff = $this->getStaff();
        if (!$staff || $leave->applicant_type !== Staff::class || $leave->applicant_id !== $staff->id) {
            return redirect()->route('teacher.leaves.index')->with('error', 'Unauthorized access.');
        }

        if ($leave->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending applications can be cancelled.');
        }

        $leave->update(['status' => 'cancelled']);

        return redirect()->route('teacher.leaves.index')
            ->with('success', 'Leave application cancelled successfully.');
    }

    /**
     * View leave balance.
     */
    public function balance()
    {
        $staff = $this->getStaff();
        if (!$staff) {
            return redirect()->route('teacher.dashboard')->with('error', 'Staff profile not found.');
        }

        $leaveTypes = LeaveType::where('is_active', true)
            ->where(function ($q) {
                $q->whereIn('applicable_to', ['all', 'staff']);
            })
            ->get();

        $balances = StaffLeaveBalance::where('staff_id', $staff->id)
            ->with('leaveType')
            ->get();

        // Calculate used leaves from leave_applications
        $usedLeaves = LeaveApplication::where('applicant_type', Staff::class)
            ->where('applicant_id', $staff->id)
            ->where('status', 'approved')
            ->whereYear('from_date', now()->year)
            ->get()
            ->groupBy('leave_type')
            ->map(function ($leaves) {
                return $leaves->sum('total_days');
            });

        return view('teacher.leaves.balance', compact('staff', 'balances', 'leaveTypes', 'usedLeaves'));
    }
}
