<?php

namespace App\Http\Controllers;

use App\Models\FeeGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeeGroupController extends Controller
{
	public function index()
	{
		$feeGroups = FeeGroup::withCount('feeStructures')
			->orderBy('name')
			->paginate(15);

		$trashedCount = FeeGroup::onlyTrashed()->count();

		return view('fees.groups.index', compact('feeGroups', 'trashedCount'));
	}

	public function create()
	{
		return view('fees.groups.create');
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'name' => 'required|string|max:255|unique:fee_groups,name',
			'description' => 'nullable|string|max:500',
			'is_active' => 'boolean',
		]);

		$validated['is_active'] = $request->has('is_active');

		FeeGroup::create($validated);

		return redirect()->route('admin.fees.groups.index')
			->with('success', 'Fee group created successfully.');
	}

	public function edit(FeeGroup $feeGroup)
	{
		return view('fees.groups.edit', compact('feeGroup'));
	}

	public function update(Request $request, FeeGroup $feeGroup)
	{
		$validated = $request->validate([
			'name' => 'required|string|max:255|unique:fee_groups,name,' . $feeGroup->id,
			'description' => 'nullable|string|max:500',
			'is_active' => 'boolean',
		]);

		$validated['is_active'] = $request->has('is_active');

		$feeGroup->update($validated);

		return redirect()->route('admin.fees.groups.index')
			->with('success', 'Fee group updated successfully.');
	}

	public function destroy(FeeGroup $feeGroup)
	{
		$feeGroup->delete();

		return redirect()->route('admin.fees.groups.index')
			->with('success', 'Fee group moved to trash.');
	}

	public function bulkDelete(Request $request)
	{
		$request->validate([
			'fee_group_ids' => ['required', 'array', 'min:1'],
			'fee_group_ids.*' => ['exists:fee_groups,id'],
		]);

		try {
			$count = FeeGroup::whereIn('id', $request->fee_group_ids)->count();
			FeeGroup::whereIn('id', $request->fee_group_ids)->delete();

			return response()->json([
				'success' => true,
				'message' => "{$count} fee group(s) moved to trash.",
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
		$query = FeeGroup::onlyTrashed()->withCount('feeStructures');

		if ($request->filled('search')) {
			$search = $request->search;
			$query->where(function ($q) use ($search) {
				$q->where('name', 'like', "%{$search}%")
					->orWhere('description', 'like', "%{$search}%");
			});
		}

		$feeGroups = $query->latest('deleted_at')->paginate(15);
		$trashedCount = FeeGroup::onlyTrashed()->count();

		return view('fees.groups.trash', compact('feeGroups', 'trashedCount'));
	}

	public function restore($id)
	{
		try {
			$feeGroup = FeeGroup::onlyTrashed()->findOrFail($id);
			$feeGroup->restore();

			return redirect()->route('admin.fees.groups.trash')
				->with('success', "'{$feeGroup->name}' restored successfully.");

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}

	public function forceDelete($id)
	{
		try {
			$feeGroup = FeeGroup::onlyTrashed()->findOrFail($id);
			$name = $feeGroup->name;
			$feeGroup->forceDelete();

			return redirect()->route('admin.fees.groups.trash')
				->with('success', "'{$name}' permanently deleted.");

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}

	public function bulkRestore(Request $request)
	{
		$request->validate([
			'fee_group_ids' => ['required', 'array', 'min:1'],
		]);

		try {
			$count = FeeGroup::onlyTrashed()->whereIn('id', $request->fee_group_ids)->count();
			FeeGroup::onlyTrashed()->whereIn('id', $request->fee_group_ids)->restore();

			return response()->json([
				'success' => true,
				'message' => "{$count} fee group(s) restored successfully.",
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
			'fee_group_ids' => ['required', 'array', 'min:1'],
		]);

		try {
			DB::beginTransaction();

			$feeGroups = FeeGroup::onlyTrashed()->whereIn('id', $request->fee_group_ids)->get();
			$count = $feeGroups->count();

			foreach ($feeGroups as $feeGroup) {
				$feeGroup->forceDelete();
			}

			DB::commit();

			return response()->json([
				'success' => true,
				'message' => "{$count} fee group(s) permanently deleted.",
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

			$feeGroups = FeeGroup::onlyTrashed()->get();
			$count = $feeGroups->count();

			foreach ($feeGroups as $feeGroup) {
				$feeGroup->forceDelete();
			}

			DB::commit();

			return redirect()->route('admin.fees.groups.trash')
				->with('success', "{$count} fee group(s) permanently deleted from trash.");

		} catch (\Exception $e) {
			DB::rollBack();
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}
}
