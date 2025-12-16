<?php

namespace App\Http\Controllers;

use App\Models\FeeGroup;
use Illuminate\Http\Request;

class FeeGroupController extends Controller
{
    public function index()
    {
        $feeGroups = FeeGroup::withCount('feeStructures')
            ->orderBy('name')
            ->paginate(15);

        return view('fees.groups.index', compact('feeGroups'));
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
        // Check if fee group is being used in any fee structure
        if ($feeGroup->feeStructures()->exists()) {
            return redirect()->route('admin.fees.groups.index')
                ->with('error', 'Cannot delete fee group. It is being used in fee structures.');
        }

        $feeGroup->delete();

        return redirect()->route('admin.fees.groups.index')
            ->with('success', 'Fee group deleted successfully.');
    }
}
