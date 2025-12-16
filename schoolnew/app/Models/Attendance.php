<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Attendance extends Model
{
    protected $fillable = [
        'student_id',
        'class_id',
        'section_id',
        'academic_year_id',
        'attendance_date',
        'status',
        'check_in_time',
        'check_out_time',
        'remarks',
        'marked_by',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in_time' => 'datetime:H:i',
        'check_out_time' => 'datetime:H:i',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function markedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    // Scopes
    public function scopeForDate($query, $date)
    {
        return $query->where('attendance_date', $date);
    }

    public function scopeForClass($query, $classId, $sectionId = null)
    {
        $query = $query->where('class_id', $classId);
        
        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }
        
        return $query;
    }

    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    // Helper methods
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'present' => '<span class="badge bg-success">Present</span>',
            'absent' => '<span class="badge bg-danger">Absent</span>',
            'late' => '<span class="badge bg-warning">Late</span>',
            'half_day' => '<span class="badge bg-info">Half Day</span>',
        };
    }

    public static function getAttendanceForDate($classId, $sectionId, $date, $academicYearId)
    {
        return self::with('student')
            ->where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->where('attendance_date', $date)
            ->where('academic_year_id', $academicYearId)
            ->get()
            ->keyBy('student_id');
    }

    public static function calculateMonthlyAttendance($studentId, $month, $year, $academicYearId)
    {
        $attendances = self::where('student_id', $studentId)
            ->where('academic_year_id', $academicYearId)
            ->whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year)
            ->get();

        $total = $attendances->count();
        $present = $attendances->where('status', 'present')->count();
        $absent = $attendances->where('status', 'absent')->count();
        $late = $attendances->where('status', 'late')->count();
        $halfDay = $attendances->where('status', 'half_day')->count();

        return [
            'total_days' => $total,
            'present_days' => $present,
            'absent_days' => $absent,
            'late_days' => $late,
            'half_days' => $halfDay,
            'attendance_percentage' => $total > 0 ? round(($present / $total) * 100, 2) : 0,
        ];
    }
}