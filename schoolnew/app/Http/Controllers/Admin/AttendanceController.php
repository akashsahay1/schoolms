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
        $selectedMonth = $request->get('month', now()->month);
        $selectedYear = $request->get('year', now()->year);
        $reportType = $request->get('report_type', 'monthly');

        if ($request->filled('class_id')) {
            $selectedClass = SchoolClass::with('sections')->find($request->class_id);

            if ($request->filled('section_id')) {
                $selectedSection = Section::find($request->section_id);
            }

            if ($reportType === 'monthly') {
                $reportData = $this->getMonthlyReport(
                    $request->class_id,
                    $request->get('section_id'),
                    $selectedMonth,
                    $selectedYear,
                    $activeYear->id ?? null
                );
            } elseif ($reportType === 'daily') {
                $reportData = $this->getDailyReport(
                    $request->class_id,
                    $request->get('section_id'),
                    $request->get('date', now()->format('Y-m-d')),
                    $activeYear->id ?? null
                );
            }
        }

        return view('admin.attendance.reports', compact(
            'classes',
            'reportData',
            'activeYear',
            'selectedClass',
            'selectedSection',
            'selectedMonth',
            'selectedYear',
            'reportType'
        ));
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