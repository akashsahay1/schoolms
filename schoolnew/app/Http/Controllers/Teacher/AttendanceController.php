<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Timetable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    protected function getStaff()
    {
        return Staff::where('user_id', Auth::id())->first();
    }

    /**
     * Show attendance marking form.
     */
    public function mark(Request $request)
    {
        $staff = $this->getStaff();
        if (!$staff) {
            return redirect()->route('teacher.dashboard')->with('error', 'Staff profile not found.');
        }

        // Get classes this teacher teaches
        $myClassIds = Timetable::where('teacher_id', $staff->id)
            ->pluck('class_id')
            ->unique();

        $classes = SchoolClass::whereIn('id', $myClassIds)->get();

        $students = collect();
        $existingAttendance = collect();
        $selectedClass = null;
        $selectedSection = null;
        $date = $request->date ?? now()->format('Y-m-d');

        if ($request->class_id) {
            $selectedClass = SchoolClass::find($request->class_id);

            $query = Student::where('class_id', $request->class_id)
                ->where('status', 'active');

            if ($request->section_id) {
                $query->where('section_id', $request->section_id);
                $selectedSection = Section::find($request->section_id);
            }

            $students = $query->orderBy('roll_number')->orderBy('first_name')->get();

            // Get existing attendance for this date
            $existingAttendance = Attendance::where('class_id', $request->class_id)
                ->when($request->section_id, function ($q) use ($request) {
                    return $q->where('section_id', $request->section_id);
                })
                ->whereDate('date', $date)
                ->get()
                ->keyBy('student_id');
        }

        return view('teacher.attendance.mark', compact(
            'staff', 'classes', 'students', 'existingAttendance',
            'selectedClass', 'selectedSection', 'date'
        ));
    }

    /**
     * Store attendance.
     */
    public function store(Request $request)
    {
        $staff = $this->getStaff();
        if (!$staff) {
            return redirect()->route('teacher.dashboard')->with('error', 'Staff profile not found.');
        }

        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*' => 'in:present,absent,late,half_day',
        ]);

        foreach ($validated['attendance'] as $studentId => $status) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'class_id' => $validated['class_id'],
                    'section_id' => $validated['section_id'],
                    'date' => $validated['date'],
                ],
                [
                    'status' => $status,
                    'marked_by' => Auth::id(),
                ]
            );
        }

        return redirect()->back()->with('success', 'Attendance marked successfully.');
    }

    /**
     * View attendance reports.
     */
    public function reports(Request $request)
    {
        $staff = $this->getStaff();
        if (!$staff) {
            return redirect()->route('teacher.dashboard')->with('error', 'Staff profile not found.');
        }

        // Get classes this teacher teaches
        $myClassIds = Timetable::where('teacher_id', $staff->id)
            ->pluck('class_id')
            ->unique();

        $classes = SchoolClass::whereIn('id', $myClassIds)->get();

        $attendanceData = collect();
        $selectedClass = null;
        $selectedSection = null;
        $startDate = $request->start_date ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? now()->format('Y-m-d');

        if ($request->class_id) {
            $selectedClass = SchoolClass::find($request->class_id);

            $query = Attendance::where('class_id', $request->class_id)
                ->whereBetween('date', [$startDate, $endDate])
                ->with('student');

            if ($request->section_id) {
                $query->where('section_id', $request->section_id);
                $selectedSection = Section::find($request->section_id);
            }

            $attendanceData = $query->get()
                ->groupBy('student_id')
                ->map(function ($records) {
                    $student = $records->first()->student;
                    return [
                        'student' => $student,
                        'present' => $records->where('status', 'present')->count(),
                        'absent' => $records->where('status', 'absent')->count(),
                        'late' => $records->where('status', 'late')->count(),
                        'half_day' => $records->where('status', 'half_day')->count(),
                        'total' => $records->count(),
                    ];
                });
        }

        return view('teacher.attendance.reports', compact(
            'staff', 'classes', 'attendanceData',
            'selectedClass', 'selectedSection', 'startDate', 'endDate'
        ));
    }

    /**
     * Get sections for a class (AJAX).
     */
    public function getSections($classId)
    {
        $sections = Section::where('class_id', $classId)->get();
        return response()->json($sections);
    }
}
