<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookCategory extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'name',
		'description',
		'is_active',
	];

	protected $casts = [
		'is_active' => 'boolean',
	];

	public function books(): HasMany
	{
		return $this->hasMany(Book::class);
	}

	public function scopeActive($query)
	{
		return $query->where('is_active', true);
	}
}
