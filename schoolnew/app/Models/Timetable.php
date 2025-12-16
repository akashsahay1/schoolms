<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Timetable extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'academic_year_id',
        'class_id',
        'section_id',
        'subject_id',
        'teacher_id',
        'period_id',
        'day',
        'room_number',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'teacher_id');
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(TimetablePeriod::class, 'period_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForDay($query, $day)
    {
        return $query->where('day', $day);
    }

    public function scopeForClassSection($query, $classId, $sectionId)
    {
        return $query->where('class_id', $classId)
                    ->where('section_id', $sectionId);
    }

    public function scopeForTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeForCurrentAcademicYear($query)
    {
        $activeYear = AcademicYear::getActive();
        if ($activeYear) {
            return $query->where('academic_year_id', $activeYear->id);
        }
        return $query;
    }

    public static function getDays()
    {
        return [
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday',
        ];
    }
}