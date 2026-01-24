<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LeaveTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = LeaveType::query();

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('applicable_to')) {
            $query->where('applicable_to', $request->applicable_to);
        }

        $leaveTypes = $query->orderBy('name')->paginate(15);
        $trashedCount = LeaveType::onlyTrashed()->count();

        return view('admin.staff-leaves.types.index', compact('leaveTypes', 'trashedCount'));
    }

    public function create()
    {
        return view('admin.staff-leaves.types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:20', 'unique:leave_types,code'],
            'description' => ['nullable', 'string', 'max:500'],
            'allowed_days' => ['required', 'integer', 'min:0', 'max:365'],
            'is_paid' => ['nullable', 'boolean'],
            'requires_attachment' => ['nullable', 'boolean'],
            'applicable_to' => ['required', 'in:all,staff,students'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        try {
            LeaveType::create([
                'name' => $validated['name'],
                'code' => strtoupper($validated['code']),
                'description' => $validated['description'] ?? null,
                'allowed_days' => $validated['allowed_days'],
                'is_paid' => $request->has('is_paid'),
                'requires_attachment' => $request->has('requires_attachment'),
                'applicable_to' => $validated['applicable_to'],
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('admin.staff-leaves.types.index')->with('success', 'Leave type created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(LeaveType $type)
    {
        return view('admin.staff-leaves.types.edit', compact('type'));
    }

    public function update(Request $request, LeaveType $type)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:20', Rule::unique('leave_types', 'code')->ignore($type->id)],
            'description' => ['nullable', 'string', 'max:500'],
            'allowed_days' => ['required', 'integer', 'min:0', 'max:365'],
            'is_paid' => ['nullable', 'boolean'],
            'requires_attachment' => ['nullable', 'boolean'],
            'applicable_to' => ['required', 'in:all,staff,students'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        try {
            $type->update([
                'name' => $validated['name'],
                'code' => strtoupper($validated['code']),
                'description' => $validated['description'] ?? null,
                'allowed_days' => $validated['allowed_days'],
                'is_paid' => $request->has('is_paid'),
                'requires_attachment' => $request->has('requires_attachment'),
                'applicable_to' => $validated['applicable_to'],
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('admin.staff-leaves.types.index')->with('success', 'Leave type updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(LeaveType $type)
    {
        try {
            $type->delete();
            return redirect()->route('admin.staff-leaves.types.index')->with('success', 'Leave type moved to trash successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['exists:leave_types,id'],
        ]);

        try {
            DB::beginTransaction();
            LeaveType::whereIn('id', $request->ids)->delete();
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Selected leave types moved to trash successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function trash(Request $request)
    {
        $leaveTypes = LeaveType::onlyTrashed()->orderBy('deleted_at', 'desc')->paginate(15);

        return view('admin.staff-leaves.types.trash', compact('leaveTypes'));
    }

    public function restore($id)
    {
        try {
            $type = LeaveType::onlyTrashed()->findOrFail($id);
            $type->restore();

            return redirect()->route('admin.staff-leaves.types.trash')->with('success', 'Leave type restored successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function forceDelete($id)
    {
        try {
            $type = LeaveType::onlyTrashed()->findOrFail($id);
            $type->forceDelete();

            return redirect()->route('admin.staff-leaves.types.trash')->with('success', 'Leave type permanently deleted.');
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
            DB::beginTransaction();
            LeaveType::onlyTrashed()->whereIn('id', $request->ids)->restore();
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Selected leave types restored successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function bulkForceDelete(Request $request)
    {
        $request->validate([
            'ids' => ['required', 'array'],
        ]);

        try {
            DB::beginTransaction();
            LeaveType::onlyTrashed()->whereIn('id', $request->ids)->forceDelete();
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Selected leave types permanently deleted.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function emptyTrash()
    {
        try {
            DB::beginTransaction();
            LeaveType::onlyTrashed()->forceDelete();
            DB::commit();

            return redirect()->route('admin.staff-leaves.types.trash')->with('success', 'Trash emptied successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
