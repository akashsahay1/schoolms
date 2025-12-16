<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'content',
        'type',
        'publish_date',
        'expiry_date',
        'target_audience',
        'target_classes',
        'attachment',
        'is_published',
        'send_email',
        'send_sms',
        'created_by',
        'academic_year_id',
    ];

    protected $casts = [
        'publish_date' => 'date',
        'expiry_date' => 'date',
        'target_audience' => 'array',
        'target_classes' => 'array',
        'is_published' => 'boolean',
        'send_email' => 'boolean',
        'send_sms' => 'boolean',
    ];

    public const TYPES = [
        'general' => 'General',
        'urgent' => 'Urgent',
        'academic' => 'Academic',
        'exam' => 'Exam',
        'holiday' => 'Holiday',
        'event' => 'Event',
    ];

    public const AUDIENCES = [
        'all' => 'All',
        'students' => 'Students',
        'parents' => 'Parents',
        'teachers' => 'Teachers',
        'staff' => 'Staff',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeActive($query)
    {
        return $query->where('publish_date', '<=', now())
            ->where(function ($q) {
                $q->whereNull('expiry_date')
                    ->orWhere('expiry_date', '>=', now());
            });
    }

    public function scopeForAudience($query, string $audience)
    {
        return $query->where(function ($q) use ($audience) {
            $q->whereJsonContains('target_audience', 'all')
                ->orWhereJsonContains('target_audience', $audience);
        });
    }

    public function scopeForClass($query, $classId)
    {
        return $query->where(function ($q) use ($classId) {
            $q->whereNull('target_classes')
                ->orWhereJsonContains('target_classes', $classId);
        });
    }

    public function getTypeLabel(): string
    {
        return self::TYPES[$this->type] ?? 'General';
    }

    public function getTypeBadgeClass(): string
    {
        return match ($this->type) {
            'urgent' => 'badge-light-danger',
            'academic' => 'badge-light-primary',
            'exam' => 'badge-light-warning',
            'holiday' => 'badge-light-success',
            'event' => 'badge-light-info',
            default => 'badge-light-secondary',
        };
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }
}
