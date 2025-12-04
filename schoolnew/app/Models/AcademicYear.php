<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public static function getActive(): ?self
    {
        return self::where('is_active', true)->first();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
