<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\AcademicYear;
use App\Models\ParentGuardian;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['schoolClass', 'section', 'academicYear', 'parent']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('admission_no', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Class filter
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        // Section filter
        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $students = $query->latest()->paginate(15);
        $classes = SchoolClass::with('sections')->active()->ordered()->get();
        $academicYear = AcademicYear::getActive();

        return view('admin.students.index', compact('students', 'classes', 'academicYear'));
    }

    public function create()
    {
        $classes = SchoolClass::with('sections')->active()->ordered()->get();
        $academicYear = AcademicYear::getActive();

        return view('admin.students.create', compact('classes', 'academicYear'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Basic Information
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female,other'],
            'date_of_birth' => ['required', 'date_format:d-m-Y'],
            'blood_group' => ['nullable', 'string', 'max:5'],
            'religion' => ['nullable', 'string', 'max:50'],
            'nationality' => ['nullable', 'string', 'max:50'],
            'mother_tongue' => ['nullable', 'string', 'max:50'],

            // Academic Information
            'class_id' => ['required', 'exists:classes,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'roll_no' => ['nullable', 'string', 'max:50'],
            'admission_date' => ['required', 'date_format:d-m-Y'],
            'previous_school' => ['nullable', 'string', 'max:255'],

            // Contact Information
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'current_address' => ['nullable', 'string'],
            'permanent_address' => ['nullable', 'string'],

            // Parent Information
            'father_name' => ['required', 'string', 'max:255'],
            'father_phone' => ['nullable', 'string', 'max:20'],
            'father_email' => ['nullable', 'email', 'max:255'],
            'father_occupation' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'mother_phone' => ['nullable', 'string', 'max:20'],
            'mother_email' => ['nullable', 'email', 'max:255'],
            'mother_occupation' => ['nullable', 'string', 'max:255'],

            // Photo
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        try {
            DB::beginTransaction();

            $academicYear = AcademicYear::getActive();
            if (!$academicYear) {
                return back()->with('error', 'No active academic year found. Please set up an academic year first.');
            }

            // Generate admission number
            $lastAdmission = Student::where('academic_year_id', $academicYear->id)
                ->orderBy('id', 'desc')
                ->first();
            $admissionNo = 'STU' . $academicYear->id . str_pad(($lastAdmission ? $lastAdmission->id + 1 : 1), 5, '0', STR_PAD_LEFT);

            // Create parent record
            $parent = ParentGuardian::create([
                'father_name' => $validated['father_name'],
                'father_phone' => $validated['father_phone'] ?? null,
                'father_email' => $validated['father_email'] ?? null,
                'father_occupation' => $validated['father_occupation'] ?? null,
                'mother_name' => $validated['mother_name'] ?? null,
                'mother_phone' => $validated['mother_phone'] ?? null,
                'mother_email' => $validated['mother_email'] ?? null,
                'mother_occupation' => $validated['mother_occupation'] ?? null,
                'current_address' => $validated['current_address'] ?? null,
                'permanent_address' => $validated['permanent_address'] ?? null,
            ]);

            // Handle photo upload
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('students', 'public');
            }

            // Create student record
            $student = Student::create([
                'parent_id' => $parent->id,
                'class_id' => $validated['class_id'],
                'section_id' => $validated['section_id'],
                'academic_year_id' => $academicYear->id,
                'admission_no' => $admissionNo,
                'roll_no' => $validated['roll_no'] ?? null,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'] ?? null,
                'gender' => $validated['gender'],
                'date_of_birth' => Carbon::createFromFormat('d-m-Y', $validated['date_of_birth'])->format('Y-m-d'),
                'blood_group' => $validated['blood_group'] ?? null,
                'religion' => $validated['religion'] ?? null,
                'nationality' => $validated['nationality'] ?? 'Indian',
                'mother_tongue' => $validated['mother_tongue'] ?? null,
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'current_address' => $validated['current_address'] ?? null,
                'permanent_address' => $validated['permanent_address'] ?? null,
                'admission_date' => Carbon::createFromFormat('d-m-Y', $validated['admission_date'])->format('Y-m-d'),
                'previous_school' => $validated['previous_school'] ?? null,
                'photo' => $photoPath,
                'status' => 'active',
            ]);

            DB::commit();

            return redirect()->route('admin.students.index')
                ->with('success', 'Student registered successfully. Admission No: ' . $admissionNo);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Student $student)
    {
        $student->load(['schoolClass', 'section', 'academicYear', 'parent', 'user']);
        return view('admin.students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $student->load(['parent']);
        $classes = SchoolClass::with('sections')->active()->ordered()->get();
        $academicYear = AcademicYear::getActive();

        return view('admin.students.edit', compact('student', 'classes', 'academicYear'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            // Basic Information
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female,other'],
            'date_of_birth' => ['required', 'date_format:d-m-Y'],
            'blood_group' => ['nullable', 'string', 'max:5'],
            'religion' => ['nullable', 'string', 'max:50'],
            'nationality' => ['nullable', 'string', 'max:50'],
            'mother_tongue' => ['nullable', 'string', 'max:50'],

            // Academic Information
            'class_id' => ['required', 'exists:classes,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'roll_no' => ['nullable', 'string', 'max:50'],

            // Contact Information
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'current_address' => ['nullable', 'string'],
            'permanent_address' => ['nullable', 'string'],

            // Parent Information
            'father_name' => ['required', 'string', 'max:255'],
            'father_phone' => ['nullable', 'string', 'max:20'],
            'father_email' => ['nullable', 'email', 'max:255'],
            'father_occupation' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'mother_phone' => ['nullable', 'string', 'max:20'],
            'mother_email' => ['nullable', 'email', 'max:255'],
            'mother_occupation' => ['nullable', 'string', 'max:255'],

            // Photo
            'photo' => ['nullable', 'image', 'max:2048'],

            // Status
            'status' => ['required', 'in:active,inactive,graduated,transferred,expelled'],
        ]);

        try {
            DB::beginTransaction();

            // Update parent record
            if ($student->parent) {
                $student->parent->update([
                    'father_name' => $validated['father_name'],
                    'father_phone' => $validated['father_phone'] ?? null,
                    'father_email' => $validated['father_email'] ?? null,
                    'father_occupation' => $validated['father_occupation'] ?? null,
                    'mother_name' => $validated['mother_name'] ?? null,
                    'mother_phone' => $validated['mother_phone'] ?? null,
                    'mother_email' => $validated['mother_email'] ?? null,
                    'mother_occupation' => $validated['mother_occupation'] ?? null,
                    'current_address' => $validated['current_address'] ?? null,
                    'permanent_address' => $validated['permanent_address'] ?? null,
                ]);
            }

            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo
                if ($student->photo) {
                    Storage::disk('public')->delete($student->photo);
                }
                $validated['photo'] = $request->file('photo')->store('students', 'public');
            }

            // Update student record
            $student->update([
                'class_id' => $validated['class_id'],
                'section_id' => $validated['section_id'],
                'roll_no' => $validated['roll_no'] ?? null,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'] ?? null,
                'gender' => $validated['gender'],
                'date_of_birth' => Carbon::createFromFormat('d-m-Y', $validated['date_of_birth'])->format('Y-m-d'),
                'blood_group' => $validated['blood_group'] ?? null,
                'religion' => $validated['religion'] ?? null,
                'nationality' => $validated['nationality'] ?? 'Indian',
                'mother_tongue' => $validated['mother_tongue'] ?? null,
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'current_address' => $validated['current_address'] ?? null,
                'permanent_address' => $validated['permanent_address'] ?? null,
                'photo' => $validated['photo'] ?? $student->photo,
                'status' => $validated['status'],
            ]);

            DB::commit();

            return redirect()->route('admin.students.index')
                ->with('success', 'Student updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Student $student)
    {
        try {
            // Delete photo if exists
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }

            $student->delete();

            return redirect()->route('admin.students.index')
                ->with('success', 'Student deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function getSections($classId)
    {
        $sections = Section::where('class_id', $classId)->active()->get();
        return response()->json($sections);
    }
}
