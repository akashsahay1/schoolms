<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DesignationController extends Controller
{

    public function index()
    {
        $designations = Designation::withCount('staff')->latest()->paginate(10);
        $trashedCount = Designation::onlyTrashed()->count();
        return view('designations.index', compact('designations', 'trashedCount'));
    }

    public function create()
    {
        return view('designations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:designations,name',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        Designation::create($validated);

        return redirect()->route('admin.designations.index')
            ->with('success', 'Designation created successfully.');
    }

    public function edit(Designation $designation)
    {
        return view('designations.edit', compact('designation'));
    }

    public function update(Request $request, Designation $designation)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:designations,name,' . $designation->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        $designation->update($validated);

        return redirect()->route('admin.designations.index')
            ->with('success', 'Designation updated successfully.');
    }

    public function destroy(Designation $designation)
    {
        if ($designation->staff()->exists()) {
            return redirect()->route('admin.designations.index')
                ->with('error', 'Cannot delete designation with assigned staff.');
        }

        $designation->delete();

        return redirect()->route('admin.designations.index')
            ->with('success', 'Designation moved to trash successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No designations selected.'], 400);
        }

        $designationsWithStaff = Designation::whereIn('id', $ids)
            ->whereHas('staff')
            ->pluck('name')
            ->toArray();

        if (!empty($designationsWithStaff)) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete designations with assigned staff: ' . implode(', ', $designationsWithStaff)
            ], 400);
        }

        DB::beginTransaction();
        try {
            Designation::whereIn('id', $ids)->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($ids) . ' designation(s) moved to trash successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to delete designations.'], 500);
        }
    }

    public function trash()
    {
        $designations = Designation::onlyTrashed()->latest()->paginate(10);
        return view('designations.trash', compact('designations'));
    }

    public function restore($id)
    {
        $designation = Designation::onlyTrashed()->findOrFail($id);
        $designation->restore();

        return redirect()->route('admin.designations.trash')
            ->with('success', 'Designation restored successfully.');
    }

    public function forceDelete($id)
    {
        $designation = Designation::onlyTrashed()->findOrFail($id);
        $designation->forceDelete();

        return redirect()->route('admin.designations.trash')
            ->with('success', 'Designation permanently deleted.');
    }

    public function bulkRestore(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No designations selected.'], 400);
        }

        DB::beginTransaction();
        try {
            Designation::onlyTrashed()->whereIn('id', $ids)->restore();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($ids) . ' designation(s) restored successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to restore designations.'], 500);
        }
    }

    public function bulkForceDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No designations selected.'], 400);
        }

        DB::beginTransaction();
        try {
            Designation::onlyTrashed()->whereIn('id', $ids)->forceDelete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($ids) . ' designation(s) permanently deleted.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to delete designations.'], 500);
        }
    }

    public function emptyTrash()
    {
        DB::beginTransaction();
        try {
            Designation::onlyTrashed()->forceDelete();
            DB::commit();

            return redirect()->route('admin.designations.trash')
                ->with('success', 'Trash emptied successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.designations.trash')
                ->with('error', 'Failed to empty trash.');
        }
    }
}