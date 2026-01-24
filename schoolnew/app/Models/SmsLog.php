<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsLog extends Model
{
    protected $fillable = [
        'recipient_phone',
        'recipient_name',
        'recipient_type',
        'recipient_id',
        'message_type',
        'message',
        'status',
        'message_id',
        'error_message',
        'cost',
        'sent_by',
        'sent_at',
        'delivered_at',
    ];

    protected $casts = [
        'cost' => 'decimal:4',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_FAILED = 'failed';

    const MESSAGE_TYPES = [
        'admission' => 'New Admission',
        'fee_collection' => 'Fee Collection',
        'fee_reminder' => 'Fee Reminder',
        'attendance' => 'Attendance Alert',
        'exam_result' => 'Exam Result',
        'leave_approval' => 'Leave Approval',
        'notice' => 'Notice',
        'bulk' => 'Bulk Message',
        'custom' => 'Custom Message',
    ];

    /**
     * Get the sender user.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_DELIVERED => 'badge-light-success',
            self::STATUS_SENT => 'badge-light-info',
            self::STATUS_PENDING => 'badge-light-warning',
            self::STATUS_FAILED => 'badge-light-danger',
            default => 'badge-light-secondary',
        };
    }

    /**
     * Get status label.
     */
    public function getStatusLabel(): string
    {
        return ucfirst($this->status);
    }

    /**
     * Get message type label.
     */
    public function getMessageTypeLabelAttribute(): string
    {
        return self::MESSAGE_TYPES[$this->message_type] ?? $this->message_type ?? 'Unknown';
    }

    /**
     * Scope for pending messages.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for failed messages.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }
}
