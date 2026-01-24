<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteGallery extends Model
{
    protected $table = 'website_gallery';

    protected $fillable = [
        'title',
        'description',
        'category',
        'image',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function scopeCategory($query, $category)
    {
        if ($category) {
            return $query->where('category', $category);
        }
        return $query;
    }

    public static function getCategories()
    {
        return self::whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->toArray();
    }
}
