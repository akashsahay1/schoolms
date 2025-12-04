<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\Request;

class SectionController extends Controller
{
	public function index(Request $request)
	{
		$query = Section::with(['schoolClass', 'classTeacher', 'students']);

		// Search filter
		if ($request->filled('search')) {
			$search = $request->search;
			$query->where(function ($q) use ($search) {
				$q->where('name', 'like', "%{$search}%")
					->orWhere('room_no', 'like', "%{$search}%");
			});
		}

		// Class filter
		if ($request->filled('class_id')) {
			$query->where('class_id', $request->class_id);
		}

		// Status filter
		if ($request->filled('status')) {
			$query->where('is_active', $request->status === 'active');
		}

		$sections = $query->orderBy('class_id')->orderBy('name')->paginate(15);
		$classes = SchoolClass::active()->ordered()->get();

		return view('admin.sections.index', compact('sections', 'classes'));
	}

	public function create()
	{
		$classes = SchoolClass::active()->ordered()->get();
		$teachers = User::where('role', 'teacher')->orWhere('role', 'staff')->orderBy('name')->get();

		if ($classes->isEmpty()) {
			return redirect()->route('admin.sections.index')
				->with('error', 'No active classes found. Please create a class first.');
		}

		return view('admin.sections.create', compact('classes', 'teachers'));
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'name' => ['required', 'string', 'max:50'],
			'class_id' => ['required', 'exists:classes,id'],
			'capacity' => ['nullable', 'integer', 'min:1'],
			'class_teacher_id' => ['nullable', 'exists:users,id'],
			'room_no' => ['nullable', 'string', 'max:20'],
			'is_active' => ['nullable', 'boolean'],
		]);

		// Check if section name already exists for this class
		$exists = Section::where('name', $validated['name'])
			->where('class_id', $validated['class_id'])
			->exists();

		if ($exists) {
			return back()->with('error', 'A section with this name already exists for the selected class.')->withInput();
		}

		try {
			Section::create([
				'name' => $validated['name'],
				'class_id' => $validated['class_id'],
				'capacity' => $validated['capacity'] ?? null,
				'class_teacher_id' => $validated['class_teacher_id'] ?? null,
				'room_no' => $validated['room_no'] ?? null,
				'is_active' => $request->has('is_active'),
			]);

			return redirect()->route('admin.sections.index')
				->with('success', 'Section created successfully.');

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
		}
	}

	public function show(Section $section)
	{
		$section->load(['schoolClass', 'classTeacher', 'students']);
		return view('admin.sections.show', compact('section'));
	}

	public function edit(Section $section)
	{
		$classes = SchoolClass::active()->ordered()->get();
		$teachers = User::where('role', 'teacher')->orWhere('role', 'staff')->orderBy('name')->get();

		return view('admin.sections.edit', compact('section', 'classes', 'teachers'));
	}

	public function update(Request $request, Section $section)
	{
		$validated = $request->validate([
			'name' => ['required', 'string', 'max:50'],
			'class_id' => ['required', 'exists:classes,id'],
			'capacity' => ['nullable', 'integer', 'min:1'],
			'class_teacher_id' => ['nullable', 'exists:users,id'],
			'room_no' => ['nullable', 'string', 'max:20'],
			'is_active' => ['nullable', 'boolean'],
		]);

		// Check if section name already exists for this class (excluding current)
		$exists = Section::where('name', $validated['name'])
			->where('class_id', $validated['class_id'])
			->where('id', '!=', $section->id)
			->exists();

		if ($exists) {
			return back()->with('error', 'A section with this name already exists for the selected class.')->withInput();
		}

		try {
			$section->update([
				'name' => $validated['name'],
				'class_id' => $validated['class_id'],
				'capacity' => $validated['capacity'] ?? null,
				'class_teacher_id' => $validated['class_teacher_id'] ?? null,
				'room_no' => $validated['room_no'] ?? null,
				'is_active' => $request->has('is_active'),
			]);

			return redirect()->route('admin.sections.index')
				->with('success', 'Section updated successfully.');

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
		}
	}

	public function destroy(Section $section)
	{
		try {
			// Check if section has students
			if ($section->students()->count() > 0) {
				return back()->with('error', 'Cannot delete section that has students assigned.');
			}

			$section->delete();

			return redirect()->route('admin.sections.index')
				->with('success', 'Section deleted successfully.');

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}
}
