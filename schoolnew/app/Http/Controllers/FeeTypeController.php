<?php

namespace App\Http\Controllers;

use App\Models\FeeType;
use Illuminate\Http\Request;

class FeeTypeController extends Controller
{
    public function index()
    {
        $feeTypes = FeeType::withCount('feeStructures')
            ->orderBy('name')
            ->paginate(15);

        return view('fees.types.index', compact('feeTypes'));
    }

    public function create()
    {
        return view('fees.types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:fee_types,name',
            'code' => 'required|string|max:50|unique:fee_types,code',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['code'] = strtoupper($validated['code']);

        FeeType::create($validated);

        return redirect()->route('admin.fees.types.index')
            ->with('success', 'Fee type created successfully.');
    }

    public function edit(FeeType $feeType)
    {
        return view('fees.types.edit', compact('feeType'));
    }

    public function update(Request $request, FeeType $feeType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:fee_types,name,' . $feeType->id,
            'code' => 'required|string|max:50|unique:fee_types,code,' . $feeType->id,
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['code'] = strtoupper($validated['code']);

        $feeType->update($validated);

        return redirect()->route('admin.fees.types.index')
            ->with('success', 'Fee type updated successfully.');
    }

    public function destroy(FeeType $feeType)
    {
        // Check if fee type is being used in any fee structure
        if ($feeType->feeStructures()->exists()) {
            return redirect()->route('admin.fees.types.index')
                ->with('error', 'Cannot delete fee type. It is being used in fee structures.');
        }

        $feeType->delete();

        return redirect()->route('admin.fees.types.index')
            ->with('success', 'Fee type deleted successfully.');
    }
}
