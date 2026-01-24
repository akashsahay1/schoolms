<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffLeaveBalance extends Model
{
    protected $fillable = [
        'staff_id',
        'leave_type_id',
        'academic_year_id',
        'allocated_days',
        'used_days',
        'carried_forward',
    ];

    protected $casts = [
        'allocated_days' => 'integer',
        'used_days' => 'integer',
        'carried_forward' => 'integer',
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get remaining days
     */
    public function getRemainingDaysAttribute(): int
    {
        return ($this->allocated_days + $this->carried_forward) - $this->used_days;
    }

    /**
     * Get total available days
     */
    public function getTotalAvailableAttribute(): int
    {
        return $this->allocated_days + $this->carried_forward;
    }

    /**
     * Check if staff can take leave
     */
    public function canTakeLeave(int $days): bool
    {
        return $this->remaining_days >= $days;
    }

    /**
     * Deduct days from balance
     */
    public function deductDays(int $days): bool
    {
        if (!$this->canTakeLeave($days)) {
            return false;
        }

        $this->used_days += $days;
        return $this->save();
    }

    /**
     * Add days back to balance (when leave is cancelled)
     */
    public function addDays(int $days): bool
    {
        $this->used_days = max(0, $this->used_days - $days);
        return $this->save();
    }
}
