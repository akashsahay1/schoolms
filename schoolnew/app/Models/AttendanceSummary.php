<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceSummary extends Model
{
    protected $fillable = [
        'student_id',
        'academic_year_id',
        'month',
        'year',
        'total_days',
        'present_days',
        'absent_days',
        'late_days',
        'half_days',
        'attendance_percentage',
    ];

    protected $casts = [
        'attendance_percentage' => 'decimal:2',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function getMonthNameAttribute()
    {
        return date('F', mktime(0, 0, 0, $this->month, 1));
    }

    public static function updateSummary($studentId, $month, $year, $academicYearId)
    {
        $data = Attendance::calculateMonthlyAttendance($studentId, $month, $year, $academicYearId);
        
        return self::updateOrCreate(
            [
                'student_id' => $studentId,
                'academic_year_id' => $academicYearId,
                'month' => $month,
                'year' => $year,
            ],
            $data
        );
    }
}