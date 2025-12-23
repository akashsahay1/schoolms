<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeStructure;
use App\Models\FeeType;
use App\Models\FeeGroup;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class FeeStructureController extends Controller
{
	public function index(Request $request)
	{
		$query = FeeStructure::with(['academicYear', 'schoolClass', 'feeType', 'feeGroup']);

		// Academic year filter
		if ($request->filled('academic_year')) {
			$query->where('academic_year_id', $request->academic_year);
		} else {
			// Default to current academic year
			$currentYear = AcademicYear::where('is_active', true)->first();
			if ($currentYear) {
				$query->where('academic_year_id', $currentYear->id);
			}
		}

		// Class filter
		if ($request->filled('class')) {
			$query->where('class_id', $request->class);
		}

		// Fee type filter
		if ($request->filled('fee_type')) {
			$query->where('fee_type_id', $request->fee_type);
		}

		// Status filter
		if ($request->filled('status')) {
			$query->where('is_active', $request->status === 'active');
		}

		$feeStructures = $query->orderBy('created_at', 'desc')->paginate(15);
		$academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
		$classes = SchoolClass::active()->ordered()->get();
		$feeTypes = FeeType::active()->orderBy('name')->get();

		return view('admin.fees.structure.index', compact('feeStructures', 'academicYears', 'classes', 'feeTypes'));
	}

	public function create()
	{
		$academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
		$classes = SchoolClass::active()->ordered()->get();
		$feeTypes = FeeType::active()->orderBy('name')->get();
		$feeGroups = FeeGroup::active()->orderBy('name')->get();

		return view('admin.fees.structure.create', compact('academicYears', 'classes', 'feeTypes', 'feeGroups'));
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'academic_year_id' => ['required', 'exists:academic_years,id'],
			'class_id' => ['required', 'exists:classes,id'],
			'fee_type_id' => ['required', 'exists:fee_types,id'],
			'fee_group_id' => ['required', 'exists:fee_groups,id'],
			'amount' => ['required', 'numeric', 'min:0'],
			'due_date' => ['nullable', 'date'],
			'fine_amount' => ['nullable', 'numeric', 'min:0'],
			'fine_type' => ['nullable', 'in:fixed,percentage'],
			'description' => ['nullable', 'string', 'max:500'],
			'is_active' => ['nullable', 'boolean'],
		]);

		try {
			// Check for duplicate
			$exists = FeeStructure::where('academic_year_id', $validated['academic_year_id'])
				->where('class_id', $validated['class_id'])
				->where('fee_type_id', $validated['fee_type_id'])
				->where('fee_group_id', $validated['fee_group_id'])
				->exists();

			if ($exists) {
				return back()->with('error', 'Fee structure already exists for this combination.')->withInput();
			}

			FeeStructure::create([
				'academic_year_id' => $validated['academic_year_id'],
				'class_id' => $validated['class_id'],
				'fee_type_id' => $validated['fee_type_id'],
				'fee_group_id' => $validated['fee_group_id'],
				'amount' => $validated['amount'],
				'due_date' => $validated['due_date'] ?? null,
				'fine_amount' => $validated['fine_amount'] ?? 0,
				'fine_type' => $validated['fine_type'] ?? 'fixed',
				'description' => $validated['description'] ?? null,
				'is_active' => $request->has('is_active'),
			]);

			return redirect()->route('admin.fees.structure')
				->with('success', 'Fee structure created successfully.');

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
		}
	}

	public function edit(FeeStructure $feeStructure)
	{
		$feeStructure->load(['academicYear', 'schoolClass', 'feeType', 'feeGroup']);
		$academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
		$classes = SchoolClass::active()->ordered()->get();
		$feeTypes = FeeType::active()->orderBy('name')->get();
		$feeGroups = FeeGroup::active()->orderBy('name')->get();

		return view('admin.fees.structure.edit', compact('feeStructure', 'academicYears', 'classes', 'feeTypes', 'feeGroups'));
	}

	public function update(Request $request, FeeStructure $feeStructure)
	{
		$validated = $request->validate([
			'academic_year_id' => ['required', 'exists:academic_years,id'],
			'class_id' => ['required', 'exists:classes,id'],
			'fee_type_id' => ['required', 'exists:fee_types,id'],
			'fee_group_id' => ['required', 'exists:fee_groups,id'],
			'amount' => ['required', 'numeric', 'min:0'],
			'due_date' => ['nullable', 'date'],
			'fine_amount' => ['nullable', 'numeric', 'min:0'],
			'fine_type' => ['nullable', 'in:fixed,percentage'],
			'description' => ['nullable', 'string', 'max:500'],
			'is_active' => ['nullable', 'boolean'],
		]);

		try {
			// Check for duplicate (excluding current record)
			$exists = FeeStructure::where('academic_year_id', $validated['academic_year_id'])
				->where('class_id', $validated['class_id'])
				->where('fee_type_id', $validated['fee_type_id'])
				->where('fee_group_id', $validated['fee_group_id'])
				->where('id', '!=', $feeStructure->id)
				->exists();

			if ($exists) {
				return back()->with('error', 'Fee structure already exists for this combination.')->withInput();
			}

			$feeStructure->update([
				'academic_year_id' => $validated['academic_year_id'],
				'class_id' => $validated['class_id'],
				'fee_type_id' => $validated['fee_type_id'],
				'fee_group_id' => $validated['fee_group_id'],
				'amount' => $validated['amount'],
				'due_date' => $validated['due_date'] ?? null,
				'fine_amount' => $validated['fine_amount'] ?? 0,
				'fine_type' => $validated['fine_type'] ?? 'fixed',
				'description' => $validated['description'] ?? null,
				'is_active' => $request->has('is_active'),
			]);

			return redirect()->route('admin.fees.structure')
				->with('success', 'Fee structure updated successfully.');

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
		}
	}

	public function destroy(FeeStructure $feeStructure)
	{
		try {
			// Check if fee structure has any collections
			if ($feeStructure->collections()->count() > 0) {
				return back()->with('error', 'Cannot delete fee structure that has fee collections. Please remove collections first.');
			}

			$feeStructure->delete();

			return redirect()->route('admin.fees.structure')
				->with('success', 'Fee structure deleted successfully.');

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}

	public function duplicate(Request $request, FeeStructure $feeStructure)
	{
		if ($request->isMethod('get')) {
			$academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
			$classes = SchoolClass::active()->ordered()->get();

			return view('admin.fees.structure.duplicate', compact('feeStructure', 'academicYears', 'classes'));
		}

		$validated = $request->validate([
			'academic_year_id' => ['required', 'exists:academic_years,id'],
			'class_id' => ['required', 'exists:classes,id'],
		]);

		try {
			// Check if already exists
			$exists = FeeStructure::where('academic_year_id', $validated['academic_year_id'])
				->where('class_id', $validated['class_id'])
				->where('fee_type_id', $feeStructure->fee_type_id)
				->where('fee_group_id', $feeStructure->fee_group_id)
				->exists();

			if ($exists) {
				return back()->with('error', 'Fee structure already exists for the selected academic year and class.')->withInput();
			}

			// Duplicate the structure
			FeeStructure::create([
				'academic_year_id' => $validated['academic_year_id'],
				'class_id' => $validated['class_id'],
				'fee_type_id' => $feeStructure->fee_type_id,
				'fee_group_id' => $feeStructure->fee_group_id,
				'amount' => $feeStructure->amount,
				'due_date' => $feeStructure->due_date,
				'fine_amount' => $feeStructure->fine_amount,
				'fine_type' => $feeStructure->fine_type,
				'description' => $feeStructure->description,
				'is_active' => $feeStructure->is_active,
			]);

			return redirect()->route('admin.fees.structure')
				->with('success', 'Fee structure duplicated successfully.');

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
		}
	}
}
