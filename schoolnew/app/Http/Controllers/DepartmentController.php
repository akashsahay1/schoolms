<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{

    public function index()
    {
        $departments = Department::withCount('staff')->latest()->paginate(10);
        $trashedCount = Department::onlyTrashed()->count();
        return view('departments.index', compact('departments', 'trashedCount'));
    }

    public function create()
    {
        return view('departments.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:departments,name',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        Department::create($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department created successfully.');
    }

    public function edit(Department $department)
    {
        return view('departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:departments,name,' . $department->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        $department->update($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        if ($department->staff()->exists()) {
            return redirect()->route('admin.departments.index')
                ->with('error', 'Cannot delete department with assigned staff.');
        }

        $department->delete();

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department moved to trash successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No departments selected.'], 400);
        }

        $departmentsWithStaff = Department::whereIn('id', $ids)
            ->whereHas('staff')
            ->pluck('name')
            ->toArray();

        if (!empty($departmentsWithStaff)) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete departments with assigned staff: ' . implode(', ', $departmentsWithStaff)
            ], 400);
        }

        Department::whereIn('id', $ids)->delete();

        return response()->json(['success' => true, 'message' => 'Selected departments moved to trash successfully.']);
    }

    public function trash()
    {
        $departments = Department::onlyTrashed()->withCount('staff')->latest('deleted_at')->paginate(10);
        return view('departments.trash', compact('departments'));
    }

    public function restore($id)
    {
        $department = Department::onlyTrashed()->findOrFail($id);
        $department->restore();

        return redirect()->route('admin.departments.trash')
            ->with('success', 'Department restored successfully.');
    }

    public function forceDelete($id)
    {
        $department = Department::onlyTrashed()->findOrFail($id);
        $department->forceDelete();

        return redirect()->route('admin.departments.trash')
            ->with('success', 'Department permanently deleted.');
    }

    public function bulkRestore(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No departments selected.'], 400);
        }

        Department::onlyTrashed()->whereIn('id', $ids)->restore();

        return response()->json(['success' => true, 'message' => 'Selected departments restored successfully.']);
    }

    public function bulkForceDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No departments selected.'], 400);
        }

        Department::onlyTrashed()->whereIn('id', $ids)->forceDelete();

        return response()->json(['success' => true, 'message' => 'Selected departments permanently deleted.']);
    }

    public function emptyTrash()
    {
        Department::onlyTrashed()->forceDelete();

        return redirect()->route('admin.departments.trash')
            ->with('success', 'Trash emptied successfully.');
    }
}