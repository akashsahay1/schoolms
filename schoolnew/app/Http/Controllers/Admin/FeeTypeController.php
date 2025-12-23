<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeType;
use Illuminate\Http\Request;

class FeeTypeController extends Controller
{
	public function index(Request $request)
	{
		$query = FeeType::query();

		// Search filter
		if ($request->filled('search')) {
			$search = $request->search;
			$query->where(function ($q) use ($search) {
				$q->where('name', 'like', "%{$search}%")
					->orWhere('code', 'like', "%{$search}%");
			});
		}

		// Status filter
		if ($request->filled('status')) {
			$query->where('is_active', $request->status === 'active');
		}

		$feeTypes = $query->orderBy('name')->paginate(15);

		return view('admin.fees.types.index', compact('feeTypes'));
	}

	public function create()
	{
		return view('admin.fees.types.create');
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'name' => ['required', 'string', 'max:100'],
			'code' => ['required', 'string', 'max:20', 'unique:fee_types,code'],
			'description' => ['nullable', 'string', 'max:500'],
			'is_active' => ['nullable', 'boolean'],
		]);

		try {
			FeeType::create([
				'name' => $validated['name'],
				'code' => strtoupper($validated['code']),
				'description' => $validated['description'] ?? null,
				'is_active' => $request->has('is_active'),
			]);

			return redirect()->route('admin.fees.types.index')
				->with('success', 'Fee type created successfully.');

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
		}
	}

	public function edit(FeeType $feeType)
	{
		return view('admin.fees.types.edit', compact('feeType'));
	}

	public function update(Request $request, FeeType $feeType)
	{
		$validated = $request->validate([
			'name' => ['required', 'string', 'max:100'],
			'code' => ['required', 'string', 'max:20', 'unique:fee_types,code,' . $feeType->id],
			'description' => ['nullable', 'string', 'max:500'],
			'is_active' => ['nullable', 'boolean'],
		]);

		try {
			$feeType->update([
				'name' => $validated['name'],
				'code' => strtoupper($validated['code']),
				'description' => $validated['description'] ?? null,
				'is_active' => $request->has('is_active'),
			]);

			return redirect()->route('admin.fees.types.index')
				->with('success', 'Fee type updated successfully.');

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
		}
	}

	public function destroy(FeeType $feeType)
	{
		try {
			// Check if fee type is used in any fee structure
			if ($feeType->feeStructures()->count() > 0) {
				return back()->with('error', 'Cannot delete fee type that is used in fee structures. Please remove it from structures first.');
			}

			$feeType->delete();

			return redirect()->route('admin.fees.types.index')
				->with('success', 'Fee type deleted successfully.');

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}
}
