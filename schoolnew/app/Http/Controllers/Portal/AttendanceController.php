<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Display the student's attendance records.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return redirect()->route('portal.dashboard');
        }

        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        // Get attendance for selected month
        $attendance = Attendance::where('student_id', $student->id)
            ->whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year)
            ->orderBy('attendance_date')
            ->get()
            ->keyBy(function ($item) {
                return $item->attendance_date->format('Y-m-d');
            });

        // Calculate stats
        $totalDays = $attendance->count();
        $present = $attendance->where('status', 'present')->count();
        $absent = $attendance->where('status', 'absent')->count();
        $late = $attendance->where('status', 'late')->count();
        $halfDay = $attendance->where('status', 'half_day')->count();
        $percentage = $totalDays > 0 ? round((($present + $halfDay * 0.5) / $totalDays) * 100, 1) : 0;

        $stats = [
            'total' => $totalDays,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'half_day' => $halfDay,
            'percentage' => $percentage,
        ];

        // Build calendar data
        $calendarData = $this->buildCalendarData($year, $month, $attendance);

        return view('portal.attendance', compact(
            'student',
            'attendance',
            'stats',
            'calendarData',
            'month',
            'year'
        ));
    }

    /**
     * Build calendar data for displaying attendance in calendar format.
     */
    private function buildCalendarData($year, $month, $attendance)
    {
        $startDate = \Carbon\Carbon::createFromDate($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $calendar = [];
        $currentDate = $startDate->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);

        while ($currentDate <= $endDate->copy()->endOfWeek(\Carbon\Carbon::SATURDAY)) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                $dateKey = $currentDate->format('Y-m-d');
                $dayData = [
                    'date' => $currentDate->copy(),
                    'day' => $currentDate->day,
                    'inMonth' => $currentDate->month == $month,
                    'isToday' => $currentDate->isToday(),
                    'isSunday' => $currentDate->isSunday(),
                    'attendance' => $attendance->get($dateKey),
                ];
                $week[] = $dayData;
                $currentDate->addDay();
            }
            $calendar[] = $week;

            if ($currentDate->month > $month && $currentDate->year >= $year) {
                break;
            }
        }

        return $calendar;
    }
}
