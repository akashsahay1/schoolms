<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class LeaveApplication extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'applicant_type',
        'applicant_id',
        'student_id',
        'applied_by',
        'leave_type',
        'from_date',
        'to_date',
        'total_days',
        'reason',
        'attachment',
        'status',
        'admin_remarks',
        'approved_by',
        'approved_at',
        'academic_year_id',
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public const LEAVE_TYPES = [
        'sick' => 'Sick Leave',
        'personal' => 'Personal Leave',
        'emergency' => 'Emergency Leave',
        'family' => 'Family Leave',
        'other' => 'Other',
    ];

    public const STATUSES = [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'cancelled' => 'Cancelled',
    ];

    public function applicant(): MorphTo
    {
        return $this->morphTo();
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function appliedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applied_by');
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function getLeaveTypeLabel(): string
    {
        return self::LEAVE_TYPES[$this->leave_type] ?? 'Other';
    }

    public function getStatusLabel(): string
    {
        return self::STATUSES[$this->status] ?? 'Unknown';
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'approved' => 'badge-light-success',
            'rejected' => 'badge-light-danger',
            'cancelled' => 'badge-light-secondary',
            default => 'badge-light-warning',
        };
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeModified(): bool
    {
        return $this->isPending() && $this->from_date->isFuture();
    }
}
