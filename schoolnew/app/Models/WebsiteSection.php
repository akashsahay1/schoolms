<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebsiteSection extends Model
{
    protected $fillable = [
        'page_id',
        'section_key',
        'layout',
        'title',
        'subtitle',
        'content',
        'image',
        'icon',
        'link',
        'link_text',
        'bg_color',
        'sort_order',
        'is_active',
    ];

    public const LAYOUTS = [
        'image-left' => 'Image Left + Content Right (50/50)',
        'image-right' => 'Content Left + Image Right (50/50)',
        'full-width' => 'Full Width Content (no image)',
        'full-image' => 'Full Width Image with Text Overlay',
        'content-center' => 'Centered Content (no image)',
    ];

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(WebsitePage::class, 'page_id');
    }
}
