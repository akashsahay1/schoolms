<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HomeworkController extends Controller
{
	public function index(Request $request)
	{
		$query = Homework::with(['schoolClass', 'section', 'subject', 'teacher']);

		// Academic year filter
		if ($request->filled('academic_year')) {
			$query->where('academic_year_id', $request->academic_year);
		} else {
			$currentYear = AcademicYear::where('is_active', true)->first();
			if ($currentYear) {
				$query->where('academic_year_id', $currentYear->id);
			}
		}

		// Class filter
		if ($request->filled('class')) {
			$query->where('class_id', $request->class);
		}

		// Section filter
		if ($request->filled('section')) {
			$query->where('section_id', $request->section);
		}

		// Subject filter
		if ($request->filled('subject')) {
			$query->where('subject_id', $request->subject);
		}

		// Status filter
		if ($request->filled('status')) {
			if ($request->status === 'overdue') {
				$query->where('submission_date', '<', now());
			} elseif ($request->status === 'active') {
				$query->where('is_active', true);
			}
		}

		$homeworks = $query->orderBy('homework_date', 'desc')->paginate(15);
		$academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
		$classes = SchoolClass::active()->ordered()->get();
		$subjects = Subject::active()->orderBy('name')->get();
		$trashedCount = Homework::onlyTrashed()->count();

		return view('admin.homework.index', compact('homeworks', 'academicYears', 'classes', 'subjects', 'trashedCount'));
	}

	public function create()
	{
		$academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
		$classes = SchoolClass::active()->ordered()->get();
		$subjects = Subject::active()->orderBy('name')->get();
		$teachers = User::role('Teacher')->orderBy('name')->get();

		return view('admin.homework.create', compact('academicYears', 'classes', 'subjects', 'teachers'));
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'academic_year_id' => ['required', 'exists:academic_years,id'],
			'class_id' => ['required', 'exists:classes,id'],
			'section_id' => ['nullable', 'exists:sections,id'],
			'subject_id' => ['required', 'exists:subjects,id'],
			'teacher_id' => ['nullable', 'exists:users,id'],
			'title' => ['required', 'string', 'max:255'],
			'description' => ['required', 'string'],
			'homework_date' => ['required', 'date'],
			'submission_date' => ['required', 'date', 'after_or_equal:homework_date'],
			'attachment' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:5120'],
			'max_marks' => ['nullable', 'integer', 'min:0'],
			'is_active' => ['nullable', 'boolean'],
		]);

		try {
			$attachmentPath = null;
			if ($request->hasFile('attachment')) {
				$attachmentPath = $request->file('attachment')->store('homework', 'public');
			}

			$homework = Homework::create([
				'academic_year_id' => $validated['academic_year_id'],
				'class_id' => $validated['class_id'],
				'section_id' => $validated['section_id'] ?? null,
				'subject_id' => $validated['subject_id'],
				'teacher_id' => $validated['teacher_id'] ?? Auth::id(),
				'title' => $validated['title'],
				'description' => $validated['description'],
				'homework_date' => $validated['homework_date'],
				'submission_date' => $validated['submission_date'],
				'attachment' => $attachmentPath,
				'max_marks' => $validated['max_marks'] ?? 0,
				'is_active' => $request->has('is_active'),
			]);

			// Create pending submissions for all students in the class/section
			$students = Student::where('class_id', $validated['class_id'])
				->when($validated['section_id'] ?? null, function ($q) use ($validated) {
					$q->where('section_id', $validated['section_id']);
				})
				->where('status', 'active')
				->get();

			foreach ($students as $student) {
				HomeworkSubmission::create([
					'homework_id' => $homework->id,
					'student_id' => $student->id,
					'status' => HomeworkSubmission::STATUS_PENDING,
				]);
			}

			return redirect()->route('admin.homework.index')
				->with('success', 'Homework assigned successfully.');

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
		}
	}

	public function edit(Homework $homework)
	{
		$homework->load(['schoolClass', 'section', 'subject', 'teacher']);
		$academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
		$classes = SchoolClass::active()->ordered()->get();
		$subjects = Subject::active()->orderBy('name')->get();
		$teachers = User::role('Teacher')->orderBy('name')->get();

		return view('admin.homework.edit', compact('homework', 'academicYears', 'classes', 'subjects', 'teachers'));
	}

	public function update(Request $request, Homework $homework)
	{
		$validated = $request->validate([
			'academic_year_id' => ['required', 'exists:academic_years,id'],
			'class_id' => ['required', 'exists:classes,id'],
			'section_id' => ['nullable', 'exists:sections,id'],
			'subject_id' => ['required', 'exists:subjects,id'],
			'teacher_id' => ['nullable', 'exists:users,id'],
			'title' => ['required', 'string', 'max:255'],
			'description' => ['required', 'string'],
			'homework_date' => ['required', 'date'],
			'submission_date' => ['required', 'date', 'after_or_equal:homework_date'],
			'attachment' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:5120'],
			'max_marks' => ['nullable', 'integer', 'min:0'],
			'is_active' => ['nullable', 'boolean'],
		]);

		try {
			$attachmentPath = $homework->attachment;
			if ($request->hasFile('attachment')) {
				// Delete old attachment
				if ($attachmentPath) {
					Storage::disk('public')->delete($attachmentPath);
				}
				$attachmentPath = $request->file('attachment')->store('homework', 'public');
			}

			$homework->update([
				'academic_year_id' => $validated['academic_year_id'],
				'class_id' => $validated['class_id'],
				'section_id' => $validated['section_id'] ?? null,
				'subject_id' => $validated['subject_id'],
				'teacher_id' => $validated['teacher_id'] ?? $homework->teacher_id,
				'title' => $validated['title'],
				'description' => $validated['description'],
				'homework_date' => $validated['homework_date'],
				'submission_date' => $validated['submission_date'],
				'attachment' => $attachmentPath,
				'max_marks' => $validated['max_marks'] ?? 0,
				'is_active' => $request->has('is_active'),
			]);

			return redirect()->route('admin.homework.index')
				->with('success', 'Homework updated successfully.');

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
		}
	}

	public function destroy(Homework $homework)
	{
		try {
			$homework->delete();

			return redirect()->route('admin.homework.index')
				->with('success', 'Homework moved to trash.');

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}

	public function bulkDelete(Request $request)
	{
		$request->validate([
			'homework_ids' => ['required', 'array', 'min:1'],
			'homework_ids.*' => ['exists:homework,id'],
		]);

		try {
			$count = Homework::whereIn('id', $request->homework_ids)->count();
			Homework::whereIn('id', $request->homework_ids)->delete();

			return response()->json([
				'success' => true,
				'message' => "{$count} homework(s) moved to trash.",
			]);

		} catch (\Exception $e) {
			return response()->json([
				'success' => false,
				'message' => 'An error occurred: ' . $e->getMessage(),
			], 500);
		}
	}

	public function trash(Request $request)
	{
		$query = Homework::onlyTrashed()->with(['schoolClass', 'section', 'subject', 'teacher']);

		if ($request->filled('search')) {
			$search = $request->search;
			$query->where(function ($q) use ($search) {
				$q->where('title', 'like', "%{$search}%")
					->orWhere('description', 'like', "%{$search}%");
			});
		}

		$homeworks = $query->latest('deleted_at')->paginate(15);
		$trashedCount = Homework::onlyTrashed()->count();

		return view('admin.homework.trash', compact('homeworks', 'trashedCount'));
	}

	public function restore($id)
	{
		try {
			$homework = Homework::onlyTrashed()->findOrFail($id);
			$homework->restore();

			return redirect()->route('admin.homework.trash')
				->with('success', "'{$homework->title}' restored successfully.");

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}

	public function forceDelete($id)
	{
		try {
			$homework = Homework::onlyTrashed()->findOrFail($id);

			// Delete attachment if exists
			if ($homework->attachment) {
				Storage::disk('public')->delete($homework->attachment);
			}

			$title = $homework->title;
			$homework->forceDelete();

			return redirect()->route('admin.homework.trash')
				->with('success', "'{$title}' permanently deleted.");

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}

	public function bulkRestore(Request $request)
	{
		$request->validate([
			'homework_ids' => ['required', 'array', 'min:1'],
		]);

		try {
			$count = Homework::onlyTrashed()->whereIn('id', $request->homework_ids)->count();
			Homework::onlyTrashed()->whereIn('id', $request->homework_ids)->restore();

			return response()->json([
				'success' => true,
				'message' => "{$count} homework(s) restored successfully.",
			]);

		} catch (\Exception $e) {
			return response()->json([
				'success' => false,
				'message' => 'An error occurred: ' . $e->getMessage(),
			], 500);
		}
	}

	public function bulkForceDelete(Request $request)
	{
		$request->validate([
			'homework_ids' => ['required', 'array', 'min:1'],
		]);

		try {
			DB::beginTransaction();

			$homeworks = Homework::onlyTrashed()->whereIn('id', $request->homework_ids)->get();
			$count = $homeworks->count();

			foreach ($homeworks as $homework) {
				if ($homework->attachment) {
					Storage::disk('public')->delete($homework->attachment);
				}
				$homework->forceDelete();
			}

			DB::commit();

			return response()->json([
				'success' => true,
				'message' => "{$count} homework(s) permanently deleted.",
			]);

		} catch (\Exception $e) {
			DB::rollBack();
			return response()->json([
				'success' => false,
				'message' => 'An error occurred: ' . $e->getMessage(),
			], 500);
		}
	}

	public function emptyTrash()
	{
		try {
			DB::beginTransaction();

			$homeworks = Homework::onlyTrashed()->get();
			$count = $homeworks->count();

			foreach ($homeworks as $homework) {
				if ($homework->attachment) {
					Storage::disk('public')->delete($homework->attachment);
				}
				$homework->forceDelete();
			}

			DB::commit();

			return redirect()->route('admin.homework.trash')
				->with('success', "{$count} homework(s) permanently deleted from trash.");

		} catch (\Exception $e) {
			DB::rollBack();
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}

	public function submissions(Homework $homework)
	{
		$homework->load(['schoolClass', 'section', 'subject']);
		$submissions = HomeworkSubmission::with(['student', 'evaluatedBy'])
			->where('homework_id', $homework->id)
			->orderBy('status')
			->orderBy('submitted_date', 'desc')
			->get();

		$stats = [
			'total' => $submissions->count(),
			'submitted' => $submissions->whereIn('status', [HomeworkSubmission::STATUS_SUBMITTED, HomeworkSubmission::STATUS_LATE, HomeworkSubmission::STATUS_EVALUATED])->count(),
			'pending' => $submissions->where('status', HomeworkSubmission::STATUS_PENDING)->count(),
			'evaluated' => $submissions->where('status', HomeworkSubmission::STATUS_EVALUATED)->count(),
		];

		return view('admin.homework.submissions', compact('homework', 'submissions', 'stats'));
	}

	public function evaluateSubmission(Request $request, HomeworkSubmission $submission)
	{
		$validated = $request->validate([
			'marks_obtained' => ['required', 'integer', 'min:0', 'max:' . $submission->homework->max_marks],
			'remarks' => ['nullable', 'string', 'max:500'],
		]);

		try {
			$submission->update([
				'marks_obtained' => $validated['marks_obtained'],
				'remarks' => $validated['remarks'] ?? null,
				'status' => HomeworkSubmission::STATUS_EVALUATED,
				'evaluated_by' => Auth::id(),
				'evaluated_at' => now(),
			]);

			return back()->with('success', 'Submission evaluated successfully.');

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}

	public function getSections($classId)
	{
		$sections = Section::where('class_id', $classId)
			->where('is_active', true)
			->orderBy('name')
			->get();

		return response()->json($sections);
	}
}
