<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromotionRule extends Model
{
    protected $fillable = [
        'academic_year_id',
        'class_id',
        'min_attendance_percentage',
        'min_marks_percentage',
        'consider_attendance',
        'consider_marks',
        'auto_promote',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'min_attendance_percentage' => 'decimal:2',
        'min_marks_percentage' => 'decimal:2',
        'consider_attendance' => 'boolean',
        'consider_marks' => 'boolean',
        'auto_promote' => 'boolean',
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

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeForAcademicYear($query, $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }
}
