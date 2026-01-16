<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class BookIssue extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'book_id',
		'student_id',
		'staff_id',
		'issued_by',
		'issue_date',
		'due_date',
		'return_date',
		'fine_amount',
		'status',
		'remarks',
	];

	protected $casts = [
		'issue_date' => 'date',
		'due_date' => 'date',
		'return_date' => 'date',
		'fine_amount' => 'decimal:2',
	];

	const STATUS_ISSUED = 'issued';
	const STATUS_RETURNED = 'returned';
	const STATUS_OVERDUE = 'overdue';
	const STATUS_LOST = 'lost';

	public function book(): BelongsTo
	{
		return $this->belongsTo(Book::class);
	}

	public function student(): BelongsTo
	{
		return $this->belongsTo(Student::class);
	}

	public function staff(): BelongsTo
	{
		return $this->belongsTo(Staff::class);
	}

	public function issuedBy(): BelongsTo
	{
		return $this->belongsTo(User::class, 'issued_by');
	}

	public function scopeIssued($query)
	{
		return $query->where('status', self::STATUS_ISSUED);
	}

	public function scopeOverdue($query)
	{
		return $query->where('status', self::STATUS_ISSUED)
			->where('due_date', '<', now());
	}

	public function scopeReturned($query)
	{
		return $query->where('status', self::STATUS_RETURNED);
	}

	/**
	 * Check if the book is overdue
	 */
	public function getIsOverdueAttribute(): bool
	{
		if ($this->status === self::STATUS_RETURNED) {
			return false;
		}
		return $this->due_date < now();
	}

	/**
	 * Get overdue days count
	 */
	public function getOverdueDaysAttribute(): int
	{
		if ($this->status === self::STATUS_RETURNED) {
			// Calculate based on return date
			if ($this->return_date > $this->due_date) {
				return $this->due_date->diffInDays($this->return_date);
			}
			return 0;
		}

		if ($this->due_date >= now()) {
			return 0;
		}

		return $this->due_date->diffInDays(now());
	}

	/**
	 * Calculate fine amount based on overdue days
	 */
	public function calculateFine(): float
	{
		$overdueDays = $this->overdue_days;
		if ($overdueDays <= 0) {
			return 0;
		}

		// Get fine per day from settings (default: 2 rupees per day)
		$finePerDay = Setting::get('library_fine_per_day', 2);

		return $overdueDays * $finePerDay;
	}

	/**
	 * Get calculated fine attribute
	 */
	public function getCalculatedFineAttribute(): float
	{
		return $this->calculateFine();
	}

	/**
	 * Get days remaining until due date
	 */
	public function getDaysRemainingAttribute(): int
	{
		if ($this->status === self::STATUS_RETURNED) {
			return 0;
		}

		if ($this->due_date < now()) {
			return 0;
		}

		return now()->diffInDays($this->due_date);
	}
}
