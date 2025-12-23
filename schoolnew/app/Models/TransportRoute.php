<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransportRoute extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'vehicle_id',
		'route_name',
		'start_place',
		'end_place',
		'stops',
		'fare_amount',
		'start_time',
		'end_time',
		'is_active',
	];

	protected $casts = [
		'is_active' => 'boolean',
		'fare_amount' => 'decimal:2',
		'stops' => 'array',
	];

	public function vehicle(): BelongsTo
	{
		return $this->belongsTo(Vehicle::class);
	}

	public function assignments(): HasMany
	{
		return $this->hasMany(RouteAssignment::class);
	}

	public function scopeActive($query)
	{
		return $query->where('is_active', true);
	}
}
