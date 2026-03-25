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
		$teachers = User::role('Teacher')->orderBy('name')->get();

		if ($classes->isEmpty()) {
			return redirect()->route('admin.sections.index')
				->with('error', 'No active classes found. Please create a class first.');
		}

		// Get already assigned class teachers for the active academic year
		$assignedTeachers = $this->getAssignedClassTeachers();

		return view('admin.sections.create', compact('classes', 'teachers', 'assignedTeachers'));
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

		// Validate class teacher not already assigned in same academic year
		if (!empty($validated['class_teacher_id'])) {
			$conflict = $this->checkClassTeacherConflict($validated['class_teacher_id'], $validated['class_id']);
			if ($conflict) {
				return back()->with('error', $conflict)->withInput();
			}
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
		$section->load(['schoolClass', 'students']);
		$classes = SchoolClass::active()->ordered()->get();
		$teachers = User::role('Teacher')->orderBy('name')->get();

		// Get already assigned class teachers, excluding the current section
		$assignedTeachers = $this->getAssignedClassTeachers($section->id);

		return view('admin.sections.edit', compact('section', 'classes', 'teachers', 'assignedTeachers'));
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

		// Validate class teacher not already assigned in same academic year
		if (!empty($validated['class_teacher_id'])) {
			$conflict = $this->checkClassTeacherConflict($validated['class_teacher_id'], $validated['class_id'], $section->id);
			if ($conflict) {
				return back()->with('error', $conflict)->withInput();
			}
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

	/**
	 * AJAX: Get assigned class teachers for a specific academic year.
	 * Used for real-time dropdown updates when the class changes.
	 */
	public function getAssignedTeachers(Request $request)
	{
		$classId = $request->input('class_id');
		$excludeSectionId = $request->input('exclude_section_id');

		if (!$classId) {
			return response()->json([]);
		}

		$class = SchoolClass::find($classId);
		if (!$class || !$class->academic_year_id) {
			return response()->json([]);
		}

		// Get all class IDs in the same academic year
		$classIds = SchoolClass::where('academic_year_id', $class->academic_year_id)->pluck('id');

		// Get sections with assigned class teachers in those classes
		$query = Section::with(['classTeacher', 'schoolClass'])
			->whereIn('class_id', $classIds)
			->whereNotNull('class_teacher_id');

		if ($excludeSectionId) {
			$query->where('id', '!=', $excludeSectionId);
		}

		$sections = $query->get();

		$assigned = [];
		foreach ($sections as $section) {
			$assigned[$section->class_teacher_id] = $section->schoolClass->name . ' ' . $section->name;
		}

		return response()->json($assigned);
	}

	/**
	 * Get a map of teacher_id => "Class Name Section" for all currently
	 * assigned class teachers in the active academic year.
	 */
	private function getAssignedClassTeachers(?int $excludeSectionId = null): array
	{
		$activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
		if (!$activeYear) {
			return [];
		}

		$classIds = SchoolClass::where('academic_year_id', $activeYear->id)->pluck('id');

		$query = Section::with(['schoolClass'])
			->whereIn('class_id', $classIds)
			->whereNotNull('class_teacher_id');

		if ($excludeSectionId) {
			$query->where('id', '!=', $excludeSectionId);
		}

		$sections = $query->get();

		$assigned = [];
		foreach ($sections as $section) {
			$assigned[$section->class_teacher_id] = $section->schoolClass->name . ' ' . $section->name;
		}

		return $assigned;
	}

	/**
	 * Check if a teacher is already assigned as class teacher in
	 * another section within the same academic year.
	 * Returns error message string or null if no conflict.
	 */
	private function checkClassTeacherConflict(int $teacherId, int $classId, ?int $excludeSectionId = null): ?string
	{
		$class = SchoolClass::find($classId);
		if (!$class || !$class->academic_year_id) {
			return null;
		}

		// Get all class IDs in the same academic year
		$classIds = SchoolClass::where('academic_year_id', $class->academic_year_id)->pluck('id');

		// Check if teacher is already assigned
		$query = Section::with(['schoolClass'])
			->whereIn('class_id', $classIds)
			->where('class_teacher_id', $teacherId);

		if ($excludeSectionId) {
			$query->where('id', '!=', $excludeSectionId);
		}

		$existingSection = $query->first();

		if ($existingSection) {
			$teacher = User::find($teacherId);
			$teacherName = $teacher ? $teacher->name : 'This teacher';
			$existingClass = $existingSection->schoolClass->name . ' ' . $existingSection->name;

			return "{$teacherName} is already assigned as Class Teacher for {$existingClass} in this academic session. A teacher can only be Class Teacher of one class per session.";
		}

		return null;
	}
}
