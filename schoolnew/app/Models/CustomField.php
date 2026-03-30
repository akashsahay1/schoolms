<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomField extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'field_type',
        'options',
        'applies_to',
        'section',
        'is_required',
        'sort_order',
        'is_active',
    ];

    /**
     * Available form sections per scope.
     */
    public const SECTIONS = [
        'student' => [
            'student_information' => 'Student Information',
            'academic_information' => 'Academic Information',
            'contact_information' => 'Contact Information',
            'parent_information' => 'Parent/Guardian Information',
            'aadhaar_details' => 'Aadhaar Card Details',
            'additional_information' => 'Additional Information',
        ],
        'teacher' => [
            'basic_information' => 'Basic Information',
            'contact_information' => 'Contact Information',
            'employment_information' => 'Employment Information',
            'qualifications' => 'Qualifications',
            'aadhaar_pan' => 'Aadhaar & PAN Card',
            'additional_information' => 'Additional Information',
        ],
        'staff' => [
            'basic_information' => 'Basic Information',
            'contact_information' => 'Contact Information',
            'job_details' => 'Job Details',
            'documents' => 'Documents',
            'additional_information' => 'Additional Information',
        ],
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function values()
    {
        return $this->hasMany(CustomFieldValue::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForStudent($query)
    {
        return $query->whereIn('applies_to', ['student', 'all']);
    }

    public function scopeForTeacher($query)
    {
        return $query->whereIn('applies_to', ['teacher', 'all']);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
