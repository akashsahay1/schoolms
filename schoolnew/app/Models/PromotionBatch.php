<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromotionBatch extends Model
{
    protected $fillable = [
        'from_academic_year_id',
        'to_academic_year_id',
        'from_class_id',
        'from_section_id',
        'total_students',
        'promoted_count',
        'retained_count',
        'alumni_count',
        'status',
        'created_by',
        'processed_at',
        'finalized_at',
        'notes',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
        'finalized_at' => 'datetime',
    ];

    const STATUS_DRAFT = 'draft';
    const STATUS_PROCESSED = 'processed';
    const STATUS_FINALIZED = 'finalized';
    const STATUS_ROLLED_BACK = 'rolled_back';

    public function fromAcademicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'from_academic_year_id');
    }

    public function toAcademicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'to_academic_year_id');
    }

    public function fromClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'from_class_id');
    }

    public function fromSection(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'from_section_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeProcessed($query)
    {
        return $query->where('status', self::STATUS_PROCESSED);
    }

    public function scopeFinalized($query)
    {
        return $query->where('status', self::STATUS_FINALIZED);
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            self::STATUS_DRAFT => '<span class="badge bg-secondary">Draft</span>',
            self::STATUS_PROCESSED => '<span class="badge bg-warning">Processed</span>',
            self::STATUS_FINALIZED => '<span class="badge bg-success">Finalized</span>',
            self::STATUS_ROLLED_BACK => '<span class="badge bg-danger">Rolled Back</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }
}
