<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sender_id',
        'sender_type',
        'recipient_id',
        'recipient_type',
        'student_id',
        'subject',
        'message',
        'attachment',
        'parent_message_id',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function parentMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'parent_message_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Message::class, 'parent_message_id')->orderBy('created_at');
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('sender_id', $userId)
                ->orWhere('recipient_id', $userId);
        });
    }

    public function scopeInbox($query, $userId)
    {
        return $query->where('recipient_id', $userId)
            ->whereNull('parent_message_id');
    }

    public function scopeSent($query, $userId)
    {
        return $query->where('sender_id', $userId)
            ->whereNull('parent_message_id');
    }

    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    public function isFromCurrentUser(): bool
    {
        return $this->sender_id === auth()->id();
    }

    public function getOtherParty(): ?User
    {
        if ($this->sender_id === auth()->id()) {
            return $this->recipient;
        }
        return $this->sender;
    }

    public static function getConversation(int $user1, int $user2, ?int $studentId = null)
    {
        return self::where(function ($query) use ($user1, $user2) {
            $query->where(function ($q) use ($user1, $user2) {
                $q->where('sender_id', $user1)->where('recipient_id', $user2);
            })->orWhere(function ($q) use ($user1, $user2) {
                $q->where('sender_id', $user2)->where('recipient_id', $user1);
            });
        })
        ->when($studentId, fn($q) => $q->where('student_id', $studentId))
        ->whereNull('parent_message_id')
        ->orderBy('created_at', 'desc');
    }
}
