<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ParentGuardian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ParentController extends Controller
{
	public function index(Request $request)
	{
		$query = ParentGuardian::with('students');

		// Search filter
		if ($request->filled('search')) {
			$search = $request->search;
			$query->where(function ($q) use ($search) {
				$q->where('father_name', 'like', "%{$search}%")
					->orWhere('mother_name', 'like', "%{$search}%")
					->orWhere('guardian_name', 'like', "%{$search}%")
					->orWhere('father_email', 'like', "%{$search}%")
					->orWhere('mother_email', 'like', "%{$search}%")
					->orWhere('father_phone', 'like', "%{$search}%")
					->orWhere('mother_phone', 'like', "%{$search}%");
			});
		}

		$parents = $query->latest()->paginate(15);
		$trashedCount = ParentGuardian::onlyTrashed()->count();

		return view('admin.parents.index', compact('parents', 'trashedCount'));
	}

	public function show(ParentGuardian $parent)
	{
		$parent->load(['students.schoolClass', 'students.section', 'user']);
		return view('admin.parents.show', compact('parent'));
	}

	public function destroy(ParentGuardian $parent)
	{
		$parent->delete();

		return redirect()->route('admin.parents.index')
			->with('success', 'Parent moved to trash successfully.');
	}

	public function bulkDelete(Request $request)
	{
		$request->validate([
			'parent_ids' => ['required', 'array', 'min:1'],
			'parent_ids.*' => ['exists:parents,id'],
		]);

		try {
			$count = ParentGuardian::whereIn('id', $request->parent_ids)->count();
			ParentGuardian::whereIn('id', $request->parent_ids)->delete();

			return response()->json([
				'success' => true,
				'message' => "{$count} parent(s) moved to trash.",
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
		$query = ParentGuardian::onlyTrashed()->with('students');

		// Search filter
		if ($request->filled('search')) {
			$search = $request->search;
			$query->where(function ($q) use ($search) {
				$q->where('father_name', 'like', "%{$search}%")
					->orWhere('mother_name', 'like', "%{$search}%")
					->orWhere('guardian_name', 'like', "%{$search}%")
					->orWhere('father_email', 'like', "%{$search}%")
					->orWhere('mother_email', 'like', "%{$search}%");
			});
		}

		$parents = $query->latest('deleted_at')->paginate(15);
		$trashedCount = ParentGuardian::onlyTrashed()->count();

		return view('admin.parents.trash', compact('parents', 'trashedCount'));
	}

	public function restore($id)
	{
		try {
			$parent = ParentGuardian::onlyTrashed()->findOrFail($id);
			$parent->restore();

			$name = $parent->father_name ?? $parent->mother_name ?? $parent->guardian_name ?? 'Parent';
			return redirect()->route('admin.parents.trash')
				->with('success', "'{$name}' restored successfully.");

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}

	public function forceDelete($id)
	{
		try {
			$parent = ParentGuardian::onlyTrashed()->findOrFail($id);

			// Delete photos if they exist
			if ($parent->father_photo) {
				Storage::disk('public')->delete($parent->father_photo);
			}
			if ($parent->mother_photo) {
				Storage::disk('public')->delete($parent->mother_photo);
			}
			if ($parent->guardian_photo) {
				Storage::disk('public')->delete($parent->guardian_photo);
			}

			$name = $parent->father_name ?? $parent->mother_name ?? $parent->guardian_name ?? 'Parent';
			$parent->forceDelete();

			return redirect()->route('admin.parents.trash')
				->with('success', "'{$name}' permanently deleted.");

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}

	public function bulkRestore(Request $request)
	{
		$request->validate([
			'parent_ids' => ['required', 'array', 'min:1'],
		]);

		try {
			$count = ParentGuardian::onlyTrashed()->whereIn('id', $request->parent_ids)->count();
			ParentGuardian::onlyTrashed()->whereIn('id', $request->parent_ids)->restore();

			return response()->json([
				'success' => true,
				'message' => "{$count} parent(s) restored successfully.",
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
			'parent_ids' => ['required', 'array', 'min:1'],
		]);

		try {
			DB::beginTransaction();

			$parents = ParentGuardian::onlyTrashed()->whereIn('id', $request->parent_ids)->get();
			$count = $parents->count();

			foreach ($parents as $parent) {
				// Delete photos if they exist
				if ($parent->father_photo) {
					Storage::disk('public')->delete($parent->father_photo);
				}
				if ($parent->mother_photo) {
					Storage::disk('public')->delete($parent->mother_photo);
				}
				if ($parent->guardian_photo) {
					Storage::disk('public')->delete($parent->guardian_photo);
				}
				$parent->forceDelete();
			}

			DB::commit();

			return response()->json([
				'success' => true,
				'message' => "{$count} parent(s) permanently deleted.",
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

			$parents = ParentGuardian::onlyTrashed()->get();
			$count = $parents->count();

			foreach ($parents as $parent) {
				// Delete photos if they exist
				if ($parent->father_photo) {
					Storage::disk('public')->delete($parent->father_photo);
				}
				if ($parent->mother_photo) {
					Storage::disk('public')->delete($parent->mother_photo);
				}
				if ($parent->guardian_photo) {
					Storage::disk('public')->delete($parent->guardian_photo);
				}
				$parent->forceDelete();
			}

			DB::commit();

			return redirect()->route('admin.parents.trash')
				->with('success', "{$count} parent(s) permanently deleted from trash.");

		} catch (\Exception $e) {
			DB::rollBack();
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}
}
