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
        $selectedMonth = $request->get('month', now()->month);
        $selectedYear = $request->get('year', now()->year);
        $reportType = $request->get('report_type', 'monthly');

        if ($request->has('generate')) {
            if ($request->filled('department_id')) {
                $selectedDepartment = Department::find($request->department_id);
            }

            if ($reportType === 'monthly') {
                $reportData = $this->getMonthlyReport(
                    $request->get('department_id'),
                    $selectedMonth,
                    $selectedYear,
                    $activeYear->id ?? null
                );
            } elseif ($reportType === 'daily') {
                $reportData = $this->getDailyReport(
                    $request->get('department_id'),
                    $request->get('date', now()->format('Y-m-d')),
                    $activeYear->id ?? null
                );
            }
        }

        return view('admin.attendance.staff-reports', compact(
            'departments',
            'reportData',
            'activeYear',
            'selectedDepartment',
            'selectedMonth',
            'selectedYear',
            'reportType'
        ));
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
}
