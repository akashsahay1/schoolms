<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Portal\Traits\PortalStudentTrait;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HomeworkController extends Controller
{
    use PortalStudentTrait;

    /**
     * Get the authenticated student.
     */
    private function getStudent()
    {
        return $this->getCurrentStudent();
    }

    /**
     * Display all homework for the student.
     */
    public function index(Request $request)
    {
        $student = $this->getStudent();

        if (!$student) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Student profile not found.');
        }

        $query = Homework::with(['subject', 'teacher', 'academicYear'])
            ->where('class_id', $student->class_id)
            ->where(function ($q) use ($student) {
                $q->whereNull('section_id')
                    ->orWhere('section_id', $student->section_id);
            })
            ->where('is_active', true)
            ->orderBy('homework_date', 'desc');

        // Apply filters
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('status')) {
            if ($request->status == 'pending') {
                $query->whereDoesntHave('submissions', function ($q) use ($student) {
                    $q->where('student_id', $student->id)
                        ->whereIn('status', ['submitted', 'evaluated']);
                });
            } elseif ($request->status == 'submitted') {
                $query->whereHas('submissions', function ($q) use ($student) {
                    $q->where('student_id', $student->id)
                        ->whereIn('status', ['submitted', 'evaluated']);
                });
            }
        }

        $homeworks = $query->paginate(10);

        // Get student's submissions for all homework
        $submissionsByHomework = HomeworkSubmission::where('student_id', $student->id)
            ->whereIn('homework_id', $homeworks->pluck('id'))
            ->get()
            ->keyBy('homework_id');

        // Get subjects for filter
        $subjects = \App\Models\Subject::active()->ordered()->get();

        // Stats
        $stats = [
            'total' => Homework::where('class_id', $student->class_id)
                ->where('is_active', true)
                ->count(),
            'pending' => Homework::where('class_id', $student->class_id)
                ->where('is_active', true)
                ->whereDoesntHave('submissions', function ($q) use ($student) {
                    $q->where('student_id', $student->id)
                        ->whereIn('status', ['submitted', 'evaluated']);
                })
                ->count(),
            'submitted' => HomeworkSubmission::where('student_id', $student->id)
                ->whereIn('status', ['submitted', 'evaluated'])
                ->count(),
            'overdue' => Homework::where('class_id', $student->class_id)
                ->where('is_active', true)
                ->where('submission_date', '<', now())
                ->whereDoesntHave('submissions', function ($q) use ($student) {
                    $q->where('student_id', $student->id)
                        ->whereIn('status', ['submitted', 'evaluated']);
                })
                ->count(),
        ];

        return view('portal.homework.index', compact('student', 'homeworks', 'submissionsByHomework', 'subjects', 'stats'));
    }

    /**
     * Display pending homework only.
     */
    public function pending()
    {
        $student = $this->getStudent();

        if (!$student) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Student profile not found.');
        }

        $homeworks = Homework::with(['subject', 'teacher', 'academicYear'])
            ->where('class_id', $student->class_id)
            ->where(function ($q) use ($student) {
                $q->whereNull('section_id')
                    ->orWhere('section_id', $student->section_id);
            })
            ->where('is_active', true)
            ->whereDoesntHave('submissions', function ($q) use ($student) {
                $q->where('student_id', $student->id)
                    ->whereIn('status', ['submitted', 'evaluated']);
            })
            ->orderBy('submission_date', 'asc')
            ->paginate(10);

        $submissionsByHomework = HomeworkSubmission::where('student_id', $student->id)
            ->whereIn('homework_id', $homeworks->pluck('id'))
            ->get()
            ->keyBy('homework_id');

        return view('portal.homework.pending', compact('student', 'homeworks', 'submissionsByHomework'));
    }

    /**
     * Display submitted homework only.
     */
    public function submitted()
    {
        $student = $this->getStudent();

        if (!$student) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Student profile not found.');
        }

        $submissions = HomeworkSubmission::with(['homework.subject', 'homework.teacher'])
            ->where('student_id', $student->id)
            ->whereIn('status', ['submitted', 'evaluated', 'late'])
            ->orderBy('submitted_date', 'desc')
            ->paginate(10);

        return view('portal.homework.submitted', compact('student', 'submissions'));
    }

    /**
     * Display homework details.
     */
    public function show(Homework $homework)
    {
        $student = $this->getStudent();

        if (!$student) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Student profile not found.');
        }

        // Check if homework is for student's class
        if ($homework->class_id != $student->class_id) {
            return redirect()->route('portal.homework')
                ->with('error', 'This homework is not assigned to your class.');
        }

        // Check if homework is for student's section
        if ($homework->section_id && $homework->section_id != $student->section_id) {
            return redirect()->route('portal.homework')
                ->with('error', 'This homework is not assigned to your section.');
        }

        $homework->load(['subject', 'teacher', 'academicYear', 'schoolClass', 'section']);

        // Get student's submission
        $submission = HomeworkSubmission::where('homework_id', $homework->id)
            ->where('student_id', $student->id)
            ->first();

        return view('portal.homework.show', compact('student', 'homework', 'submission'));
    }

    /**
     * Submit homework.
     */
    public function submit(Request $request, Homework $homework)
    {
        // Parents cannot submit homework on behalf of students
        if ($this->isParentUser()) {
            return redirect()->route('portal.homework.show', $homework)
                ->with('error', 'Parents cannot submit homework. Only students can submit their own homework.');
        }

        $student = $this->getStudent();

        if (!$student) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Student profile not found.');
        }

        // Check if homework is for student's class
        if ($homework->class_id != $student->class_id) {
            return redirect()->route('portal.homework')
                ->with('error', 'This homework is not assigned to your class.');
        }

        // Check existing submission
        $existingSubmission = HomeworkSubmission::where('homework_id', $homework->id)
            ->where('student_id', $student->id)
            ->first();

        // Cannot edit after evaluation
        if ($existingSubmission && $existingSubmission->status === 'evaluated') {
            return back()->with('error', 'This homework has already been evaluated and cannot be edited.');
        }

        // Validate request — files required for new submissions, optional when editing (already has files)
        $attachmentsRule = 'array';
        if ($existingSubmission && $existingSubmission->attachment) {
            $attachmentsRule = 'nullable|' . $attachmentsRule;
        } else {
            $attachmentsRule = 'required|' . $attachmentsRule;
        }

        $request->validate([
            'submission_text' => 'nullable|string|max:5000',
            'attachments' => $attachmentsRule,
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png,zip|max:5120',
        ]);

        // Determine if late submission
        $isLate = $homework->submission_date < now();

        // Handle file uploads (multiple)
        $attachmentPath = $existingSubmission?->attachment;
        if ($request->hasFile('attachments')) {
            // Delete old attachments if exists
            if ($attachmentPath) {
                $oldPaths = json_decode($attachmentPath, true);
                if (is_array($oldPaths)) {
                    foreach ($oldPaths as $oldPath) {
                        if (Storage::disk('public')->exists($oldPath)) {
                            Storage::disk('public')->delete($oldPath);
                        }
                    }
                } elseif (Storage::disk('public')->exists($attachmentPath)) {
                    Storage::disk('public')->delete($attachmentPath);
                }
            }

            $paths = [];
            foreach ($request->file('attachments') as $file) {
                $paths[] = $file->store('homework-submissions/' . $student->id, 'public');
            }
            $attachmentPath = json_encode($paths);
        }

        // Create or update submission
        $submission = HomeworkSubmission::updateOrCreate(
            [
                'homework_id' => $homework->id,
                'student_id' => $student->id,
            ],
            [
                'submission_text' => $request->submission_text,
                'attachment' => $attachmentPath,
                'submitted_date' => now(),
                'status' => $isLate ? 'late' : 'submitted',
            ]
        );

        return redirect()->route('portal.homework.show', $homework)
            ->with('success', 'Homework submitted successfully!' . ($isLate ? ' (Late submission)' : ''));
    }
}
