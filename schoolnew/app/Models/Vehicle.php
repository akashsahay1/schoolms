<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'vehicle_no',
		'vehicle_model',
		'year_made',
		'registration_no',
		'chasis_no',
		'max_seating_capacity',
		'driver_name',
		'driver_license',
		'driver_contact',
		'status',
		'note',
	];

	const STATUS_ACTIVE = 'active';
	const STATUS_INACTIVE = 'inactive';
	const STATUS_MAINTENANCE = 'maintenance';

	public function routes(): HasMany
	{
		return $this->hasMany(TransportRoute::class);
	}

	public function scopeActive($query)
	{
		return $query->where('status', self::STATUS_ACTIVE);
	}
}
