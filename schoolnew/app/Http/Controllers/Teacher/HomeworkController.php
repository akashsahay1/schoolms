<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Timetable;
use App\Notifications\HomeworkAssigned;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class HomeworkController extends Controller
{
    protected function getStaff()
    {
        return Staff::where('user_id', Auth::id())->first();
    }

    /**
     * List all homework assigned by this teacher.
     */
    public function index(Request $request)
    {
        $staff = $this->getStaff();
        if (!$staff) {
            return redirect()->route('teacher.dashboard')->with('error', 'Staff profile not found.');
        }

        $homework = Homework::where('teacher_id', Auth::id())
            ->with(['schoolClass', 'section', 'subject'])
            ->withCount('submissions')
            ->latest()
            ->paginate(15);

        return view('teacher.homework.index', compact('staff', 'homework'));
    }

    /**
     * Show form to create homework.
     */
    public function create()
    {
        $staff = $this->getStaff();
        if (!$staff) {
            return redirect()->route('teacher.dashboard')->with('error', 'Staff profile not found.');
        }

        // Get classes this teacher teaches
        $myClassIds = Timetable::where('teacher_id', Auth::id())
            ->pluck('class_id')
            ->unique();

        $classes = SchoolClass::whereIn('id', $myClassIds)->get();
        $subjects = Subject::whereIn('class_id', $myClassIds)->get();

        return view('teacher.homework.create', compact('staff', 'classes', 'subjects'));
    }

    /**
     * Store homework.
     */
    public function store(Request $request)
    {
        $staff = $this->getStaff();
        if (!$staff) {
            return redirect()->route('teacher.dashboard')->with('error', 'Staff profile not found.');
        }

        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'submission_date' => 'required|date|after_or_equal:today',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $validated['teacher_id'] = Auth::id();
        $validated['homework_date'] = now()->toDateString();

        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment')->store('homework', 'public');
        }

        $homework = Homework::create($validated);

        // Notify students in this class
        $studentQuery = Student::where('class_id', $validated['class_id'])->where('status', 'active');
        if (!empty($validated['section_id'])) {
            $studentQuery->where('section_id', $validated['section_id']);
        }
        $studentUserIds = $studentQuery->whereNotNull('user_id')->pluck('user_id');
        $users = \App\Models\User::whereIn('id', $studentUserIds)->get();
        Notification::send($users, new HomeworkAssigned($homework));

        return redirect()->route('teacher.homework.index')
            ->with('success', 'Homework assigned successfully.');
    }

    /**
     * View homework submissions.
     */
    public function submissions(Request $request)
    {
        $staff = $this->getStaff();
        if (!$staff) {
            return redirect()->route('teacher.dashboard')->with('error', 'Staff profile not found.');
        }

        $homeworkList = Homework::where('teacher_id', Auth::id())
            ->with(['schoolClass', 'section', 'subject'])
            ->withCount(['submissions' => function ($q) {
                $q->where('status', 'submitted');
            }])
            ->latest()
            ->get();

        $selectedHomework = null;
        $submissions = collect();

        if ($request->homework_id) {
            $selectedHomework = Homework::where('id', $request->homework_id)
                ->where('teacher_id', Auth::id())
                ->first();

            if ($selectedHomework) {
                $submissions = HomeworkSubmission::where('homework_id', $selectedHomework->id)
                    ->with('student')
                    ->get();
            }
        }

        return view('teacher.homework.submissions', compact('staff', 'homeworkList', 'selectedHomework', 'submissions'));
    }

    /**
     * Grade a submission.
     */
    public function grade(Request $request, HomeworkSubmission $submission)
    {
        $staff = $this->getStaff();
        if (!$staff) {
            return redirect()->route('teacher.dashboard')->with('error', 'Staff profile not found.');
        }

        // Verify this submission belongs to teacher's homework
        $homework = Homework::where('id', $submission->homework_id)
            ->where('teacher_id', Auth::id())
            ->first();

        if (!$homework) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $validated = $request->validate([
            'marks' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string|max:500',
            'status' => 'required|in:evaluated,rejected',
        ]);

        $submission->update($validated);

        return redirect()->back()->with('success', 'Submission graded successfully.');
    }

    /**
     * Get sections for a class (AJAX).
     */
    public function getSections($classId)
    {
        $sections = Section::where('class_id', $classId)->get();
        return response()->json($sections);
    }
}
