<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankStatement extends Model
{
    protected $fillable = [
        'transaction_date',
        'reference_no',
        'description',
        'credit_amount',
        'debit_amount',
        'balance',
        'bank_name',
        'account_number',
        'status',
        'fee_collection_id',
        'matched_by',
        'matched_at',
        'notes',
        'import_batch',
        'imported_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'credit_amount' => 'decimal:2',
        'debit_amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'matched_at' => 'datetime',
    ];

    public function feeCollection(): BelongsTo
    {
        return $this->belongsTo(FeeCollection::class);
    }

    public function matchedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'matched_by');
    }

    public function importedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'imported_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeMatched($query)
    {
        return $query->where('status', 'matched');
    }

    public function scopeUnmatched($query)
    {
        return $query->where('status', 'unmatched');
    }

    public function scopeCredits($query)
    {
        return $query->where('credit_amount', '>', 0);
    }

    public function scopeByBatch($query, $batch)
    {
        return $query->where('import_batch', $batch);
    }

    public function getAmountAttribute()
    {
        return $this->credit_amount > 0 ? $this->credit_amount : -$this->debit_amount;
    }

    public function getIsMatchedAttribute()
    {
        return $this->status === 'matched';
    }
}
