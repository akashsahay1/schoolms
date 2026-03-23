<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Services\StudentLifecycleService;
use Illuminate\Http\Request;

class AlumniController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['schoolClass', 'section', 'academicYear'])
            ->whereIn('status', ['graduated', 'transferred', 'expelled']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('admission_no', 'like', "%{$search}%");
            });
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('year')) {
            $query->whereYear('leaving_date', $request->year);
        }

        $alumni = $query->orderBy('leaving_date', 'desc')->paginate(20);

        $classes = SchoolClass::where('is_active', true)->orderBy('order')->get();
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        $stats = [
            'total' => Student::whereIn('status', ['graduated', 'transferred', 'expelled'])->count(),
            'graduated' => Student::where('status', 'graduated')->count(),
            'transferred' => Student::where('status', 'transferred')->count(),
        ];

        $years = Student::whereIn('status', ['graduated', 'transferred', 'expelled'])
            ->whereNotNull('leaving_date')
            ->selectRaw('YEAR(leaving_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        // Check if Class 12 students exist for auto-graduate button
        $class12Ids = SchoolClass::where('name', 'like', 'Class 12%')->pluck('id');
        $class12ActiveCount = Student::where('status', 'active')->whereIn('class_id', $class12Ids)->count();

        return view('admin.alumni.index', compact('alumni', 'classes', 'academicYears', 'stats', 'years', 'class12ActiveCount'));
    }

    /**
     * Auto-graduate Class 12 students who passed.
     */
    public function autoGraduate(Request $request)
    {
        $result = StudentLifecycleService::autoGraduateClass12($request->exam_id);

        return back()->with('success', $result['message']);
    }

    /**
     * Mark a single student as graduated.
     */
    public function markGraduated(Request $request, Student $student)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        if ($student->status !== 'active') {
            return response()->json(['error' => 'Only active students can be marked as graduated.'], 400);
        }

        StudentLifecycleService::markAsGraduated($student, $request->reason);

        if ($request->ajax()) {
            return response()->json(['message' => $student->full_name . ' has been marked as graduated.']);
        }

        return back()->with('success', $student->full_name . ' has been marked as graduated.');
    }

    /**
     * Mark a single student as transferred.
     */
    public function markTransferred(Request $request, Student $student)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
            'leaving_date' => 'nullable|date',
        ]);

        if ($student->status !== 'active') {
            return response()->json(['error' => 'Only active students can be transferred.'], 400);
        }

        StudentLifecycleService::markAsTransferred($student, $request->reason, $request->leaving_date);

        if ($request->ajax()) {
            return response()->json(['message' => $student->full_name . ' has been transferred.']);
        }

        return back()->with('success', $student->full_name . ' has been transferred.');
    }
}
