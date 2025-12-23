<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'book_category_id',
		'title',
		'author',
		'isbn',
		'publisher',
		'edition',
		'published_year',
		'total_copies',
		'available_copies',
		'price',
		'rack_no',
		'description',
		'cover_image',
		'is_active',
	];

	protected $casts = [
		'is_active' => 'boolean',
		'total_copies' => 'integer',
		'available_copies' => 'integer',
		'price' => 'decimal:2',
		'published_year' => 'integer',
	];

	public function category(): BelongsTo
	{
		return $this->belongsTo(BookCategory::class, 'book_category_id');
	}

	public function issues(): HasMany
	{
		return $this->hasMany(BookIssue::class);
	}

	public function scopeActive($query)
	{
		return $query->where('is_active', true);
	}

	public function scopeAvailable($query)
	{
		return $query->where('available_copies', '>', 0);
	}
}
