<?php

namespace App\Http\Controllers;

use App\Models\FeeStructure;
use App\Models\FeeType;
use App\Models\FeeGroup;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeeStructureController extends Controller
{
    public function index(Request $request)
    {
        $query = FeeStructure::with(['academicYear', 'schoolClass', 'feeType', 'feeGroup']);
        
        // Filter by academic year
        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        } else {
            // Default to active academic year
            $activeYear = AcademicYear::getActive();
            if ($activeYear) {
                $query->where('academic_year_id', $activeYear->id);
            }
        }
        
        // Filter by class
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        
        // Filter by fee type
        if ($request->filled('fee_type_id')) {
            $query->where('fee_type_id', $request->fee_type_id);
        }
        
        $feeStructures = $query->latest()->paginate(15);
        
        // Get filter data
        $academicYears = AcademicYear::latest()->get();
        $classes = SchoolClass::ordered()->get();
        $feeTypes = FeeType::active()->get();
        
        return view('fees.structure.index', compact(
            'feeStructures', 
            'academicYears', 
            'classes', 
            'feeTypes'
        ));
    }

    public function create()
    {
        $academicYears = AcademicYear::latest()->get();
        $classes = SchoolClass::ordered()->get();
        $feeTypes = FeeType::active()->get();
        $feeGroups = FeeGroup::active()->get();
        
        return view('fees.structure.create', compact(
            'academicYears',
            'classes',
            'feeTypes',
            'feeGroups'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'class_id' => 'required|exists:classes,id',
            'fee_type_id' => 'required|exists:fee_types,id',
            'fee_group_id' => 'required|exists:fee_groups,id',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
            'fine_type' => 'required|in:none,percentage,fixed',
            'fine_amount' => 'required_unless:fine_type,none|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        try {
            DB::beginTransaction();
            
            // Check for existing fee structure
            $existing = FeeStructure::where('academic_year_id', $validated['academic_year_id'])
                ->where('class_id', $validated['class_id'])
                ->where('fee_type_id', $validated['fee_type_id'])
                ->first();
                
            if ($existing) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Fee structure already exists for this class and fee type in selected academic year.');
            }
            
            FeeStructure::create($validated);
            
            DB::commit();
            
            return redirect()->route('admin.fees.structure')
                ->with('success', 'Fee structure created successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create fee structure. Please try again.');
        }
    }

    public function edit(FeeStructure $feeStructure)
    {
        $academicYears = AcademicYear::latest()->get();
        $classes = SchoolClass::ordered()->get();
        $feeTypes = FeeType::active()->get();
        $feeGroups = FeeGroup::active()->get();
        
        return view('fees.structure.edit', compact(
            'feeStructure',
            'academicYears',
            'classes',
            'feeTypes',
            'feeGroups'
        ));
    }

    public function update(Request $request, FeeStructure $feeStructure)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
            'fine_type' => 'required|in:none,percentage,fixed',
            'fine_amount' => 'required_unless:fine_type,none|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        $feeStructure->update($validated);

        return redirect()->route('admin.fees.structure')
            ->with('success', 'Fee structure updated successfully.');
    }

    public function destroy(FeeStructure $feeStructure)
    {
        // Check if there are any collections
        if ($feeStructure->collections()->exists()) {
            return redirect()->route('admin.fees.structure')
                ->with('error', 'Cannot delete fee structure with existing collections.');
        }

        $feeStructure->delete();

        return redirect()->route('admin.fees.structure')
            ->with('success', 'Fee structure deleted successfully.');
    }

    public function duplicate(Request $request, FeeStructure $feeStructure)
    {
        // Get target academic year and class from request or show selection form
        if (!$request->filled('target_academic_year_id') || !$request->filled('target_class_id')) {
            $academicYears = AcademicYear::latest()->get();
            $classes = SchoolClass::ordered()->get();
            
            return view('fees.structure.duplicate', compact('feeStructure', 'academicYears', 'classes'));
        }

        $validated = $request->validate([
            'target_academic_year_id' => 'required|exists:academic_years,id',
            'target_class_id' => 'required|exists:classes,id',
        ]);

        // Check if combination already exists
        $exists = FeeStructure::where('academic_year_id', $validated['target_academic_year_id'])
            ->where('class_id', $validated['target_class_id'])
            ->where('fee_type_id', $feeStructure->fee_type_id)
            ->exists();

        if ($exists) {
            $academicYear = AcademicYear::find($validated['target_academic_year_id']);
            $class = SchoolClass::find($validated['target_class_id']);
            
            return redirect()->back()
                ->with('error', "Fee structure for {$feeStructure->feeType->name} already exists for {$class->name} in {$academicYear->name}.");
        }

        // Create duplicate with new academic year and class
        $newStructure = $feeStructure->replicate();
        $newStructure->academic_year_id = $validated['target_academic_year_id'];
        $newStructure->class_id = $validated['target_class_id'];
        $newStructure->save();
        
        $academicYear = AcademicYear::find($validated['target_academic_year_id']);
        $class = SchoolClass::find($validated['target_class_id']);
        
        return redirect()->route('admin.fees.structure')
            ->with('success', "Fee structure duplicated successfully to {$class->name} in {$academicYear->name}.");
    }
}