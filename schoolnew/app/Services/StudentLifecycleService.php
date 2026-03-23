<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\SchoolClass;
use App\Models\Student;

class StudentLifecycleService
{
    /**
     * Auto-graduate Class 12 students who have passed their final exam.
     * Session-aware: only processes students in the given academic year.
     * Does NOT overwrite transferred/expelled students.
     */
    public static function autoGraduateClass12(?int $examId = null, ?int $academicYearId = null): array
    {
        $academicYear = $academicYearId
            ? AcademicYear::find($academicYearId)
            : AcademicYear::getActive();

        if (!$academicYear) {
            return ['graduated' => 0, 'skipped' => 0, 'message' => 'No active academic year found.'];
        }

        $class12Ids = SchoolClass::where('name', 'like', 'Class 12%')
            ->pluck('id')
            ->toArray();

        if (empty($class12Ids)) {
            return ['graduated' => 0, 'skipped' => 0, 'message' => 'No Class 12 found in the system.'];
        }

        // Only active students in Class 12 for this academic year
        $students = Student::where('status', 'active')
            ->where('academic_year_id', $academicYear->id)
            ->whereIn('class_id', $class12Ids)
            ->get();

        if ($students->isEmpty()) {
            return ['graduated' => 0, 'skipped' => 0, 'message' => 'No active Class 12 students found for session ' . $academicYear->name . '.'];
        }

        // Find exams for this academic year if no specific exam given
        $examIds = null;
        if (!$examId) {
            $examIds = Exam::where('academic_year_id', $academicYear->id)
                ->whereIn('class_id', $class12Ids)
                ->pluck('id')
                ->toArray();
        }

        $leavingDate = $academicYear->end_date ?? now();
        $graduated = 0;
        $skipped = 0;

        foreach ($students as $student) {
            if (self::hasPassedExam($student->id, $examId, $examIds)) {
                $student->update([
                    'status' => 'graduated',
                    'leaving_date' => $leavingDate,
                    'leaving_reason' => 'Completed Class 12 - Passed (' . $academicYear->name . ')',
                ]);
                $graduated++;
            } else {
                $skipped++;
            }
        }

        return [
            'graduated' => $graduated,
            'skipped' => $skipped,
            'message' => "Session {$academicYear->name}: Graduated {$graduated} student(s). Skipped {$skipped} (not passed or no marks).",
        ];
    }

    /**
     * Check if a student has passed (overall percentage >= 33%).
     * Session-aware: checks marks from specific exam or exams within a session.
     */
    public static function hasPassedExam(int $studentId, ?int $examId = null, ?array $examIds = null): bool
    {
        $query = ExamMark::where('student_id', $studentId);

        if ($examId) {
            $query->where('exam_id', $examId);
        } elseif ($examIds && !empty($examIds)) {
            $query->whereIn('exam_id', $examIds);
        }

        $marks = $query->get();

        if ($marks->isEmpty()) {
            return false;
        }

        $totalMarks = $marks->sum('marks_obtained');
        $totalFull = $marks->sum('full_marks');

        if ($totalFull <= 0) {
            return false;
        }

        return ($totalMarks / $totalFull) * 100 >= 33;
    }

    /**
     * Manually mark a student as graduated.
     */
    public static function markAsGraduated(Student $student, ?string $reason = null): bool
    {
        if ($student->status !== 'active') {
            return false;
        }

        $academicYear = AcademicYear::find($student->academic_year_id);

        $student->update([
            'status' => 'graduated',
            'leaving_date' => $academicYear?->end_date ?? now(),
            'leaving_reason' => $reason ?? 'Completed education (' . ($academicYear?->name ?? '') . ')',
        ]);

        return true;
    }

    /**
     * Manually mark a student as transferred.
     */
    public static function markAsTransferred(Student $student, ?string $reason = null, ?string $leavingDate = null): bool
    {
        if ($student->status !== 'active') {
            return false;
        }

        $student->update([
            'status' => 'transferred',
            'leaving_date' => $leavingDate ?? now(),
            'leaving_reason' => $reason ?? 'Transferred to another school',
        ]);

        return true;
    }

    /**
     * Get session-aware exam results for a student.
     * Returns marks from exams in the student's academic year.
     */
    public static function getSessionResults(Student $student, ?int $examId = null): array
    {
        if ($examId) {
            $exam = Exam::with('examType')->find($examId);
        } else {
            // Find the latest exam for student's academic year and class
            $exam = Exam::with('examType')
                ->where('academic_year_id', $student->academic_year_id)
                ->where('class_id', $student->class_id)
                ->latest('start_date')
                ->first();

            // Fallback: any exam where student has marks
            if (!$exam) {
                $latestExamId = ExamMark::where('student_id', $student->id)
                    ->orderBy('exam_id', 'desc')
                    ->value('exam_id');
                $exam = $latestExamId ? Exam::with('examType')->find($latestExamId) : null;
            }
        }

        if (!$exam) {
            return ['exam' => null, 'marks' => collect(), 'has_data' => false];
        }

        $marks = ExamMark::where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->with('subject')
            ->orderBy('subject_id')
            ->get();

        if ($marks->isEmpty()) {
            return ['exam' => $exam, 'marks' => collect(), 'has_data' => false];
        }

        $totalMarks = $marks->sum('marks_obtained');
        $totalFull = $marks->sum('full_marks');
        $percentage = $totalFull > 0 ? round(($totalMarks / $totalFull) * 100, 2) : 0;

        return [
            'exam' => $exam,
            'marks' => $marks,
            'total_marks' => $totalMarks,
            'total_full' => $totalFull,
            'percentage' => $percentage,
            'grade' => self::calculateGrade($percentage),
            'result' => $percentage >= 33 ? 'PASS' : 'FAIL',
            'has_data' => true,
        ];
    }

    private static function calculateGrade(float $percentage): string
    {
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
