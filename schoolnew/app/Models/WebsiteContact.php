<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteContact extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'status',
        'reply',
        'replied_at',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
    ];

    const STATUS_NEW = 'new';
    const STATUS_READ = 'read';
    const STATUS_REPLIED = 'replied';

    public function scopeStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    public function scopeNew($query)
    {
        return $query->where('status', self::STATUS_NEW);
    }

    public function isNew(): bool
    {
        return $this->status === self::STATUS_NEW;
    }

    public function isReplied(): bool
    {
        return $this->status === self::STATUS_REPLIED;
    }

    public function markAsRead(): void
    {
        if ($this->status === self::STATUS_NEW) {
            $this->update(['status' => self::STATUS_READ]);
        }
    }

    public function sendReply(string $reply): void
    {
        $this->update([
            'reply' => $reply,
            'status' => self::STATUS_REPLIED,
            'replied_at' => now(),
        ]);
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_NEW => 'badge-light-primary',
            self::STATUS_READ => 'badge-light-warning',
            self::STATUS_REPLIED => 'badge-light-success',
            default => 'badge-light-secondary',
        };
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_NEW => 'New',
            self::STATUS_READ => 'Read',
            self::STATUS_REPLIED => 'Replied',
            default => ucfirst($this->status),
        };
    }
}
