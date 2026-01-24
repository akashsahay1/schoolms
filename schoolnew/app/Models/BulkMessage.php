<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BulkMessage extends Model
{
    protected $fillable = [
        'created_by',
        'title',
        'message',
        'message_type',
        'recipient_type',
        'recipient_filters',
        'total_recipients',
        'sent_count',
        'failed_count',
        'status',
        'scheduled_at',
        'sent_at',
    ];

    protected $casts = [
        'recipient_filters' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    const STATUS_DRAFT = 'draft';
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_SENDING = 'sending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    const TYPE_SMS = 'sms';
    const TYPE_EMAIL = 'email';
    const TYPE_NOTIFICATION = 'notification';
    const TYPE_ALL = 'all';

    const RECIPIENT_ALL_STUDENTS = 'all_students';
    const RECIPIENT_ALL_PARENTS = 'all_parents';
    const RECIPIENT_ALL_TEACHERS = 'all_teachers';
    const RECIPIENT_ALL_STAFF = 'all_staff';
    const RECIPIENT_CLASS_WISE = 'class_wise';
    const RECIPIENT_CUSTOM = 'custom';

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(BulkMessageLog::class);
    }

    public function scopeStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'badge-light-secondary',
            self::STATUS_SCHEDULED => 'badge-light-info',
            self::STATUS_SENDING => 'badge-light-warning',
            self::STATUS_COMPLETED => 'badge-light-success',
            self::STATUS_FAILED => 'badge-light-danger',
            default => 'badge-light-secondary',
        };
    }

    public function getStatusLabel(): string
    {
        return ucfirst($this->status);
    }

    public function getMessageTypeLabel(): string
    {
        return match ($this->message_type) {
            self::TYPE_SMS => 'SMS',
            self::TYPE_EMAIL => 'Email',
            self::TYPE_NOTIFICATION => 'Notification',
            self::TYPE_ALL => 'All Channels',
            default => ucfirst($this->message_type),
        };
    }

    public function getRecipientTypeLabel(): string
    {
        return match ($this->recipient_type) {
            self::RECIPIENT_ALL_STUDENTS => 'All Students',
            self::RECIPIENT_ALL_PARENTS => 'All Parents',
            self::RECIPIENT_ALL_TEACHERS => 'All Teachers',
            self::RECIPIENT_ALL_STAFF => 'All Staff',
            self::RECIPIENT_CLASS_WISE => 'Class Wise',
            self::RECIPIENT_CUSTOM => 'Custom Selection',
            default => ucfirst($this->recipient_type),
        };
    }

    public function getSuccessRate(): float
    {
        if ($this->total_recipients === 0) {
            return 0;
        }
        return round(($this->sent_count / $this->total_recipients) * 100, 2);
    }

    public static function getMessageTypes(): array
    {
        return [
            self::TYPE_SMS => 'SMS',
            self::TYPE_EMAIL => 'Email',
            self::TYPE_NOTIFICATION => 'In-App Notification',
            self::TYPE_ALL => 'All Channels',
        ];
    }

    public static function getRecipientTypes(): array
    {
        return [
            self::RECIPIENT_ALL_STUDENTS => 'All Students',
            self::RECIPIENT_ALL_PARENTS => 'All Parents',
            self::RECIPIENT_ALL_TEACHERS => 'All Teachers',
            self::RECIPIENT_ALL_STAFF => 'All Staff',
            self::RECIPIENT_CLASS_WISE => 'Specific Classes',
            self::RECIPIENT_CUSTOM => 'Custom Selection',
        ];
    }
}
