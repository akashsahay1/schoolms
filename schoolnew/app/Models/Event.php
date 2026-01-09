<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'type',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'venue',
        'color',
        'is_holiday',
        'is_public',
        'target_audience',
        'target_classes',
        'image',
        'created_by',
        'academic_year_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_holiday' => 'boolean',
        'is_public' => 'boolean',
        'target_audience' => 'array',
        'target_classes' => 'array',
    ];

    public const TYPES = [
        'general' => 'General',
        'cultural' => 'Cultural',
        'sports' => 'Sports',
        'academic' => 'Academic',
        'holiday' => 'Holiday',
        'exam' => 'Exam',
        'meeting' => 'Meeting',
    ];

    public const COLORS = [
        'general' => '#3498db',
        'cultural' => '#9b59b6',
        'sports' => '#27ae60',
        'academic' => '#f39c12',
        'holiday' => '#e74c3c',
        'exam' => '#e67e22',
        'meeting' => '#1abc9c',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(EventPhoto::class)->orderBy('sort_order');
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeHolidays($query)
    {
        return $query->where('is_holiday', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now()->toDateString());
    }

    public function scopeInMonth($query, $year, $month)
    {
        return $query->whereYear('start_date', $year)
            ->whereMonth('start_date', $month);
    }

    public function scopeForAudience($query, string $audience)
    {
        return $query->where(function ($q) use ($audience) {
            $q->whereNull('target_audience')
                ->orWhereRaw('JSON_LENGTH(target_audience) = 0')
                ->orWhereJsonContains('target_audience', 'all')
                ->orWhereJsonContains('target_audience', $audience);
        });
    }

    public function getTypeLabel(): string
    {
        return self::TYPES[$this->type] ?? 'General';
    }

    public function getTypeBadgeClass(): string
    {
        return match ($this->type) {
            'cultural' => 'badge-light-purple',
            'sports' => 'badge-light-success',
            'academic' => 'badge-light-warning',
            'holiday' => 'badge-light-danger',
            'exam' => 'badge-light-orange',
            'meeting' => 'badge-light-info',
            default => 'badge-light-primary',
        };
    }

    public function isMultiDay(): bool
    {
        return $this->end_date && $this->end_date->gt($this->start_date);
    }

    public function getDurationDays(): int
    {
        if (!$this->end_date) {
            return 1;
        }
        return $this->start_date->diffInDays($this->end_date) + 1;
    }
}
