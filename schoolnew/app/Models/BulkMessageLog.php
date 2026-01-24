<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BulkMessageLog extends Model
{
    protected $fillable = [
        'bulk_message_id',
        'user_id',
        'recipient_name',
        'recipient_phone',
        'recipient_email',
        'channel',
        'status',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    const CHANNEL_SMS = 'sms';
    const CHANNEL_EMAIL = 'email';
    const CHANNEL_NOTIFICATION = 'notification';

    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_FAILED = 'failed';

    public function bulkMessage(): BelongsTo
    {
        return $this->belongsTo(BulkMessage::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'badge-light-warning',
            self::STATUS_SENT => 'badge-light-info',
            self::STATUS_DELIVERED => 'badge-light-success',
            self::STATUS_FAILED => 'badge-light-danger',
            default => 'badge-light-secondary',
        };
    }

    public function getStatusLabel(): string
    {
        return ucfirst($this->status);
    }
}
