<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParentGuardian extends Model
{
    use SoftDeletes;

    protected $table = 'parents';

    protected $fillable = [
        'user_id',
        // Father's Information
        'father_name',
        'father_phone',
        'father_email',
        'father_occupation',
        'father_photo',
        'father_national_id',
        // Mother's Information
        'mother_name',
        'mother_phone',
        'mother_email',
        'mother_occupation',
        'mother_photo',
        'mother_national_id',
        // Guardian Information
        'guardian_name',
        'guardian_relation',
        'guardian_phone',
        'guardian_email',
        'guardian_occupation',
        'guardian_photo',
        'guardian_address',
        // Address
        'current_address',
        'permanent_address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'parent_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getPrimaryContactAttribute(): string
    {
        return $this->father_phone ?? $this->mother_phone ?? $this->guardian_phone ?? '';
    }

    public function getPrimaryEmailAttribute(): string
    {
        return $this->father_email ?? $this->mother_email ?? $this->guardian_email ?? '';
    }
}
