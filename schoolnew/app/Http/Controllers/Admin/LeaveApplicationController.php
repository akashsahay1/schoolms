<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\LeaveStatusNotification;
use App\Models\LeaveApplication;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class LeaveApplicationController extends Controller
{
    /**
     * Display a listing of leave applications.
     */
    public function index(Request $request)
    {
        $query = LeaveApplication::with(['student.schoolClass', 'student.section', 'appliedByUser', 'approvedByUser']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by class
        if ($request->filled('class_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        // Filter by leave type
        if ($request->filled('leave_type')) {
            $query->where('leave_type', $request->leave_type);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->where('from_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->where('to_date', '<=', $request->to_date);
        }

        $leaves = $query->latest()->paginate(15);

        // Get filter options
        $classes = SchoolClass::where('is_active', true)->orderBy('order')->get();
        $leaveTypes = LeaveApplication::LEAVE_TYPES;
        $statuses = LeaveApplication::STATUSES;

        // Get counts for stats
        $pendingCount = LeaveApplication::pending()->count();
        $approvedCount = LeaveApplication::approved()->count();
        $rejectedCount = LeaveApplication::rejected()->count();
        $totalCount = LeaveApplication::count();

        return view('admin.leaves.index', compact(
            'leaves',
            'classes',
            'leaveTypes',
            'statuses',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'totalCount'
        ));
    }

    /**
     * Display the specified leave application.
     */
    public function show(LeaveApplication $leave)
    {
        $leave->load(['student.schoolClass', 'student.section', 'student.user', 'appliedByUser', 'approvedByUser']);

        return view('admin.leaves.show', compact('leave'));
    }

    /**
     * Approve the leave application.
     */
    public function approve(Request $request, LeaveApplication $leave)
    {
        if ($leave->status !== 'pending') {
            return back()->with('error', 'Only pending applications can be approved.');
        }

        $validated = $request->validate([
            'admin_remarks' => 'nullable|string|max:500',
        ]);

        $leave->update([
            'status' => 'approved',
            'admin_remarks' => $validated['admin_remarks'] ?? null,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        // Send email notification
        $this->sendStatusNotification($leave, 'approved');

        return redirect()->route('admin.leaves.index')
            ->with('success', 'Leave application approved successfully. Email notification sent.');
    }

    /**
     * Reject the leave application.
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

        // Send email notification
        $this->sendStatusNotification($leave, 'rejected');

        return redirect()->route('admin.leaves.index')
            ->with('success', 'Leave application rejected. Email notification sent.');
    }

    /**
     * Send email notification to student about leave status.
     */
    private function sendStatusNotification(LeaveApplication $leave, string $status): void
    {
        // Reload the leave with relationships
        $leave->load(['student.user', 'approvedByUser']);

        // Get student's email
        $studentEmail = $leave->student?->user?->email;

        if ($studentEmail) {
            try {
                Mail::to($studentEmail)->send(new LeaveStatusNotification($leave, $status));
            } catch (\Exception $e) {
                // Log the error but don't fail the operation
                \Log::error('Failed to send leave notification email: ' . $e->getMessage());
            }
        }
    }

    /**
     * Bulk approve leave applications.
     */
    public function bulkApprove(Request $request)
    {
        $leaveIds = json_decode($request->leave_ids, true);

        if (empty($leaveIds) || !is_array($leaveIds)) {
            return back()->with('error', 'No leave applications selected.');
        }

        $count = LeaveApplication::whereIn('id', $leaveIds)
            ->where('status', 'pending')
            ->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

        return back()->with('success', "{$count} leave application(s) approved successfully.");
    }

    /**
     * Bulk reject leave applications.
     */
    public function bulkReject(Request $request)
    {
        $leaveIds = json_decode($request->leave_ids, true);

        if (empty($leaveIds) || !is_array($leaveIds)) {
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
}
