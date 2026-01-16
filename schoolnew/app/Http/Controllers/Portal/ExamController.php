<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    /**
     * Get the authenticated student.
     */
    private function getStudent()
    {
        return Student::where('user_id', Auth::id())->first();
    }

    /**
     * Calculate overall grade based on percentage.
     */
    private function calculateOverallGrade($percentage)
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

    /**
     * Display list of exams for the student.
     */
    public function index()
    {
        $student = $this->getStudent();

        if (!$student) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Student profile not found.');
        }

        // Get published exams for student's class
        $exams = Exam::with(['examType', 'academicYear'])
            ->where('class_id', $student->class_id)
            ->where('is_published', true)
            ->orderBy('start_date', 'desc')
            ->get();

        // Calculate if student has results for each exam
        foreach ($exams as $exam) {
            $exam->has_results = ExamMark::where('exam_id', $exam->id)
                ->where('student_id', $student->id)
                ->exists();
        }

        return view('portal.exams.index', compact('student', 'exams'));
    }

    /**
     * Display exam results for the student.
     */
    public function results(Request $request)
    {
        $student = $this->getStudent();

        if (!$student) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Student profile not found.');
        }

        // Get published exams for student's class
        $exams = Exam::with(['examType', 'academicYear'])
            ->where('class_id', $student->class_id)
            ->where('is_published', true)
            ->orderBy('start_date', 'desc')
            ->get();

        $selectedExam = null;
        $result = null;

        if ($request->filled('exam_id')) {
            $selectedExam = Exam::with(['examType', 'academicYear'])
                ->where('id', $request->exam_id)
                ->where('class_id', $student->class_id)
                ->where('is_published', true)
                ->first();

            if ($selectedExam) {
                // Get all subjects
                $subjects = Subject::active()->ordered()->get();

                // Get student's marks
                $marks = ExamMark::where('exam_id', $selectedExam->id)
                    ->where('student_id', $student->id)
                    ->get()
                    ->keyBy('subject_id');

                $totalMarks = 0;
                $totalFullMarks = 0;
                $subjectResults = [];

                foreach ($subjects as $subject) {
                    if (isset($marks[$subject->id])) {
                        $mark = $marks[$subject->id];
                        $subjectResults[] = [
                            'subject' => $subject,
                            'marks_obtained' => $mark->marks_obtained,
                            'full_marks' => $mark->full_marks,
                            'percentage' => $mark->percentage,
                            'grade' => $mark->grade,
                            'passed' => $mark->percentage >= 33,
                        ];
                        $totalMarks += $mark->marks_obtained;
                        $totalFullMarks += $mark->full_marks;
                    }
                }

                $overallPercentage = $totalFullMarks > 0 ? round(($totalMarks / $totalFullMarks) * 100, 2) : 0;

                // Calculate rank among classmates
                $classResults = $this->getClassResults($selectedExam->id, $student->class_id, $student->section_id);
                $rank = 1;
                foreach ($classResults as $classResult) {
                    if ($classResult['student_id'] == $student->id) {
                        break;
                    }
                    $rank++;
                }

                $result = [
                    'subjects' => $subjectResults,
                    'total_marks' => $totalMarks,
                    'total_full_marks' => $totalFullMarks,
                    'percentage' => $overallPercentage,
                    'grade' => $this->calculateOverallGrade($overallPercentage),
                    'result' => $overallPercentage >= 33 ? 'Pass' : 'Fail',
                    'rank' => $rank,
                    'total_students' => count($classResults),
                ];
            }
        }

        return view('portal.exams.results', compact('student', 'exams', 'selectedExam', 'result'));
    }

    /**
     * Get class results for ranking.
     */
    private function getClassResults($examId, $classId, $sectionId = null)
    {
        $studentsQuery = Student::where('class_id', $classId)
            ->where('status', 'active');

        if ($sectionId) {
            $studentsQuery->where('section_id', $sectionId);
        }

        $students = $studentsQuery->get();
        $subjects = Subject::active()->ordered()->get();
        $results = [];

        foreach ($students as $student) {
            $marks = ExamMark::where('exam_id', $examId)
                ->where('student_id', $student->id)
                ->get()
                ->keyBy('subject_id');

            $totalMarks = 0;
            $totalFullMarks = 0;

            foreach ($subjects as $subject) {
                if (isset($marks[$subject->id])) {
                    $totalMarks += $marks[$subject->id]->marks_obtained;
                    $totalFullMarks += $marks[$subject->id]->full_marks;
                }
            }

            if ($totalFullMarks > 0) {
                $results[] = [
                    'student_id' => $student->id,
                    'percentage' => round(($totalMarks / $totalFullMarks) * 100, 2),
                ];
            }
        }

        // Sort by percentage descending
        usort($results, function ($a, $b) {
            return $b['percentage'] <=> $a['percentage'];
        });

        return $results;
    }

    /**
     * Display report card for the student.
     */
    public function reportCard(Request $request)
    {
        $student = $this->getStudent();

        if (!$student) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Student profile not found.');
        }

        // Load student with relationships
        $student->load(['schoolClass', 'section', 'parent']);

        // Get published exams for student's class
        $exams = Exam::with(['examType', 'academicYear'])
            ->where('class_id', $student->class_id)
            ->where('is_published', true)
            ->orderBy('start_date', 'desc')
            ->get();

        $selectedExam = null;
        $reportCardData = null;

        if ($request->filled('exam_id')) {
            $selectedExam = Exam::with(['examType', 'academicYear'])
                ->where('id', $request->exam_id)
                ->where('class_id', $student->class_id)
                ->where('is_published', true)
                ->first();

            if ($selectedExam) {
                $subjects = Subject::active()->ordered()->get();
                $marks = ExamMark::where('exam_id', $selectedExam->id)
                    ->where('student_id', $student->id)
                    ->get()
                    ->keyBy('subject_id');

                $totalMarks = 0;
                $totalFullMarks = 0;
                $subjectResults = [];

                foreach ($subjects as $subject) {
                    if (isset($marks[$subject->id])) {
                        $mark = $marks[$subject->id];
                        $subjectResults[] = [
                            'subject' => $subject,
                            'marks_obtained' => $mark->marks_obtained,
                            'full_marks' => $mark->full_marks,
                            'percentage' => $mark->percentage,
                            'grade' => $mark->grade,
                            'remarks' => $this->getRemarks($mark->percentage),
                        ];
                        $totalMarks += $mark->marks_obtained;
                        $totalFullMarks += $mark->full_marks;
                    }
                }

                $overallPercentage = $totalFullMarks > 0 ? round(($totalMarks / $totalFullMarks) * 100, 2) : 0;

                // Get rank
                $classResults = $this->getClassResults($selectedExam->id, $student->class_id, $student->section_id);
                $rank = 1;
                foreach ($classResults as $classResult) {
                    if ($classResult['student_id'] == $student->id) {
                        break;
                    }
                    $rank++;
                }

                $reportCardData = [
                    'student' => $student,
                    'exam' => $selectedExam,
                    'subjects' => $subjectResults,
                    'total_marks' => $totalMarks,
                    'total_full_marks' => $totalFullMarks,
                    'percentage' => $overallPercentage,
                    'grade' => $this->calculateOverallGrade($overallPercentage),
                    'result' => $overallPercentage >= 33 ? 'Pass' : 'Fail',
                    'rank' => $rank,
                    'total_students' => count($classResults),
                ];
            }
        }

        return view('portal.exams.report-card', compact('student', 'exams', 'selectedExam', 'reportCardData'));
    }

    /**
     * Get remarks based on percentage.
     */
    private function getRemarks($percentage)
    {
        if ($percentage >= 90) return 'Excellent';
        if ($percentage >= 80) return 'Very Good';
        if ($percentage >= 70) return 'Good';
        if ($percentage >= 60) return 'Satisfactory';
        if ($percentage >= 50) return 'Average';
        if ($percentage >= 40) return 'Below Average';
        if ($percentage >= 33) return 'Needs Improvement';
        return 'Fail';
    }
}
