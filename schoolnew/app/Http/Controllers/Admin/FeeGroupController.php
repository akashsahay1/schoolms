<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeGroup;
use Illuminate\Http\Request;

class FeeGroupController extends Controller
{
	public function index(Request $request)
	{
		$query = FeeGroup::query();

		// Search filter
		if ($request->filled('search')) {
			$search = $request->search;
			$query->where('name', 'like', "%{$search}%");
		}

		// Status filter
		if ($request->filled('status')) {
			$query->where('is_active', $request->status === 'active');
		}

		$feeGroups = $query->orderBy('name')->paginate(15);

		return view('admin.fees.groups.index', compact('feeGroups'));
	}

	public function create()
	{
		return view('admin.fees.groups.create');
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'name' => ['required', 'string', 'max:100', 'unique:fee_groups,name'],
			'description' => ['nullable', 'string', 'max:500'],
			'is_active' => ['nullable', 'boolean'],
		]);

		try {
			FeeGroup::create([
				'name' => $validated['name'],
				'description' => $validated['description'] ?? null,
				'is_active' => $request->has('is_active'),
			]);

			return redirect()->route('admin.fees.groups.index')
				->with('success', 'Fee group created successfully.');

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
		}
	}

	public function edit(FeeGroup $feeGroup)
	{
		return view('admin.fees.groups.edit', compact('feeGroup'));
	}

	public function update(Request $request, FeeGroup $feeGroup)
	{
		$validated = $request->validate([
			'name' => ['required', 'string', 'max:100', 'unique:fee_groups,name,' . $feeGroup->id],
			'description' => ['nullable', 'string', 'max:500'],
			'is_active' => ['nullable', 'boolean'],
		]);

		try {
			$feeGroup->update([
				'name' => $validated['name'],
				'description' => $validated['description'] ?? null,
				'is_active' => $request->has('is_active'),
			]);

			return redirect()->route('admin.fees.groups.index')
				->with('success', 'Fee group updated successfully.');

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
		}
	}

	public function destroy(FeeGroup $feeGroup)
	{
		try {
			// Check if fee group is used in any fee structure
			if ($feeGroup->feeStructures()->count() > 0) {
				return back()->with('error', 'Cannot delete fee group that is used in fee structures. Please remove it from structures first.');
			}

			$feeGroup->delete();

			return redirect()->route('admin.fees.groups.index')
				->with('success', 'Fee group deleted successfully.');

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}
}
