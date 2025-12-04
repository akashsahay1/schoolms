<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Staff extends Model
{
    use SoftDeletes;

    protected $table = 'staff';

    protected $fillable = [
        'user_id',
        'department_id',
        'designation_id',
        // Basic Information
        'staff_id',
        'first_name',
        'last_name',
        'gender',
        'date_of_birth',
        'blood_group',
        'religion',
        'marital_status',
        'nationality',
        // Contact Information
        'email',
        'phone',
        'emergency_contact',
        'current_address',
        'permanent_address',
        // Identification
        'national_id',
        'passport_no',
        'driving_license',
        // Photo
        'photo',
        // Employment Information
        'joining_date',
        'contract_type',
        'basic_salary',
        'bank_name',
        'bank_account_no',
        'bank_branch',
        'pan_number',
        'epf_no',
        // Qualifications
        'qualification',
        'experience',
        'skills',
        // Social Links
        'facebook',
        'twitter',
        'linkedin',
        'bio',
        // Status
        'status',
        'leaving_date',
        'leaving_reason',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'joining_date' => 'date',
        'leaving_date' => 'date',
        'basic_salary' => 'decimal:2',
    ];

    protected $hidden = [
        'basic_salary',
        'bank_account_no',
        'pan_number',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeTeachers($query)
    {
        return $query->whereHas('designation', function ($q) {
            $q->where('name', 'like', '%teacher%');
        });
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getAgeAttribute(): int
    {
        return $this->date_of_birth?->age ?? 0;
    }

    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return asset('assets/images/user/default-avatar.png');
    }

    public function getExperienceYearsAttribute(): int
    {
        return $this->joining_date?->diffInYears(now()) ?? 0;
    }
}
