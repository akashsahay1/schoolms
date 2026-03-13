<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\StaffAttendance;
use App\Models\Staff;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Student Reports Index
     */
    public function students(Request $request)
    {
        $classes = SchoolClass::where('is_active', true)->orderBy('name')->get();
        $sections = Section::where('is_active', true)->orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        $query = Student::with(['schoolClass', 'section', 'academicYear', 'parent']);

        // Filters
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('admission_no', 'like', "%{$search}%");
            });
        }

        // Summary statistics
        $stats = [
            'total' => Student::count(),
            'active' => Student::where('status', 'active')->count(),
            'male' => Student::where('gender', 'male')->count(),
            'female' => Student::where('gender', 'female')->count(),
        ];

        // Class-wise distribution
        $classWiseStats = Student::select('class_id', DB::raw('count(*) as count'))
            ->where('status', 'active')
            ->groupBy('class_id')
            ->with('schoolClass')
            ->get();

        $students = $query->orderBy('first_name')->paginate(25);

        return view('admin.reports.students', compact('students', 'classes', 'sections', 'academicYears', 'stats', 'classWiseStats'));
    }

    /**
     * Export Students Report
     */
    public function exportStudents(Request $request)
    {
        $query = Student::with(['schoolClass', 'section', 'academicYear', 'parent']);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $students = $query->orderBy('first_name')->get();

        $filename = 'students_report_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($students) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'Admission No',
                'Name',
                'Gender',
                'Date of Birth',
                'Class',
                'Section',
                'Email',
                'Phone',
                'Status',
                'Admission Date'
            ]);

            foreach ($students as $student) {
                fputcsv($file, [
                    $student->admission_no,
                    $student->full_name,
                    ucfirst($student->gender ?? ''),
                    $student->date_of_birth?->format('Y-m-d'),
                    $student->schoolClass->name ?? '',
                    $student->section->name ?? '',
                    $student->email,
                    $student->phone,
                    ucfirst($student->status),
                    $student->admission_date?->format('Y-m-d')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Attendance Reports Index
     */
    public function attendance(Request $request)
    {
        $classes = SchoolClass::where('is_active', true)->orderBy('name')->get();
        $sections = Section::where('is_active', true)->orderBy('name')->get();

        $startDate = $request->start_date ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? now()->format('Y-m-d');

        $stats = [];
        $attendanceData = [];
        $students = collect();

        if ($request->filled('class_id')) {
            $query = Student::where('class_id', $request->class_id)
                ->where('status', 'active');

            if ($request->filled('section_id')) {
                $query->where('section_id', $request->section_id);
            }

            $students = $query->orderBy('first_name')->get();

            // Get attendance records for the period
            foreach ($students as $student) {
                $attendance = Attendance::where('student_id', $student->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->get();

                $totalDays = $attendance->count();
                $present = $attendance->where('status', 'present')->count();
                $absent = $attendance->where('status', 'absent')->count();
                $late = $attendance->where('status', 'late')->count();
                $percentage = $totalDays > 0 ? round(($present / $totalDays) * 100, 1) : 0;

                $attendanceData[$student->id] = [
                    'student' => $student,
                    'total_days' => $totalDays,
                    'present' => $present,
                    'absent' => $absent,
                    'late' => $late,
                    'percentage' => $percentage,
                ];
            }

            // Calculate overall statistics
            $totalPresent = collect($attendanceData)->sum('present');
            $totalAbsent = collect($attendanceData)->sum('absent');
            $totalLate = collect($attendanceData)->sum('late');
            $totalRecords = collect($attendanceData)->sum('total_days');

            $stats = [
                'total_students' => $students->count(),
                'total_days' => $totalRecords > 0 ? round($totalRecords / max($students->count(), 1)) : 0,
                'avg_attendance' => $totalRecords > 0 ? round(($totalPresent / $totalRecords) * 100, 1) : 0,
                'total_present' => $totalPresent,
                'total_absent' => $totalAbsent,
                'total_late' => $totalLate,
            ];
        }

        return view('admin.reports.attendance', compact('classes', 'sections', 'startDate', 'endDate', 'stats', 'attendanceData', 'students'));
    }

    /**
     * Export Attendance Report
     */
    public function exportAttendance(Request $request)
    {
        $request->validate([
            'class_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $query = Student::where('class_id', $request->class_id)
            ->where('status', 'active');

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        $students = $query->with(['schoolClass', 'section'])->orderBy('first_name')->get();

        $filename = 'attendance_report_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($students, $request) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Admission No',
                'Student Name',
                'Class',
                'Section',
                'Total Days',
                'Present',
                'Absent',
                'Late',
                'Attendance %'
            ]);

            foreach ($students as $student) {
                $attendance = Attendance::where('student_id', $student->id)
                    ->whereBetween('date', [$request->start_date, $request->end_date])
                    ->get();

                $totalDays = $attendance->count();
                $present = $attendance->where('status', 'present')->count();
                $absent = $attendance->where('status', 'absent')->count();
                $late = $attendance->where('status', 'late')->count();
                $percentage = $totalDays > 0 ? round(($present / $totalDays) * 100, 1) : 0;

                fputcsv($file, [
                    $student->admission_no,
                    $student->full_name,
                    $student->schoolClass->name ?? '',
                    $student->section->name ?? '',
                    $totalDays,
                    $present,
                    $absent,
                    $late,
                    $percentage . '%'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Exam Reports Index
     */
    public function exams(Request $request)
    {
        $classes = SchoolClass::where('is_active', true)->orderBy('name')->get();
        $sections = Section::where('is_active', true)->orderBy('name')->get();
        $exams = Exam::orderBy('start_date', 'desc')->get();

        $examResults = collect();
        $stats = [];
        $gradeDistribution = [];

        if ($request->filled('exam_id') && $request->filled('class_id')) {
            $exam = Exam::find($request->exam_id);

            $query = Student::where('class_id', $request->class_id)
                ->where('status', 'active');

            if ($request->filled('section_id')) {
                $query->where('section_id', $request->section_id);
            }

            $students = $query->orderBy('first_name')->get();

            foreach ($students as $student) {
                $results = ExamResult::where('exam_id', $request->exam_id)
                    ->where('student_id', $student->id)
                    ->with('subject')
                    ->get();

                if ($results->isNotEmpty()) {
                    $totalMarks = $results->sum('marks_obtained');
                    $maxMarks = $results->sum('max_marks');
                    $percentage = $maxMarks > 0 ? round(($totalMarks / $maxMarks) * 100, 1) : 0;

                    $examResults->push([
                        'student' => $student,
                        'results' => $results,
                        'total_marks' => $totalMarks,
                        'max_marks' => $maxMarks,
                        'percentage' => $percentage,
                        'grade' => $this->calculateGrade($percentage),
                        'rank' => null, // Will be calculated later
                    ]);
                }
            }

            // Sort by percentage and assign ranks
            $examResults = $examResults->sortByDesc('percentage')->values();
            foreach ($examResults as $index => $result) {
                $examResults[$index]['rank'] = $index + 1;
            }

            // Calculate statistics
            if ($examResults->isNotEmpty()) {
                $stats = [
                    'total_students' => $examResults->count(),
                    'highest' => $examResults->max('percentage'),
                    'lowest' => $examResults->min('percentage'),
                    'average' => round($examResults->avg('percentage'), 1),
                    'pass_count' => $examResults->where('percentage', '>=', 33)->count(),
                    'fail_count' => $examResults->where('percentage', '<', 33)->count(),
                ];

                // Grade distribution
                $gradeDistribution = $examResults->groupBy('grade')->map->count();
            }
        }

        return view('admin.reports.exams', compact('classes', 'sections', 'exams', 'examResults', 'stats', 'gradeDistribution'));
    }

    /**
     * Export Exam Report
     */
    public function exportExams(Request $request)
    {
        $request->validate([
            'exam_id' => 'required',
            'class_id' => 'required',
        ]);

        $exam = Exam::find($request->exam_id);

        $query = Student::where('class_id', $request->class_id)
            ->where('status', 'active');

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        $students = $query->with(['schoolClass', 'section'])->orderBy('first_name')->get();

        $filename = 'exam_report_' . ($exam->name ?? 'exam') . '_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($students, $request, $exam) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Rank',
                'Admission No',
                'Student Name',
                'Class',
                'Section',
                'Total Marks',
                'Max Marks',
                'Percentage',
                'Grade'
            ]);

            $results = collect();
            foreach ($students as $student) {
                $examResults = ExamResult::where('exam_id', $request->exam_id)
                    ->where('student_id', $student->id)
                    ->get();

                if ($examResults->isNotEmpty()) {
                    $totalMarks = $examResults->sum('marks_obtained');
                    $maxMarks = $examResults->sum('max_marks');
                    $percentage = $maxMarks > 0 ? round(($totalMarks / $maxMarks) * 100, 1) : 0;

                    $results->push([
                        'student' => $student,
                        'total_marks' => $totalMarks,
                        'max_marks' => $maxMarks,
                        'percentage' => $percentage,
                        'grade' => $this->calculateGrade($percentage),
                    ]);
                }
            }

            // Sort and rank
            $results = $results->sortByDesc('percentage')->values();

            foreach ($results as $index => $result) {
                fputcsv($file, [
                    $index + 1,
                    $result['student']->admission_no,
                    $result['student']->full_name,
                    $result['student']->schoolClass->name ?? '',
                    $result['student']->section->name ?? '',
                    $result['total_marks'],
                    $result['max_marks'],
                    $result['percentage'] . '%',
                    $result['grade']
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Fees Reports - redirect to existing fee reports
     */
    public function fees()
    {
        return redirect()->route('admin.fees.reports.index');
    }

    /**
     * Calculate grade based on percentage
     */
    private function calculateGrade(float $percentage): string
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C+';
        if ($percentage >= 40) return 'C';
        if ($percentage >= 33) return 'D';
        return 'F';
    }
}
