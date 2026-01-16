<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentPromotion extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'student_id',
        'from_academic_year_id',
        'to_academic_year_id',
        'from_class_id',
        'to_class_id',
        'from_section_id',
        'to_section_id',
        'status',
        'promotion_type',
        'final_percentage',
        'attendance_percentage',
        'grade',
        'rank',
        'remarks',
        'promoted_by',
        'promoted_at',
    ];

    protected $casts = [
        'final_percentage' => 'decimal:2',
        'attendance_percentage' => 'decimal:2',
        'promoted_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_PROMOTED = 'promoted';
    const STATUS_RETAINED = 'retained';
    const STATUS_ALUMNI = 'alumni';
    const STATUS_CANCELLED = 'cancelled';

    const TYPE_REGULAR = 'regular';
    const TYPE_CONDITIONAL = 'conditional';
    const TYPE_SPECIAL = 'special';

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

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

    public function toClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'to_class_id');
    }

    public function fromSection(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'from_section_id');
    }

    public function toSection(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'to_section_id');
    }

    public function promotedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'promoted_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopePromoted($query)
    {
        return $query->where('status', self::STATUS_PROMOTED);
    }

    public function scopeRetained($query)
    {
        return $query->where('status', self::STATUS_RETAINED);
    }

    public function scopeAlumni($query)
    {
        return $query->where('status', self::STATUS_ALUMNI);
    }

    public function scopeForAcademicYear($query, $academicYearId)
    {
        return $query->where('from_academic_year_id', $academicYearId);
    }

    public function scopeForClass($query, $classId)
    {
        return $query->where('from_class_id', $classId);
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            self::STATUS_PENDING => '<span class="badge bg-warning">Pending</span>',
            self::STATUS_PROMOTED => '<span class="badge bg-success">Promoted</span>',
            self::STATUS_RETAINED => '<span class="badge bg-danger">Retained</span>',
            self::STATUS_ALUMNI => '<span class="badge bg-info">Alumni</span>',
            self::STATUS_CANCELLED => '<span class="badge bg-secondary">Cancelled</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }

    public function getPromotionTypeBadgeAttribute()
    {
        return match ($this->promotion_type) {
            self::TYPE_REGULAR => '<span class="badge bg-primary">Regular</span>',
            self::TYPE_CONDITIONAL => '<span class="badge bg-warning">Conditional</span>',
            self::TYPE_SPECIAL => '<span class="badge bg-info">Special</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }
}
