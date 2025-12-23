<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
