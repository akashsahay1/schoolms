<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class ClassController extends Controller
{
	public function index(Request $request)
	{
		$query = SchoolClass::with(['academicYear', 'sections', 'students']);

		// Search filter
		if ($request->filled('search')) {
			$search = $request->search;
			$query->where(function ($q) use ($search) {
				$q->where('name', 'like', "%{$search}%")
					->orWhere('numeric_name', 'like', "%{$search}%");
			});
		}

		// Status filter
		if ($request->filled('status')) {
			$query->where('is_active', $request->status === 'active');
		}

		$classes = $query->ordered()->paginate(15);
		$academicYear = AcademicYear::getActive();

		return view('admin.classes.index', compact('classes', 'academicYear'));
	}

	public function create()
	{
		$academicYear = AcademicYear::getActive();

		if (!$academicYear) {
			return redirect()->route('admin.classes.index')
				->with('error', 'No active academic year found. Please set up an academic year first.');
		}

		return view('admin.classes.create', compact('academicYear'));
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'name' => ['required', 'string', 'max:100'],
			'numeric_name' => ['nullable', 'string', 'max:10'],
			'pass_mark' => ['required', 'integer', 'min:0', 'max:100'],
			'order' => ['nullable', 'integer', 'min:0'],
			'is_active' => ['nullable', 'boolean'],
		]);

		$academicYear = AcademicYear::getActive();

		if (!$academicYear) {
			return back()->with('error', 'No active academic year found.')->withInput();
		}

		// Check if class name already exists for this academic year
		$exists = SchoolClass::where('name', $validated['name'])
			->where('academic_year_id', $academicYear->id)
			->exists();

		if ($exists) {
			return back()->with('error', 'A class with this name already exists for the current academic year.')->withInput();
		}

		try {
			SchoolClass::create([
				'name' => $validated['name'],
				'numeric_name' => $validated['numeric_name'] ?? null,
				'academic_year_id' => $academicYear->id,
				'pass_mark' => $validated['pass_mark'],
				'order' => $validated['order'] ?? 0,
				'is_active' => $request->has('is_active'),
			]);

			return redirect()->route('admin.classes.index')
				->with('success', 'Class created successfully.');

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
		}
	}

	public function show(SchoolClass $class)
	{
		$class->load(['academicYear', 'sections', 'students', 'subjects']);
		return view('admin.classes.show', compact('class'));
	}

	public function edit(SchoolClass $class)
	{
		$academicYear = AcademicYear::getActive();
		return view('admin.classes.edit', compact('class', 'academicYear'));
	}

	public function update(Request $request, SchoolClass $class)
	{
		$validated = $request->validate([
			'name' => ['required', 'string', 'max:100'],
			'numeric_name' => ['nullable', 'string', 'max:10'],
			'pass_mark' => ['required', 'integer', 'min:0', 'max:100'],
			'order' => ['nullable', 'integer', 'min:0'],
			'is_active' => ['nullable', 'boolean'],
		]);

		// Check if class name already exists for this academic year (excluding current)
		$exists = SchoolClass::where('name', $validated['name'])
			->where('academic_year_id', $class->academic_year_id)
			->where('id', '!=', $class->id)
			->exists();

		if ($exists) {
			return back()->with('error', 'A class with this name already exists for the current academic year.')->withInput();
		}

		try {
			$class->update([
				'name' => $validated['name'],
				'numeric_name' => $validated['numeric_name'] ?? null,
				'pass_mark' => $validated['pass_mark'],
				'order' => $validated['order'] ?? 0,
				'is_active' => $request->has('is_active'),
			]);

			return redirect()->route('admin.classes.index')
				->with('success', 'Class updated successfully.');

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
		}
	}

	public function destroy(SchoolClass $class)
	{
		try {
			// Check if class has students
			if ($class->students()->count() > 0) {
				return back()->with('error', 'Cannot delete class that has students assigned.');
			}

			// Check if class has sections
			if ($class->sections()->count() > 0) {
				return back()->with('error', 'Cannot delete class that has sections. Please delete sections first.');
			}

			$class->delete();

			return redirect()->route('admin.classes.index')
				->with('success', 'Class deleted successfully.');

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}
}
