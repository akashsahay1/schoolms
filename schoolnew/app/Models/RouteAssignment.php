<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RouteAssignment extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'transport_route_id',
		'student_id',
		'pickup_point',
		'drop_point',
		'academic_year_id',
		'is_active',
	];

	protected $casts = [
		'is_active' => 'boolean',
	];

	public function route(): BelongsTo
	{
		return $this->belongsTo(TransportRoute::class, 'transport_route_id');
	}

	public function student(): BelongsTo
	{
		return $this->belongsTo(Student::class);
	}

	public function academicYear(): BelongsTo
	{
		return $this->belongsTo(AcademicYear::class);
	}

	public function scopeActive($query)
	{
		return $query->where('is_active', true);
	}
}
