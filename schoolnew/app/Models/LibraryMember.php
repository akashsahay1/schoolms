<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibraryMember extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'member_id',
        'memberable_type',
        'memberable_id',
        'membership_start',
        'membership_end',
        'status',
        'max_books_allowed',
        'current_books_count',
        'total_fines',
        'paid_fines',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'membership_start' => 'date',
        'membership_end' => 'date',
        'total_fines' => 'decimal:2',
        'paid_fines' => 'decimal:2',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_EXPIRED = 'expired';
    const STATUS_SUSPENDED = 'suspended';

    /**
     * Get the parent memberable model (student or staff).
     */
    public function memberable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who created this membership.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope for active members.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope for expired members.
     */
    public function scopeExpired($query)
    {
        return $query->where('status', self::STATUS_EXPIRED);
    }

    /**
     * Scope for suspended members.
     */
    public function scopeSuspended($query)
    {
        return $query->where('status', self::STATUS_SUSPENDED);
    }

    /**
     * Check if membership is expired.
     */
    public function getIsExpiredAttribute(): bool
    {
        if (!$this->membership_end) {
            return false;
        }
        return $this->membership_end < now();
    }

    /**
     * Get outstanding fines.
     */
    public function getOutstandingFinesAttribute(): float
    {
        return $this->total_fines - $this->paid_fines;
    }

    /**
     * Check if member can borrow more books.
     */
    public function getCanBorrowAttribute(): bool
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return false;
        }

        if ($this->is_expired) {
            return false;
        }

        if ($this->current_books_count >= $this->max_books_allowed) {
            return false;
        }

        // Check if there are outstanding fines above threshold
        $maxOutstandingFines = Setting::get('library_max_outstanding_fines', 100);
        if ($this->outstanding_fines > $maxOutstandingFines) {
            return false;
        }

        return true;
    }

    /**
     * Get member name based on memberable type.
     */
    public function getMemberNameAttribute(): string
    {
        if ($this->memberable) {
            if ($this->memberable_type === Student::class) {
                return $this->memberable->full_name ?? $this->memberable->first_name . ' ' . $this->memberable->last_name;
            } elseif ($this->memberable_type === Staff::class) {
                return $this->memberable->full_name ?? $this->memberable->first_name . ' ' . $this->memberable->last_name;
            }
        }
        return 'Unknown';
    }

    /**
     * Get member type label.
     */
    public function getMemberTypeAttribute(): string
    {
        if ($this->memberable_type === Student::class) {
            return 'Student';
        } elseif ($this->memberable_type === Staff::class) {
            return 'Staff';
        }
        return 'Unknown';
    }

    /**
     * Generate a unique member ID.
     */
    public static function generateMemberId(string $type = 'S'): string
    {
        $prefix = $type === 'staff' ? 'LM-STF-' : 'LM-STU-';
        $year = now()->format('Y');

        // Get the last member ID for this type and year
        $lastMember = self::where('member_id', 'like', $prefix . $year . '%')
            ->orderBy('member_id', 'desc')
            ->first();

        if ($lastMember) {
            $lastNumber = (int) substr($lastMember->member_id, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $year . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get book issues for this member.
     */
    public function getBookIssuesAttribute()
    {
        if ($this->memberable_type === Student::class) {
            return BookIssue::where('student_id', $this->memberable_id)->get();
        }
        return collect();
    }

    /**
     * Update membership status based on end date.
     */
    public function updateStatus(): void
    {
        if ($this->status === self::STATUS_SUSPENDED) {
            return; // Don't auto-update suspended memberships
        }

        if ($this->is_expired) {
            $this->update(['status' => self::STATUS_EXPIRED]);
        } else {
            $this->update(['status' => self::STATUS_ACTIVE]);
        }
    }
}
