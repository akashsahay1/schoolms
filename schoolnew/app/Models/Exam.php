<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    protected $fillable = [
        'name',
        'exam_type_id',
        'academic_year_id',
        'class_id',
        'start_date',
        'end_date',
        'description',
        'is_published',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_published' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function examType(): BelongsTo
    {
        return $this->belongsTo(ExamType::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(ExamSchedule::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function getStatusAttribute()
    {
        $now = now()->toDateString();
        
        if ($now < $this->start_date) {
            return 'upcoming';
        } elseif ($now > $this->end_date) {
            return 'completed';
        } else {
            return 'ongoing';
        }
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'upcoming' => '<span class="badge bg-info">Upcoming</span>',
            'ongoing' => '<span class="badge bg-success">Ongoing</span>',
            'completed' => '<span class="badge bg-secondary">Completed</span>',
        };
    }
}