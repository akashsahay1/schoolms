<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffAttendanceSummary extends Model
{
    protected $table = 'staff_attendance_summaries';

    protected $fillable = [
        'staff_id',
        'academic_year_id',
        'month',
        'year',
        'total_days',
        'present_days',
        'absent_days',
        'late_days',
        'half_days',
        'leave_days',
        'attendance_percentage',
    ];

    protected $casts = [
        'attendance_percentage' => 'float',
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public static function updateSummary($staffId, $month, $year, $academicYearId)
    {
        $attendances = StaffAttendance::where('staff_id', $staffId)
            ->where('academic_year_id', $academicYearId)
            ->whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year)
            ->get();

        $total = $attendances->count();
        $present = $attendances->where('status', 'present')->count();
        $absent = $attendances->where('status', 'absent')->count();
        $late = $attendances->where('status', 'late')->count();
        $halfDay = $attendances->where('status', 'half_day')->count();
        $onLeave = $attendances->where('status', 'on_leave')->count();

        $effectivePresent = $present + ($halfDay * 0.5) + ($late * 0.75);
        $percentage = $total > 0 ? round(($effectivePresent / $total) * 100, 2) : 0;

        return self::updateOrCreate(
            [
                'staff_id' => $staffId,
                'month' => $month,
                'year' => $year,
                'academic_year_id' => $academicYearId,
            ],
            [
                'total_days' => $total,
                'present_days' => $present,
                'absent_days' => $absent,
                'late_days' => $late,
                'half_days' => $halfDay,
                'leave_days' => $onLeave,
                'attendance_percentage' => $percentage,
            ]
        );
    }
}
