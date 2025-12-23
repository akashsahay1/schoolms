<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomeworkSubmission extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'homework_id',
		'student_id',
		'submitted_date',
		'submission_text',
		'attachment',
		'marks_obtained',
		'remarks',
		'status',
		'evaluated_by',
		'evaluated_at',
	];

	protected $casts = [
		'submitted_date' => 'datetime',
		'evaluated_at' => 'datetime',
		'marks_obtained' => 'integer',
	];

	const STATUS_PENDING = 'pending';
	const STATUS_SUBMITTED = 'submitted';
	const STATUS_EVALUATED = 'evaluated';
	const STATUS_LATE = 'late';

	public function homework(): BelongsTo
	{
		return $this->belongsTo(Homework::class);
	}

	public function student(): BelongsTo
	{
		return $this->belongsTo(Student::class);
	}

	public function evaluatedBy(): BelongsTo
	{
		return $this->belongsTo(User::class, 'evaluated_by');
	}

	public function scopeSubmitted($query)
	{
		return $query->whereIn('status', [self::STATUS_SUBMITTED, self::STATUS_EVALUATED, self::STATUS_LATE]);
	}

	public function scopePending($query)
	{
		return $query->where('status', self::STATUS_PENDING);
	}

	public function scopeEvaluated($query)
	{
		return $query->where('status', self::STATUS_EVALUATED);
	}
}
