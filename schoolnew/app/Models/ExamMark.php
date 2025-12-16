<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamMark extends Model
{
    protected $fillable = [
        'exam_id',
        'student_id',
        'subject_id',
        'marks_obtained',
        'full_marks',
        'grade',
        'remarks',
    ];

    protected $casts = [
        'marks_obtained' => 'decimal:2',
        'full_marks' => 'decimal:2',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function getPercentageAttribute()
    {
        if ($this->full_marks == 0) {
            return 0;
        }
        return round(($this->marks_obtained / $this->full_marks) * 100, 2);
    }

    public function calculateGrade()
    {
        $percentage = $this->percentage;
        
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C+';
        if ($percentage >= 40) return 'C';
        if ($percentage >= 33) return 'D';
        return 'F';
    }
}
