<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactMessage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'student_id',
        'subject',
        'message',
        'category',
        'priority',
        'status',
        'admin_response',
        'assigned_to',
        'responded_by',
        'responded_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    public const CATEGORIES = [
        'general' => 'General Inquiry',
        'academic' => 'Academic',
        'fee' => 'Fee Related',
        'transport' => 'Transport',
        'complaint' => 'Complaint',
        'suggestion' => 'Suggestion',
    ];

    public const PRIORITIES = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
    ];

    public const STATUSES = [
        'open' => 'Open',
        'in_progress' => 'In Progress',
        'resolved' => 'Resolved',
        'closed' => 'Closed',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function respondedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeResolved($query)
    {
        return $query->whereIn('status', ['resolved', 'closed']);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function getCategoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? 'General';
    }

    public function getPriorityLabel(): string
    {
        return self::PRIORITIES[$this->priority] ?? 'Medium';
    }

    public function getStatusLabel(): string
    {
        return self::STATUSES[$this->status] ?? 'Open';
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'in_progress' => 'badge-light-warning',
            'resolved' => 'badge-light-success',
            'closed' => 'badge-light-secondary',
            default => 'badge-light-info',
        };
    }

    public function getPriorityBadgeClass(): string
    {
        return match ($this->priority) {
            'high' => 'badge-light-danger',
            'low' => 'badge-light-secondary',
            default => 'badge-light-warning',
        };
    }

    public function isOpen(): bool
    {
        return in_array($this->status, ['open', 'in_progress']);
    }
}
