<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Homework extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'academic_year_id',
		'class_id',
		'section_id',
		'subject_id',
		'teacher_id',
		'title',
		'description',
		'homework_date',
		'submission_date',
		'attachment',
		'max_marks',
		'is_active',
	];

	protected $casts = [
		'homework_date' => 'date',
		'submission_date' => 'date',
		'is_active' => 'boolean',
		'max_marks' => 'integer',
	];

	public function academicYear(): BelongsTo
	{
		return $this->belongsTo(AcademicYear::class);
	}

	public function schoolClass(): BelongsTo
	{
		return $this->belongsTo(SchoolClass::class, 'class_id');
	}

	public function section(): BelongsTo
	{
		return $this->belongsTo(Section::class);
	}

	public function subject(): BelongsTo
	{
		return $this->belongsTo(Subject::class);
	}

	public function teacher(): BelongsTo
	{
		return $this->belongsTo(User::class, 'teacher_id');
	}

	public function submissions(): HasMany
	{
		return $this->hasMany(HomeworkSubmission::class);
	}

	public function scopeActive($query)
	{
		return $query->where('is_active', true);
	}

	public function scopeForClass($query, $classId)
	{
		return $query->where('class_id', $classId);
	}

	public function scopeForSection($query, $sectionId)
	{
		return $query->where('section_id', $sectionId);
	}

	public function getIsOverdueAttribute()
	{
		return $this->submission_date < now();
	}
}
