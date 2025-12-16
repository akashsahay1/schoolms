<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TimetablePeriod extends Model
{
    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'type',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function timetables(): HasMany
    {
        return $this->hasMany(Timetable::class, 'period_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('start_time');
    }

    public function getFormattedTimeRangeAttribute()
    {
        return $this->start_time->format('g:i A') . ' - ' . $this->end_time->format('g:i A');
    }
}