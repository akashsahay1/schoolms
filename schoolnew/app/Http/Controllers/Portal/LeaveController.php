<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\LeaveApplication;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LeaveController extends Controller
{
    /**
     * Display list of leave applications.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return redirect()->route('portal.dashboard');
        }

        $query = LeaveApplication::forStudent($student->id)
            ->with('approvedByUser');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $leaves = $query->latest()->paginate(10);

        return view('portal.leaves.index', compact('student', 'leaves'));
    }

    /**
     * Show the form for creating a new leave application.
     */
    public function create()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)
            ->with(['schoolClass', 'section'])
            ->first();

        if (!$student) {
            return redirect()->route('portal.dashboard');
        }

        $leaveTypes = LeaveApplication::LEAVE_TYPES;

        return view('portal.leaves.create', compact('student', 'leaveTypes'));
    }

    /**
     * Store a newly created leave application.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return redirect()->route('portal.dashboard');
        }

        $validated = $request->validate([
            'leave_type' => 'required|in:' . implode(',', array_keys(LeaveApplication::LEAVE_TYPES)),
            'from_date' => 'required|date|after_or_equal:today',
            'to_date' => 'required|date|after_or_equal:from_date',
            'reason' => 'required|string|max:1000',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Calculate total days
        $fromDate = \Carbon\Carbon::parse($validated['from_date']);
        $toDate = \Carbon\Carbon::parse($validated['to_date']);
        $totalDays = $fromDate->diffInDays($toDate) + 1;

        // Handle attachment
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('leave-attachments', 'public');
        }

        // Get current academic year
        $academicYear = AcademicYear::where('is_active', true)->first();

        LeaveApplication::create([
            'applicant_type' => Student::class,
            'applicant_id' => $student->id,
            'student_id' => $student->id,
            'applied_by' => $user->id,
            'leave_type' => $validated['leave_type'],
            'from_date' => $validated['from_date'],
            'to_date' => $validated['to_date'],
            'total_days' => $totalDays,
            'reason' => $validated['reason'],
            'attachment' => $attachmentPath,
            'status' => 'pending',
            'academic_year_id' => $academicYear?->id,
        ]);

        return redirect()->route('portal.leaves.index')
            ->with('success', 'Leave application submitted successfully.');
    }

    /**
     * Display a leave application.
     */
    public function show(LeaveApplication $leave)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        // Security check
        if (!$student || $leave->student_id !== $student->id) {
            abort(403, 'Unauthorized access');
        }

        $leave->load('approvedByUser');

        return view('portal.leaves.show', compact('student', 'leave'));
    }

    /**
     * Cancel a pending leave application.
     */
    public function cancel(LeaveApplication $leave)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        // Security check
        if (!$student || $leave->student_id !== $student->id) {
            abort(403, 'Unauthorized access');
        }

        if (!$leave->canBeModified()) {
            return back()->with('error', 'This leave application cannot be cancelled.');
        }

        $leave->update(['status' => 'cancelled']);

        return redirect()->route('portal.leaves.index')
            ->with('success', 'Leave application cancelled successfully.');
    }
}
