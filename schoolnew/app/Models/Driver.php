<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'first_name',
        'last_name',
        'phone',
        'alternate_phone',
        'email',
        'address',
        'date_of_birth',
        'gender',
        'license_number',
        'license_type',
        'license_expiry',
        'joining_date',
        'salary',
        'blood_group',
        'emergency_contact_name',
        'emergency_contact_phone',
        'photo',
        'license_document',
        'id_proof_document',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'license_expiry' => 'date',
        'joining_date' => 'date',
        'salary' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the vehicles assigned to this driver.
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    /**
     * Get driver's full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Check if license is expired.
     */
    public function isLicenseExpired(): bool
    {
        return $this->license_expiry->isPast();
    }

    /**
     * Check if license is expiring soon (within 30 days).
     */
    public function isLicenseExpiringSoon(): bool
    {
        return $this->license_expiry->diffInDays(now()) <= 30 && !$this->isLicenseExpired();
    }

    /**
     * Get license status badge class.
     */
    public function getLicenseStatusBadgeClass(): string
    {
        if ($this->isLicenseExpired()) {
            return 'badge-light-danger';
        }
        if ($this->isLicenseExpiringSoon()) {
            return 'badge-light-warning';
        }
        return 'badge-light-success';
    }

    /**
     * Get license status label.
     */
    public function getLicenseStatusLabel(): string
    {
        if ($this->isLicenseExpired()) {
            return 'Expired';
        }
        if ($this->isLicenseExpiringSoon()) {
            return 'Expiring Soon';
        }
        return 'Valid';
    }

    /**
     * Generate unique employee ID.
     */
    public static function generateEmployeeId(): string
    {
        $prefix = 'DRV';
        $year = date('Y');
        $lastDriver = self::withTrashed()
            ->where('employee_id', 'like', "{$prefix}{$year}%")
            ->orderBy('employee_id', 'desc')
            ->first();

        if ($lastDriver) {
            $lastNumber = (int) substr($lastDriver->employee_id, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "{$prefix}{$year}{$newNumber}";
    }
}
