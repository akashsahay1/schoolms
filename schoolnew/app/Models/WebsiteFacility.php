<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteFacility extends Model
{
    protected $table = 'website_facilities';

    protected $fillable = [
        'title',
        'description',
        'icon',
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
}
