<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SmsTemplate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'category',
        'content',
        'variables',
        'is_active',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    const CATEGORIES = [
        'academic' => 'Academic',
        'financial' => 'Financial',
        'attendance' => 'Attendance',
        'communication' => 'Communication',
        'general' => 'General',
    ];

    const DEFAULT_VARIABLES = [
        'student_name' => 'Student Full Name',
        'student_first_name' => 'Student First Name',
        'parent_name' => 'Parent Name',
        'class' => 'Class Name',
        'section' => 'Section Name',
        'school_name' => 'School Name',
        'date' => 'Current Date',
        'amount' => 'Fee Amount',
        'due_date' => 'Due Date',
        'attendance_status' => 'Attendance Status',
        'exam_name' => 'Exam Name',
        'percentage' => 'Result Percentage',
        'grade' => 'Grade',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($template) {
            if (empty($template->slug)) {
                $template->slug = Str::slug($template->name);
            }
        });
    }

    /**
     * Get category label.
     */
    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category ?? 'General';
    }

    /**
     * Parse template with variables.
     */
    public function parse(array $data): string
    {
        $content = $this->content;

        foreach ($data as $key => $value) {
            $content = str_replace('{' . $key . '}', $value, $content);
        }

        return $content;
    }

    /**
     * Scope for active templates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
