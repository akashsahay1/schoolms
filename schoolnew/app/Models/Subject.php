<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subject extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'type',
        'full_marks',
        'pass_marks',
        'is_optional',
        'is_active',
    ];

    protected $casts = [
        'is_optional' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'class_subject', 'subject_id', 'class_id')
            ->withPivot('teacher_id', 'credit_hours')
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeTheory($query)
    {
        return $query->whereIn('type', ['theory', 'both']);
    }

    public function scopePractical($query)
    {
        return $query->whereIn('type', ['practical', 'both']);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('name');
    }
}
