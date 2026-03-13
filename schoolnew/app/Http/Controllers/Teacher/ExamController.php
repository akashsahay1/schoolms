<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Exam;
use App\Models\ExamSchedule;
use App\Models\ExamMark;
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

        $schedules = ExamSchedule::with(['exam', 'schoolClass', 'subject'])
            ->whereIn('class_id', $myClassIds)
            ->whereIn('subject_id', $mySubjectIds)
            ->whereHas('exam', function ($q) {
                $q->where('is_active', true);
            })
            ->orderBy('exam_date')
            ->get();

        return view('teacher.exams.schedule', compact('staff', 'schedules'));
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

        $mySubjectIds = Timetable::where('teacher_id', $staff->id)
            ->pluck('subject_id')
            ->unique();

        $schedules = ExamSchedule::with(['exam', 'schoolClass', 'subject'])
            ->whereIn('class_id', $myClassIds)
            ->whereIn('subject_id', $mySubjectIds)
            ->whereHas('exam', function ($q) {
                $q->where('is_active', true);
            })
            ->orderBy('exam_date', 'desc')
            ->get();

        $students = collect();
        $existingMarks = collect();
        $selectedSchedule = null;

        if ($request->schedule_id) {
            $selectedSchedule = ExamSchedule::with(['exam', 'schoolClass', 'subject'])->find($request->schedule_id);

            if ($selectedSchedule) {
                $students = Student::where('class_id', $selectedSchedule->class_id)
                    ->where('status', 'active')
                    ->orderBy('roll_no')
                    ->orderBy('first_name')
                    ->get();

                // Get existing marks
                $existingMarks = ExamMark::where('exam_id', $selectedSchedule->exam_id)
                    ->where('subject_id', $selectedSchedule->subject_id)
                    ->get()
                    ->keyBy('student_id');
            }
        }

        return view('teacher.exams.marks', compact('staff', 'schedules', 'students', 'existingMarks', 'selectedSchedule'));
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
            'schedule_id' => 'required|exists:exam_schedules,id',
            'marks' => 'required|array',
            'marks.*' => 'nullable|numeric|min:0',
        ]);

        $schedule = ExamSchedule::findOrFail($validated['schedule_id']);

        foreach ($validated['marks'] as $studentId => $marks) {
            if ($marks !== null) {
                ExamMark::updateOrCreate(
                    [
                        'exam_id' => $schedule->exam_id,
                        'student_id' => $studentId,
                        'subject_id' => $schedule->subject_id,
                    ],
                    [
                        'marks_obtained' => $marks,
                        'full_marks' => $schedule->full_marks,
                    ]
                );
            }
        }

        return redirect()->back()->with('success', 'Marks saved successfully.');
    }
}
