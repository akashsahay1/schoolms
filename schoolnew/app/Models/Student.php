<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'parent_id',
        'class_id',
        'section_id',
        'academic_year_id',
        // Basic Information
        'admission_no',
        'roll_no',
        'first_name',
        'last_name',
        'gender',
        'date_of_birth',
        'blood_group',
        'religion',
        'caste',
        'nationality',
        'mother_tongue',
        // Contact Information
        'email',
        'phone',
        'current_address',
        'permanent_address',
        // Identification
        'national_id',
        'passport_no',
        // Photo & Documents
        'photo',
        'birth_certificate',
        'transfer_certificate',
        // Academic Information
        'admission_date',
        'previous_school',
        'previous_class',
        // Health Information
        'height',
        'weight',
        'medical_conditions',
        'allergies',
        // Transport & Hostel
        'uses_transport',
        'route_id',
        'is_boarder',
        'room_id',
        // Fees & Discount
        'fee_category_id',
        'discount_percent',
        // Status
        'status',
        'leaving_date',
        'leaving_reason',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'admission_date' => 'date',
        'leaving_date' => 'date',
        'uses_transport' => 'boolean',
        'is_boarder' => 'boolean',
        'discount_percent' => 'decimal:2',
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentGuardian::class, 'parent_id');
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function bookIssues()
    {
        return $this->hasMany(BookIssue::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeInSection($query, $sectionId)
    {
        return $query->where('section_id', $sectionId);
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getAgeAttribute(): int
    {
        return $this->date_of_birth?->age ?? 0;
    }

    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return asset('assets/images/user/user.png');
    }
}
