<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Timetable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    protected function getStaff()
    {
        return Staff::where('user_id', Auth::id())->first();
    }

    /**
     * View exam schedule.
     */
    public function schedule(Request $request)
    {
        $staff = $this->getStaff();
        if (!$staff) {
            return redirect()->route('teacher.dashboard')->with('error', 'Staff profile not found.');
        }

        // Get classes and subjects this teacher teaches
        $myClassIds = Timetable::where('teacher_id', $staff->id)
            ->pluck('class_id')
            ->unique();

        $mySubjectIds = Timetable::where('teacher_id', $staff->id)
            ->pluck('subject_id')
            ->unique();

        $exams = Exam::with(['schoolClass', 'subject'])
            ->whereIn('class_id', $myClassIds)
            ->whereIn('subject_id', $mySubjectIds)
            ->orderBy('exam_date')
            ->get();

        return view('teacher.exams.schedule', compact('staff', 'exams'));
    }

    /**
     * Show marks entry form.
     */
    public function marks(Request $request)
    {
        $staff = $this->getStaff();
        if (!$staff) {
            return redirect()->route('teacher.dashboard')->with('error', 'Staff profile not found.');
        }

        // Get classes and subjects this teacher teaches
        $myClassIds = Timetable::where('teacher_id', $staff->id)
            ->pluck('class_id')
            ->unique();

        $exams = Exam::whereIn('class_id', $myClassIds)
            ->with(['schoolClass', 'subject'])
            ->orderBy('exam_date', 'desc')
            ->get();

        $students = collect();
        $existingMarks = collect();
        $selectedExam = null;

        if ($request->exam_id) {
            $selectedExam = Exam::with(['schoolClass', 'section', 'subject'])->find($request->exam_id);

            if ($selectedExam) {
                $query = Student::where('class_id', $selectedExam->class_id)
                    ->where('status', 'active');

                if ($selectedExam->section_id) {
                    $query->where('section_id', $selectedExam->section_id);
                }

                $students = $query->orderBy('roll_number')->orderBy('first_name')->get();

                // Get existing marks
                $existingMarks = ExamMark::where('exam_id', $selectedExam->id)
                    ->get()
                    ->keyBy('student_id');
            }
        }

        return view('teacher.exams.marks', compact('staff', 'exams', 'students', 'existingMarks', 'selectedExam'));
    }

    /**
     * Store marks.
     */
    public function storeMarks(Request $request)
    {
        $staff = $this->getStaff();
        if (!$staff) {
            return redirect()->route('teacher.dashboard')->with('error', 'Staff profile not found.');
        }

        $validated = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'marks' => 'required|array',
            'marks.*' => 'nullable|numeric|min:0',
        ]);

        $exam = Exam::findOrFail($validated['exam_id']);

        foreach ($validated['marks'] as $studentId => $marks) {
            if ($marks !== null) {
                ExamMark::updateOrCreate(
                    [
                        'exam_id' => $exam->id,
                        'student_id' => $studentId,
                    ],
                    [
                        'marks_obtained' => $marks,
                        'total_marks' => $exam->total_marks,
                        'is_absent' => false,
                    ]
                );
            }
        }

        return redirect()->back()->with('success', 'Marks saved successfully.');
    }
}
