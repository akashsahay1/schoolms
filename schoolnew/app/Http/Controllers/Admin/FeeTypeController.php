<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
		$trashedCount = FeeType::onlyTrashed()->count();

		return view('admin.fees.types.index', compact('feeTypes', 'trashedCount'));
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
				->with('success', 'Fee type moved to trash successfully.');

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}

	public function bulkDelete(Request $request)
	{
		$request->validate([
			'ids' => ['required', 'array'],
			'ids.*' => ['exists:fee_types,id'],
		]);

		try {
			DB::beginTransaction();

			$feeTypes = FeeType::whereIn('id', $request->ids)->get();
			$deletedCount = 0;
			$skippedCount = 0;

			foreach ($feeTypes as $feeType) {
				// Check if fee type is used in any fee structure
				if ($feeType->feeStructures()->count() > 0) {
					$skippedCount++;
					continue;
				}

				$feeType->delete();
				$deletedCount++;
			}

			DB::commit();

			if ($skippedCount > 0) {
				return back()->with('warning', "{$deletedCount} fee type(s) moved to trash. {$skippedCount} fee type(s) skipped because they are used in fee structures.");
			}

			return back()->with('success', "{$deletedCount} fee type(s) moved to trash successfully.");

		} catch (\Exception $e) {
			DB::rollBack();
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}

	public function trash(Request $request)
	{
		$query = FeeType::onlyTrashed();

		// Search filter
		if ($request->filled('search')) {
			$search = $request->search;
			$query->where(function ($q) use ($search) {
				$q->where('name', 'like', "%{$search}%")
					->orWhere('code', 'like', "%{$search}%");
			});
		}

		$feeTypes = $query->orderBy('deleted_at', 'desc')->paginate(15);

		return view('admin.fees.types.trash', compact('feeTypes'));
	}

	public function restore($id)
	{
		try {
			$feeType = FeeType::onlyTrashed()->findOrFail($id);
			$feeType->restore();

			return redirect()->route('admin.fees.types.trash')
				->with('success', 'Fee type restored successfully.');

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}

	public function forceDelete($id)
	{
		try {
			$feeType = FeeType::onlyTrashed()->findOrFail($id);
			$feeType->forceDelete();

			return redirect()->route('admin.fees.types.trash')
				->with('success', 'Fee type permanently deleted.');

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}

	public function bulkRestore(Request $request)
	{
		$request->validate([
			'ids' => ['required', 'array'],
		]);

		try {
			$restoredCount = FeeType::onlyTrashed()
				->whereIn('id', $request->ids)
				->restore();

			return back()->with('success', "{$restoredCount} fee type(s) restored successfully.");

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}

	public function bulkForceDelete(Request $request)
	{
		$request->validate([
			'ids' => ['required', 'array'],
		]);

		try {
			$deletedCount = FeeType::onlyTrashed()
				->whereIn('id', $request->ids)
				->forceDelete();

			return back()->with('success', "{$deletedCount} fee type(s) permanently deleted.");

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}

	public function emptyTrash()
	{
		try {
			$deletedCount = FeeType::onlyTrashed()->forceDelete();

			return redirect()->route('admin.fees.types.trash')
				->with('success', "{$deletedCount} fee type(s) permanently deleted from trash.");

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}
}
