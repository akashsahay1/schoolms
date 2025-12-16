<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\ExamType;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::with(['examType', 'academicYear'])
            ->latest()
            ->get();
            
        return view('admin.exams.index', compact('exams'));
    }

    public function create()
    {
        $examTypes = ExamType::where('is_active', true)->orderBy('order')->get();
        $academicYears = AcademicYear::orderBy('name')->get();
        
        return view('admin.exams.create', compact('examTypes', 'academicYears'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'exam_type_id' => 'required|exists:exam_types,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);

        Exam::create($validated);

        return redirect()->route('admin.exams.index')
            ->with('success', 'Exam created successfully!');
    }

    public function edit(Exam $exam)
    {
        $examTypes = ExamType::where('is_active', true)->orderBy('order')->get();
        $academicYears = AcademicYear::orderBy('name')->get();
        
        return view('admin.exams.edit', compact('exam', 'examTypes', 'academicYears'));
    }

    public function update(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'exam_type_id' => 'required|exists:exam_types,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);

        $exam->update($validated);

        return redirect()->route('admin.exams.index')
            ->with('success', 'Exam updated successfully!');
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();

        return redirect()->route('admin.exams.index')
            ->with('success', 'Exam deleted successfully!');
    }

    public function marks(Request $request)
    {
        $exams = Exam::with('examType', 'academicYear')->where('is_active', true)->get();
        $classes = \App\Models\SchoolClass::ordered()->get();
        $subjects = \App\Models\Subject::ordered()->get();
        $students = collect();
        $selectedExam = null;
        $selectedClass = null;
        $selectedSubject = null;

        if ($request->has('exam_id') && $request->has('class_id') && $request->has('subject_id')) {
            $selectedExam = Exam::find($request->exam_id);
            $selectedClass = \App\Models\SchoolClass::find($request->class_id);
            $selectedSubject = \App\Models\Subject::find($request->subject_id);
            
            if ($selectedExam && $selectedClass && $selectedSubject) {
                $students = \App\Models\Student::where('class_id', $request->class_id)
                    ->where('status', 'active')
                    ->orderBy('roll_no')
                    ->get();
            }
        }

        return view('admin.exams.marks', compact('exams', 'classes', 'subjects', 'students', 'selectedExam', 'selectedClass', 'selectedSubject'));
    }

    public function storeMarks(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'subject_id' => 'required|exists:subjects,id',
            'full_marks' => 'required|numeric|min:1',
            'marks.*' => 'required|numeric|min:0|max:' . $request->full_marks,
        ]);

        foreach ($request->marks as $studentId => $marksObtained) {
            $examMark = \App\Models\ExamMark::updateOrCreate(
                [
                    'exam_id' => $request->exam_id,
                    'student_id' => $studentId,
                    'subject_id' => $request->subject_id,
                ],
                [
                    'marks_obtained' => $marksObtained,
                    'full_marks' => $request->full_marks,
                ]
            );

            // Calculate and update grade
            $examMark->grade = $examMark->calculateGrade();
            $examMark->save();
        }

        return back()->with('success', 'Marks saved successfully!');
    }

    public function results(Request $request)
    {
        $exams = Exam::with('examType', 'academicYear')->latest()->get();
        $classes = \App\Models\SchoolClass::ordered()->get();
        $sections = collect();
        $results = collect();
        $selectedExam = null;
        $selectedClass = null;
        $selectedSection = null;
        $subjects = collect();

        if ($request->filled('exam_id') && $request->filled('class_id')) {
            $selectedExam = Exam::with('examType')->find($request->exam_id);
            $selectedClass = \App\Models\SchoolClass::find($request->class_id);
            $sections = \App\Models\Section::where('class_id', $request->class_id)->get();

            if ($request->filled('section_id')) {
                $selectedSection = \App\Models\Section::find($request->section_id);
            }

            // Get subjects for this class
            $subjects = \App\Models\Subject::active()->ordered()->get();

            // Get students with their marks
            $studentsQuery = \App\Models\Student::where('class_id', $request->class_id)
                ->where('status', 'active');

            if ($selectedSection) {
                $studentsQuery->where('section_id', $selectedSection->id);
            }

            $students = $studentsQuery->orderBy('roll_no')->get();

            // Get marks for each student
            try {
                foreach ($students as $student) {
                    $studentMarks = ExamMark::where('exam_id', $request->exam_id)
                        ->where('student_id', $student->id)
                        ->get()
                        ->keyBy('subject_id');

                    $totalMarks = 0;
                    $totalFullMarks = 0;
                    $subjectCount = 0;

                    foreach ($subjects as $subject) {
                        if (isset($studentMarks[$subject->id])) {
                            $totalMarks += $studentMarks[$subject->id]->marks_obtained;
                            $totalFullMarks += $studentMarks[$subject->id]->full_marks;
                            $subjectCount++;
                        }
                    }

                    $percentage = $totalFullMarks > 0 ? round(($totalMarks / $totalFullMarks) * 100, 2) : 0;

                    $results->push([
                        'student' => $student,
                        'marks' => $studentMarks,
                        'total_marks' => $totalMarks,
                        'total_full_marks' => $totalFullMarks,
                        'percentage' => $percentage,
                        'grade' => $this->calculateOverallGrade($percentage),
                        'result' => $percentage >= 33 ? 'Pass' : 'Fail',
                    ]);
                }

                // Sort by percentage (rank)
                $results = $results->sortByDesc('percentage')->values();

                // Add rank
                $results = $results->map(function ($item, $index) {
                    $item['rank'] = $index + 1;
                    return $item;
                });
            } catch (\Exception $e) {
                // Table may not exist yet - return empty results
                $results = collect();
            }
        }

        return view('admin.exams.results', compact(
            'exams', 'classes', 'sections', 'results', 'subjects',
            'selectedExam', 'selectedClass', 'selectedSection'
        ));
    }

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

    public function reportCards(Request $request)
    {
        $exams = Exam::with('examType', 'academicYear')->latest()->get();
        $classes = \App\Models\SchoolClass::ordered()->get();
        $sections = collect();
        $students = collect();
        $selectedExam = null;
        $selectedClass = null;
        $selectedSection = null;
        $selectedStudent = null;
        $reportCardData = null;

        if ($request->filled('exam_id') && $request->filled('class_id')) {
            $selectedExam = Exam::with(['examType', 'academicYear'])->find($request->exam_id);
            $selectedClass = \App\Models\SchoolClass::find($request->class_id);
            $sections = \App\Models\Section::where('class_id', $request->class_id)->get();

            if ($request->filled('section_id')) {
                $selectedSection = \App\Models\Section::find($request->section_id);
                $students = \App\Models\Student::where('class_id', $request->class_id)
                    ->where('section_id', $request->section_id)
                    ->where('status', 'active')
                    ->orderBy('roll_no')
                    ->get();
            }

            if ($request->filled('student_id')) {
                $selectedStudent = \App\Models\Student::with(['schoolClass', 'section', 'parent'])
                    ->find($request->student_id);

                if ($selectedStudent) {
                    try {
                        $subjects = \App\Models\Subject::active()->ordered()->get();
                        $marks = ExamMark::where('exam_id', $request->exam_id)
                            ->where('student_id', $selectedStudent->id)
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
                                ];
                                $totalMarks += $mark->marks_obtained;
                                $totalFullMarks += $mark->full_marks;
                            }
                        }

                        $overallPercentage = $totalFullMarks > 0 ? round(($totalMarks / $totalFullMarks) * 100, 2) : 0;

                        $reportCardData = [
                            'student' => $selectedStudent,
                            'exam' => $selectedExam,
                            'subjects' => $subjectResults,
                            'total_marks' => $totalMarks,
                            'total_full_marks' => $totalFullMarks,
                            'percentage' => $overallPercentage,
                            'grade' => $this->calculateOverallGrade($overallPercentage),
                            'result' => $overallPercentage >= 33 ? 'Pass' : 'Fail',
                        ];
                    } catch (\Exception $e) {
                        // Table may not exist yet
                        $reportCardData = null;
                    }
                }
            }
        }

        return view('admin.exams.report-cards', compact(
            'exams', 'classes', 'sections', 'students',
            'selectedExam', 'selectedClass', 'selectedSection', 'selectedStudent',
            'reportCardData'
        ));
    }

    public function getSections($classId)
    {
        $sections = \App\Models\Section::where('class_id', $classId)->get(['id', 'name']);
        return response()->json($sections);
    }

    public function getStudents($classId, $sectionId)
    {
        $students = \App\Models\Student::where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->where('status', 'active')
            ->orderBy('roll_no')
            ->get(['id', 'first_name', 'last_name', 'roll_no', 'admission_no']);
        return response()->json($students);
    }
}