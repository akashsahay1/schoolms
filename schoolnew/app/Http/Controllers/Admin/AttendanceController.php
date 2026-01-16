<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceSummary;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function mark(Request $request)
    {
        $activeYear = AcademicYear::getActive();
        $classes = SchoolClass::with('sections')->ordered()->get();
        
        $students = collect();
        $attendanceData = collect();
        $selectedClass = null;
        $selectedSection = null;
        $selectedDate = $request->get('date', now()->format('Y-m-d'));

        if ($request->filled('class_id') && $request->filled('section_id')) {
            $selectedClass = SchoolClass::find($request->class_id);
            $selectedSection = Section::find($request->section_id);
            
            // Get students for the selected class and section
            $students = Student::with(['schoolClass', 'section'])
                ->where('class_id', $request->class_id)
                ->where('section_id', $request->section_id)
                ->orderBy('roll_no')
                ->get();

            // Get existing attendance for the selected date
            if ($activeYear) {
                $attendanceData = Attendance::getAttendanceForDate(
                    $request->class_id,
                    $request->section_id,
                    $selectedDate,
                    $activeYear->id
                );
            }
        }

        return view('admin.attendance.mark', compact(
            'classes',
            'students',
            'attendanceData',
            'activeYear',
            'selectedClass',
            'selectedSection',
            'selectedDate'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'required|exists:sections,id',
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*' => 'in:present,absent,late,half_day',
            'check_in_time' => 'nullable|array',
            'check_in_time.*' => 'nullable|date_format:H:i',
            'remarks' => 'nullable|array',
            'remarks.*' => 'nullable|string|max:255',
        ]);

        $activeYear = AcademicYear::getActive();
        
        if (!$activeYear) {
            return redirect()->back()->with('error', 'No active academic year found.');
        }

        try {
            DB::beginTransaction();

            foreach ($validated['attendance'] as $studentId => $status) {
                $attendanceData = [
                    'student_id' => $studentId,
                    'class_id' => $validated['class_id'],
                    'section_id' => $validated['section_id'],
                    'academic_year_id' => $activeYear->id,
                    'attendance_date' => $validated['date'],
                    'status' => $status,
                    'check_in_time' => $validated['check_in_time'][$studentId] ?? null,
                    'remarks' => $validated['remarks'][$studentId] ?? null,
                    'marked_by' => auth()->id(),
                ];

                Attendance::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'attendance_date' => $validated['date'],
                    ],
                    $attendanceData
                );

                // Update monthly summary
                $date = Carbon::parse($validated['date']);
                AttendanceSummary::updateSummary(
                    $studentId,
                    $date->month,
                    $date->year,
                    $activeYear->id
                );
            }

            DB::commit();

            return redirect()->route('admin.attendance.mark', [
                'class_id' => $validated['class_id'],
                'section_id' => $validated['section_id'],
                'date' => $validated['date']
            ])->with('success', 'Attendance marked successfully for ' . count($validated['attendance']) . ' students.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to mark attendance. Please try again.');
        }
    }

    public function reports(Request $request)
    {
        $activeYear = AcademicYear::getActive();
        $classes = SchoolClass::with('sections')->ordered()->get();

        $reportData = collect();
        $selectedClass = null;
        $selectedSection = null;
        $fromDate = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->format('Y-m-d'));

        if ($request->filled('class_id') && $request->filled('from_date') && $request->filled('to_date')) {
            $selectedClass = SchoolClass::with('sections')->find($request->class_id);

            if ($request->filled('section_id')) {
                $selectedSection = Section::find($request->section_id);
            }

            $reportData = $this->getDateRangeReport(
                $request->class_id,
                $request->get('section_id'),
                $fromDate,
                $toDate,
                $activeYear->id ?? null
            );
        }

        return view('admin.attendance.reports', compact(
            'classes',
            'reportData',
            'activeYear',
            'selectedClass',
            'selectedSection',
            'fromDate',
            'toDate'
        ));
    }

    private function getDateRangeReport($classId, $sectionId, $fromDate, $toDate, $academicYearId)
    {
        if (!$academicYearId) return collect();

        // Get all students in the class/section
        $studentsQuery = Student::where('class_id', $classId);
        if ($sectionId) {
            $studentsQuery->where('section_id', $sectionId);
        }
        $students = $studentsQuery->orderBy('roll_no')->get();

        // Get attendance records within date range
        $attendanceRecords = Attendance::where('academic_year_id', $academicYearId)
            ->whereBetween('attendance_date', [$fromDate, $toDate])
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->groupBy('student_id');

        // Aggregate data for each student
        $reportData = collect();
        foreach ($students as $student) {
            $studentAttendance = $attendanceRecords->get($student->id, collect());

            $totalDays = $studentAttendance->count();
            $presentDays = $studentAttendance->where('status', 'present')->count();
            $absentDays = $studentAttendance->where('status', 'absent')->count();
            $lateDays = $studentAttendance->where('status', 'late')->count();
            $halfDays = $studentAttendance->where('status', 'half_day')->count();
            $percentage = $totalDays > 0 ? round((($presentDays + ($halfDays * 0.5) + ($lateDays * 0.75)) / $totalDays) * 100, 1) : 0;

            $reportData->push((object)[
                'student' => $student,
                'total_days' => $totalDays,
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
                'late_days' => $lateDays,
                'half_days' => $halfDays,
                'attendance_percentage' => $percentage,
            ]);
        }

        return $reportData->sortByDesc('attendance_percentage')->values();
    }

    private function getMonthlyReport($classId, $sectionId, $month, $year, $academicYearId)
    {
        if (!$academicYearId) return collect();

        return AttendanceSummary::with('student')
            ->whereHas('student', function($q) use ($classId, $sectionId) {
                $q->where('class_id', $classId);
                if ($sectionId) {
                    $q->where('section_id', $sectionId);
                }
            })
            ->where('academic_year_id', $academicYearId)
            ->where('month', $month)
            ->where('year', $year)
            ->orderBy('attendance_percentage', 'desc')
            ->get();
    }

    private function getDailyReport($classId, $sectionId, $date, $academicYearId)
    {
        if (!$academicYearId) return collect();

        $query = Attendance::with('student')
            ->where('class_id', $classId)
            ->where('attendance_date', $date)
            ->where('academic_year_id', $academicYearId);

        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }

        return $query->orderBy('status')->get();
    }

    private function getYearlyReport($classId, $sectionId, $year, $academicYearId)
    {
        if (!$academicYearId) return collect();

        // Get all students in the class/section
        $studentsQuery = Student::where('class_id', $classId);
        if ($sectionId) {
            $studentsQuery->where('section_id', $sectionId);
        }
        $students = $studentsQuery->orderBy('roll_no')->get();

        // Get monthly summaries for the entire year
        $summaries = AttendanceSummary::where('academic_year_id', $academicYearId)
            ->where('year', $year)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->groupBy('student_id');

        // Aggregate yearly data for each student
        $yearlyData = collect();
        foreach ($students as $student) {
            $studentSummaries = $summaries->get($student->id, collect());

            $totalDays = $studentSummaries->sum('total_days');
            $presentDays = $studentSummaries->sum('present_days');
            $absentDays = $studentSummaries->sum('absent_days');
            $lateDays = $studentSummaries->sum('late_days');
            $halfDays = $studentSummaries->sum('half_days');
            $percentage = $totalDays > 0 ? round((($presentDays + ($halfDays * 0.5)) / $totalDays) * 100, 1) : 0;

            // Get month-wise breakdown
            $monthlyBreakdown = [];
            for ($m = 1; $m <= 12; $m++) {
                $monthSummary = $studentSummaries->firstWhere('month', $m);
                $monthlyBreakdown[$m] = $monthSummary ? [
                    'total' => $monthSummary->total_days,
                    'present' => $monthSummary->present_days,
                    'absent' => $monthSummary->absent_days,
                    'late' => $monthSummary->late_days,
                    'half_day' => $monthSummary->half_days,
                    'percentage' => $monthSummary->attendance_percentage,
                ] : null;
            }

            $yearlyData->push((object)[
                'student' => $student,
                'total_days' => $totalDays,
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
                'late_days' => $lateDays,
                'half_days' => $halfDays,
                'attendance_percentage' => $percentage,
                'monthly_breakdown' => $monthlyBreakdown,
            ]);
        }

        return $yearlyData->sortByDesc('attendance_percentage')->values();
    }

    public function getSections($classId)
    {
        $sections = Section::where('class_id', $classId)->get(['id', 'name']);
        return response()->json($sections);
    }

    public function calendar(Request $request)
    {
        $activeYear = AcademicYear::getActive();
        $classes = SchoolClass::with('sections')->ordered()->get();

        $calendarData = [];
        $selectedClass = null;
        $selectedSection = null;
        $selectedMonth = $request->get('month', now()->month);
        $selectedYear = $request->get('year', now()->year);

        if ($request->filled('class_id') && $request->filled('section_id') && $activeYear) {
            $selectedClass = SchoolClass::find($request->class_id);
            $selectedSection = Section::find($request->section_id);

            // Get first and last day of month
            $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1);
            $endDate = $startDate->copy()->endOfMonth();

            // Get all attendance records for the month
            $attendanceRecords = Attendance::where('class_id', $request->class_id)
                ->where('section_id', $request->section_id)
                ->where('academic_year_id', $activeYear->id)
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->selectRaw('attendance_date, status, COUNT(*) as count')
                ->groupBy('attendance_date', 'status')
                ->get();

            // Get total students in section
            $totalStudents = Student::where('class_id', $request->class_id)
                ->where('section_id', $request->section_id)
                ->where('status', 'active')
                ->count();

            // Organize data by date
            foreach ($attendanceRecords->groupBy('attendance_date') as $date => $records) {
                $calendarData[$date] = [
                    'total' => $totalStudents,
                    'present' => 0,
                    'absent' => 0,
                    'late' => 0,
                    'half_day' => 0,
                    'marked' => true,
                ];

                foreach ($records as $record) {
                    $calendarData[$date][$record->status] = $record->count;
                }
            }
        }

        return view('admin.attendance.calendar', compact(
            'classes',
            'calendarData',
            'activeYear',
            'selectedClass',
            'selectedSection',
            'selectedMonth',
            'selectedYear'
        ));
    }
}