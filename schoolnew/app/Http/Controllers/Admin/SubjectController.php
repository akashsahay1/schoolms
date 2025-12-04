<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
	public function index(Request $request)
	{
		$query = Subject::with('classes');

		// Search filter
		if ($request->filled('search')) {
			$search = $request->search;
			$query->where(function ($q) use ($search) {
				$q->where('name', 'like', "%{$search}%")
					->orWhere('code', 'like', "%{$search}%");
			});
		}

		// Type filter
		if ($request->filled('type')) {
			$query->where('type', $request->type);
		}

		// Status filter
		if ($request->filled('status')) {
			$query->where('is_active', $request->status === 'active');
		}

		$subjects = $query->orderBy('name')->paginate(15);

		return view('admin.subjects.index', compact('subjects'));
	}

	public function create()
	{
		$classes = SchoolClass::active()->ordered()->get();
		$teachers = User::role(['Teacher', 'Staff'])->orderBy('name')->get();

		return view('admin.subjects.create', compact('classes', 'teachers'));
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'name' => ['required', 'string', 'max:100'],
			'code' => ['required', 'string', 'max:20', 'unique:subjects,code'],
			'type' => ['required', 'in:theory,practical,both'],
			'full_marks' => ['required', 'integer', 'min:1'],
			'pass_marks' => ['required', 'integer', 'min:0'],
			'is_optional' => ['nullable', 'boolean'],
			'is_active' => ['nullable', 'boolean'],
			'classes' => ['nullable', 'array'],
			'classes.*' => ['exists:classes,id'],
		]);

		// Validate pass marks is less than full marks
		if ($validated['pass_marks'] >= $validated['full_marks']) {
			return back()->with('error', 'Pass marks must be less than full marks.')->withInput();
		}

		try {
			$subject = Subject::create([
				'name' => $validated['name'],
				'code' => strtoupper($validated['code']),
				'type' => $validated['type'],
				'full_marks' => $validated['full_marks'],
				'pass_marks' => $validated['pass_marks'],
				'is_optional' => $request->has('is_optional'),
				'is_active' => $request->has('is_active'),
			]);

			// Attach classes if provided
			if (!empty($validated['classes'])) {
				$subject->classes()->attach($validated['classes']);
			}

			return redirect()->route('admin.subjects.index')
				->with('success', 'Subject created successfully.');

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
		}
	}

	public function show(Subject $subject)
	{
		$subject->load('classes');
		return view('admin.subjects.show', compact('subject'));
	}

	public function edit(Subject $subject)
	{
		$subject->load('classes');
		$classes = SchoolClass::active()->ordered()->get();
		$teachers = User::role(['Teacher', 'Staff'])->orderBy('name')->get();

		return view('admin.subjects.edit', compact('subject', 'classes', 'teachers'));
	}

	public function update(Request $request, Subject $subject)
	{
		$validated = $request->validate([
			'name' => ['required', 'string', 'max:100'],
			'code' => ['required', 'string', 'max:20', 'unique:subjects,code,' . $subject->id],
			'type' => ['required', 'in:theory,practical,both'],
			'full_marks' => ['required', 'integer', 'min:1'],
			'pass_marks' => ['required', 'integer', 'min:0'],
			'is_optional' => ['nullable', 'boolean'],
			'is_active' => ['nullable', 'boolean'],
			'classes' => ['nullable', 'array'],
			'classes.*' => ['exists:classes,id'],
		]);

		// Validate pass marks is less than full marks
		if ($validated['pass_marks'] >= $validated['full_marks']) {
			return back()->with('error', 'Pass marks must be less than full marks.')->withInput();
		}

		try {
			$subject->update([
				'name' => $validated['name'],
				'code' => strtoupper($validated['code']),
				'type' => $validated['type'],
				'full_marks' => $validated['full_marks'],
				'pass_marks' => $validated['pass_marks'],
				'is_optional' => $request->has('is_optional'),
				'is_active' => $request->has('is_active'),
			]);

			// Sync classes
			$subject->classes()->sync($validated['classes'] ?? []);

			return redirect()->route('admin.subjects.index')
				->with('success', 'Subject updated successfully.');

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
		}
	}

	public function destroy(Subject $subject)
	{
		try {
			// Check if subject is assigned to any class
			if ($subject->classes()->count() > 0) {
				return back()->with('error', 'Cannot delete subject that is assigned to classes. Please remove it from classes first.');
			}

			$subject->delete();

			return redirect()->route('admin.subjects.index')
				->with('success', 'Subject deleted successfully.');

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}
}
