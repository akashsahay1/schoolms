<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'allowed_days',
        'is_paid',
        'requires_attachment',
        'is_active',
        'applicable_to',
    ];

    protected $casts = [
        'allowed_days' => 'integer',
        'is_paid' => 'boolean',
        'requires_attachment' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function leaveBalances(): HasMany
    {
        return $this->hasMany(StaffLeaveBalance::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForStaff($query)
    {
        return $query->whereIn('applicable_to', ['all', 'staff']);
    }

    public function scopeForStudents($query)
    {
        return $query->whereIn('applicable_to', ['all', 'students']);
    }
}
