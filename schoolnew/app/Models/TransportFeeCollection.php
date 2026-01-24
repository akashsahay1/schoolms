<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransportFeeCollection extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'transport_fee_id',
        'student_id',
        'route_assignment_id',
        'month',
        'amount',
        'discount',
        'fine',
        'paid_amount',
        'payment_date',
        'payment_mode',
        'receipt_number',
        'transaction_id',
        'status',
        'remarks',
        'collected_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'fine' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_PARTIAL = 'partial';
    const STATUS_PAID = 'paid';
    const STATUS_WAIVED = 'waived';

    const PAYMENT_MODES = [
        'cash' => 'Cash',
        'online' => 'Online',
        'cheque' => 'Cheque',
        'bank_transfer' => 'Bank Transfer',
        'upi' => 'UPI',
        'card' => 'Card',
    ];

    /**
     * Get the transport fee.
     */
    public function transportFee(): BelongsTo
    {
        return $this->belongsTo(TransportFee::class);
    }

    /**
     * Get the student.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the route assignment.
     */
    public function routeAssignment(): BelongsTo
    {
        return $this->belongsTo(RouteAssignment::class);
    }

    /**
     * Get the collector user.
     */
    public function collector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    /**
     * Get total payable amount.
     */
    public function getTotalPayableAttribute(): float
    {
        return ($this->amount + $this->fine) - $this->discount;
    }

    /**
     * Get balance amount.
     */
    public function getBalanceAttribute(): float
    {
        return $this->total_payable - $this->paid_amount;
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_PAID => 'badge-light-success',
            self::STATUS_PARTIAL => 'badge-light-warning',
            self::STATUS_PENDING => 'badge-light-danger',
            self::STATUS_WAIVED => 'badge-light-info',
            default => 'badge-light-secondary',
        };
    }

    /**
     * Get status label.
     */
    public function getStatusLabel(): string
    {
        return ucfirst($this->status);
    }

    /**
     * Generate unique receipt number.
     */
    public static function generateReceiptNumber(): string
    {
        $prefix = 'TFR';
        $date = date('Ymd');
        $lastReceipt = self::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        if ($lastReceipt && preg_match('/TFR\d{8}(\d{4})/', $lastReceipt->receipt_number, $matches)) {
            $lastNumber = (int)$matches[1];
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "{$prefix}{$date}{$newNumber}";
    }

    /**
     * Scope for pending payments.
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_PARTIAL]);
    }

    /**
     * Scope for paid payments.
     */
    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }
}
