<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WebsitePage extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'meta_description',
        'meta_keywords',
        'content',
        'banner_image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function sections(): HasMany
    {
        return $this->hasMany(WebsiteSection::class, 'page_id')->orderBy('sort_order');
    }

    public function activeSections(): HasMany
    {
        return $this->hasMany(WebsiteSection::class, 'page_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    public static function findBySlug(string $slug): ?self
    {
        return self::where('slug', $slug)->where('is_active', true)->first();
    }

    public function getSection(string $key): ?WebsiteSection
    {
        return $this->sections()->where('section_key', $key)->first();
    }
}
