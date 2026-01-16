<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaffAttendance;
use App\Models\StaffAttendanceSummary;
use App\Models\Staff;
use App\Models\Department;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StaffAttendanceController extends Controller
{
    public function mark(Request $request)
    {
        $activeYear = AcademicYear::getActive();
        $departments = Department::where('is_active', true)->get();

        $staff = collect();
        $attendanceData = collect();
        $selectedDepartment = null;
        $selectedDate = $request->get('date', now()->format('Y-m-d'));

        // Get staff members
        $staffQuery = Staff::with(['department', 'designation'])->active();

        if ($request->filled('department_id')) {
            $selectedDepartment = Department::find($request->department_id);
            $staffQuery->where('department_id', $request->department_id);
        }

        if ($request->has('load')) {
            $staff = $staffQuery->orderBy('first_name')->get();

            // Get existing attendance for the selected date
            if ($activeYear) {
                $attendanceData = StaffAttendance::getAttendanceForDate($selectedDate, $activeYear->id);
            }
        }

        return view('admin.attendance.staff-mark', compact(
            'departments',
            'staff',
            'attendanceData',
            'activeYear',
            'selectedDepartment',
            'selectedDate'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*' => 'in:present,absent,late,half_day,on_leave',
            'check_in_time' => 'nullable|array',
            'check_in_time.*' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|array',
            'check_out_time.*' => 'nullable|date_format:H:i',
            'remarks' => 'nullable|array',
            'remarks.*' => 'nullable|string|max:255',
        ]);

        $activeYear = AcademicYear::getActive();

        if (!$activeYear) {
            return redirect()->back()->with('error', 'No active academic year found.');
        }

        try {
            DB::beginTransaction();

            foreach ($validated['attendance'] as $staffId => $status) {
                $attendanceData = [
                    'staff_id' => $staffId,
                    'academic_year_id' => $activeYear->id,
                    'attendance_date' => $validated['date'],
                    'status' => $status,
                    'check_in_time' => $validated['check_in_time'][$staffId] ?? null,
                    'check_out_time' => $validated['check_out_time'][$staffId] ?? null,
                    'remarks' => $validated['remarks'][$staffId] ?? null,
                    'marked_by' => auth()->id(),
                ];

                StaffAttendance::updateOrCreate(
                    [
                        'staff_id' => $staffId,
                        'attendance_date' => $validated['date'],
                    ],
                    $attendanceData
                );

                // Update monthly summary
                $date = Carbon::parse($validated['date']);
                StaffAttendanceSummary::updateSummary(
                    $staffId,
                    $date->month,
                    $date->year,
                    $activeYear->id
                );
            }

            DB::commit();

            return redirect()->route('admin.staff-attendance.mark', [
                'department_id' => $request->department_id,
                'date' => $validated['date'],
                'load' => 1
            ])->with('success', 'Staff attendance marked successfully for ' . count($validated['attendance']) . ' staff members.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to mark attendance. Please try again.');
        }
    }

    public function reports(Request $request)
    {
        $activeYear = AcademicYear::getActive();
        $departments = Department::where('is_active', true)->get();

        $reportData = collect();
        $selectedDepartment = null;
        $fromDate = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->format('Y-m-d'));

        if ($request->has('generate') && $request->filled('from_date') && $request->filled('to_date')) {
            if ($request->filled('department_id')) {
                $selectedDepartment = Department::find($request->department_id);
            }

            $reportData = $this->getDateRangeReport(
                $request->get('department_id'),
                $fromDate,
                $toDate,
                $activeYear->id ?? null
            );
        }

        return view('admin.attendance.staff-reports', compact(
            'departments',
            'reportData',
            'activeYear',
            'selectedDepartment',
            'fromDate',
            'toDate'
        ));
    }

    private function getDateRangeReport($departmentId, $fromDate, $toDate, $academicYearId)
    {
        if (!$academicYearId) return collect();

        // Get all staff members
        $staffQuery = Staff::with(['department', 'designation'])->active();
        if ($departmentId) {
            $staffQuery->where('department_id', $departmentId);
        }
        $staffMembers = $staffQuery->orderBy('first_name')->get();

        // Get attendance records within date range
        $attendanceRecords = StaffAttendance::where('academic_year_id', $academicYearId)
            ->whereBetween('attendance_date', [$fromDate, $toDate])
            ->whereIn('staff_id', $staffMembers->pluck('id'))
            ->get()
            ->groupBy('staff_id');

        // Aggregate data for each staff member
        $reportData = collect();
        foreach ($staffMembers as $staff) {
            $staffAttendance = $attendanceRecords->get($staff->id, collect());

            $totalDays = $staffAttendance->count();
            $presentDays = $staffAttendance->where('status', 'present')->count();
            $absentDays = $staffAttendance->where('status', 'absent')->count();
            $lateDays = $staffAttendance->where('status', 'late')->count();
            $halfDays = $staffAttendance->where('status', 'half_day')->count();
            $leaveDays = $staffAttendance->where('status', 'on_leave')->count();
            $percentage = $totalDays > 0 ? round((($presentDays + ($halfDays * 0.5) + ($lateDays * 0.75)) / $totalDays) * 100, 1) : 0;

            $reportData->push((object)[
                'staff' => $staff,
                'total_days' => $totalDays,
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
                'late_days' => $lateDays,
                'half_days' => $halfDays,
                'leave_days' => $leaveDays,
                'attendance_percentage' => $percentage,
            ]);
        }

        return $reportData->sortByDesc('attendance_percentage')->values();
    }

    private function getMonthlyReport($departmentId, $month, $year, $academicYearId)
    {
        if (!$academicYearId) return collect();

        $query = StaffAttendanceSummary::with('staff')
            ->where('academic_year_id', $academicYearId)
            ->where('month', $month)
            ->where('year', $year);

        if ($departmentId) {
            $query->whereHas('staff', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        return $query->orderBy('attendance_percentage', 'desc')->get();
    }

    private function getDailyReport($departmentId, $date, $academicYearId)
    {
        if (!$academicYearId) return collect();

        $query = StaffAttendance::with('staff')
            ->where('attendance_date', $date)
            ->where('academic_year_id', $academicYearId);

        if ($departmentId) {
            $query->whereHas('staff', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        return $query->orderBy('status')->get();
    }

    private function getYearlyReport($departmentId, $year, $academicYearId)
    {
        if (!$academicYearId) return collect();

        // Get all staff members
        $staffQuery = Staff::with(['department', 'designation'])->active();
        if ($departmentId) {
            $staffQuery->where('department_id', $departmentId);
        }
        $staffMembers = $staffQuery->orderBy('first_name')->get();

        // Get monthly summaries for the entire year
        $summaries = StaffAttendanceSummary::where('academic_year_id', $academicYearId)
            ->where('year', $year)
            ->whereIn('staff_id', $staffMembers->pluck('id'))
            ->get()
            ->groupBy('staff_id');

        // Aggregate yearly data for each staff member
        $yearlyData = collect();
        foreach ($staffMembers as $staff) {
            $staffSummaries = $summaries->get($staff->id, collect());

            $totalDays = $staffSummaries->sum('total_days');
            $presentDays = $staffSummaries->sum('present_days');
            $absentDays = $staffSummaries->sum('absent_days');
            $lateDays = $staffSummaries->sum('late_days');
            $halfDays = $staffSummaries->sum('half_days');
            $leaveDays = $staffSummaries->sum('leave_days');
            $percentage = $totalDays > 0 ? round((($presentDays + ($halfDays * 0.5) + ($lateDays * 0.75)) / $totalDays) * 100, 1) : 0;

            // Get month-wise breakdown
            $monthlyBreakdown = [];
            for ($m = 1; $m <= 12; $m++) {
                $monthSummary = $staffSummaries->firstWhere('month', $m);
                $monthlyBreakdown[$m] = $monthSummary ? [
                    'total' => $monthSummary->total_days,
                    'present' => $monthSummary->present_days,
                    'absent' => $monthSummary->absent_days,
                    'late' => $monthSummary->late_days,
                    'half_day' => $monthSummary->half_days,
                    'leave' => $monthSummary->leave_days,
                    'percentage' => $monthSummary->attendance_percentage,
                ] : null;
            }

            $yearlyData->push((object)[
                'staff' => $staff,
                'total_days' => $totalDays,
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
                'late_days' => $lateDays,
                'half_days' => $halfDays,
                'leave_days' => $leaveDays,
                'attendance_percentage' => $percentage,
                'monthly_breakdown' => $monthlyBreakdown,
            ]);
        }

        return $yearlyData->sortByDesc('attendance_percentage')->values();
    }
}
