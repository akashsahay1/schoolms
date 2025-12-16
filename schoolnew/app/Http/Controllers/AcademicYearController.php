<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademicYearController extends Controller
{

    public function index()
    {
        $academicYears = AcademicYear::latest()->paginate(10);
        return view('academic-years.index', compact('academicYears'));
    }

    public function create()
    {
        return view('academic-years.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:academic_years,name',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        DB::transaction(function () use ($validated) {
            if (isset($validated['is_active']) && $validated['is_active']) {
                AcademicYear::where('is_active', true)->update(['is_active' => false]);
            }
            
            AcademicYear::create($validated);
        });

        return redirect()->route('admin.academic-years.index')
            ->with('success', 'Academic year created successfully.');
    }

    public function edit(AcademicYear $academicYear)
    {
        return view('academic-years.edit', compact('academicYear'));
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:academic_years,name,' . $academicYear->id,
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string'
        ]);

        $academicYear->update($validated);

        return redirect()->route('admin.academic-years.index')
            ->with('success', 'Academic year updated successfully.');
    }

    public function destroy(AcademicYear $academicYear)
    {
        if ($academicYear->is_active) {
            return redirect()->route('admin.academic-years.index')
                ->with('error', 'Cannot delete active academic year.');
        }

        if ($academicYear->classes()->exists() || $academicYear->students()->exists()) {
            return redirect()->route('admin.academic-years.index')
                ->with('error', 'Cannot delete academic year with associated data.');
        }

        $academicYear->delete();

        return redirect()->route('admin.academic-years.index')
            ->with('success', 'Academic year deleted successfully.');
    }

    public function setActive(AcademicYear $academicYear)
    {
        DB::transaction(function () use ($academicYear) {
            AcademicYear::where('is_active', true)->update(['is_active' => false]);
            $academicYear->update(['is_active' => true]);
        });

        return redirect()->route('admin.academic-years.index')
            ->with('success', 'Academic year activated successfully.');
    }
}