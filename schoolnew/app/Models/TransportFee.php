<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransportFee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'academic_year_id',
        'transport_route_id',
        'fee_type',
        'amount',
        'fine_per_day',
        'fine_grace_days',
        'due_date',
        'description',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fine_per_day' => 'decimal:2',
        'fine_grace_days' => 'integer',
        'due_date' => 'date',
        'is_active' => 'boolean',
    ];

    const FEE_TYPES = [
        'monthly' => 'Monthly',
        'quarterly' => 'Quarterly',
        'half_yearly' => 'Half Yearly',
        'yearly' => 'Yearly',
        'one_time' => 'One Time',
    ];

    /**
     * Get the academic year.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the transport route.
     */
    public function route(): BelongsTo
    {
        return $this->belongsTo(TransportRoute::class, 'transport_route_id');
    }

    /**
     * Get fee collections.
     */
    public function collections(): HasMany
    {
        return $this->hasMany(TransportFeeCollection::class);
    }

    /**
     * Get fee type label.
     */
    public function getFeeTypeLabelAttribute(): string
    {
        return self::FEE_TYPES[$this->fee_type] ?? $this->fee_type;
    }

    /**
     * Calculate fine for a given number of days overdue.
     */
    public function calculateFine(int $daysOverdue): float
    {
        if ($daysOverdue <= $this->fine_grace_days) {
            return 0;
        }

        return ($daysOverdue - $this->fine_grace_days) * $this->fine_per_day;
    }

    /**
     * Scope for active fees.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for academic year.
     */
    public function scopeForAcademicYear($query, $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }
}
