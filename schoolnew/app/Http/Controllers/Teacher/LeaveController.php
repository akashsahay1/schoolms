<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\StaffLeave;
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

        $leaves = StaffLeave::where('staff_id', $staff->id)
            ->with('leaveType')
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

        $leaveTypes = LeaveType::where('is_active', true)->get();

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
            'leave_type_id' => 'required|exists:leave_types,id',
            'from_date' => 'required|date|after_or_equal:today',
            'to_date' => 'required|date|after_or_equal:from_date',
            'reason' => 'required|string|max:1000',
            'attachment' => 'nullable|file|max:5120',
        ]);

        $validated['staff_id'] = $staff->id;
        $validated['status'] = 'pending';
        $validated['applied_at'] = now();

        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment')->store('leave-attachments', 'public');
        }

        StaffLeave::create($validated);

        return redirect()->route('teacher.leaves.index')
            ->with('success', 'Leave application submitted successfully.');
    }

    /**
     * View a leave application.
     */
    public function show(StaffLeave $leave)
    {
        $staff = $this->getStaff();
        if (!$staff || $leave->staff_id !== $staff->id) {
            return redirect()->route('teacher.leaves.index')->with('error', 'Unauthorized access.');
        }

        return view('teacher.leaves.show', compact('staff', 'leave'));
    }

    /**
     * Cancel a pending leave application.
     */
    public function cancel(StaffLeave $leave)
    {
        $staff = $this->getStaff();
        if (!$staff || $leave->staff_id !== $staff->id) {
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

        $balances = StaffLeaveBalance::where('staff_id', $staff->id)
            ->with('leaveType')
            ->get();

        $leaveTypes = LeaveType::where('is_active', true)->get();

        // Calculate used leaves
        $usedLeaves = StaffLeave::where('staff_id', $staff->id)
            ->where('status', 'approved')
            ->whereYear('from_date', now()->year)
            ->get()
            ->groupBy('leave_type_id')
            ->map(function ($leaves) {
                return $leaves->sum(function ($leave) {
                    return $leave->from_date->diffInDays($leave->to_date) + 1;
                });
            });

        return view('teacher.leaves.balance', compact('staff', 'balances', 'leaveTypes', 'usedLeaves'));
    }
}
