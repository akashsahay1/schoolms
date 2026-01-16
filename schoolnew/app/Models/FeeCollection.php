<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeeCollection extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'student_id',
        'fee_structure_id',
        'academic_year_id',
        'collected_by',
        'amount',
        'discount_amount',
        'fine_amount',
        'paid_amount',
        'payment_mode',
        'transaction_id',
        'payment_date',
        'remarks',
        'receipt_no',
        'reconciliation_status',
        'bank_statement_id',
        'reconciled_by',
        'reconciled_at',
        'reconciliation_notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'fine_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'payment_date' => 'date',
        'reconciled_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->receipt_no)) {
                $model->receipt_no = 'RCP' . date('Y') . str_pad(static::count() + 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function feeStructure(): BelongsTo
    {
        return $this->belongsTo(FeeStructure::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function collectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    public function bankStatement(): BelongsTo
    {
        return $this->belongsTo(BankStatement::class);
    }

    public function reconciledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reconciled_by');
    }

    public function scopePendingReconciliation($query)
    {
        return $query->where('reconciliation_status', 'pending');
    }

    public function scopeReconciled($query)
    {
        return $query->where('reconciliation_status', 'reconciled');
    }

    public function scopeDisputed($query)
    {
        return $query->where('reconciliation_status', 'disputed');
    }

    public function getIsReconciledAttribute()
    {
        return $this->reconciliation_status === 'reconciled';
    }
}