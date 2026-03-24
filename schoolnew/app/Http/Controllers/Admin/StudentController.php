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
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Traits\HandlesCustomFields;

class StudentController extends Controller
{
    use HandlesCustomFields;

    public function index(Request $request)
    {
        $query = Student::with(['schoolClass', 'section', 'academicYear', 'parent']);

        // Academic session filter — default to active session
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $activeYear = AcademicYear::getActive();

        if ($request->filled('academic_year_id')) {
            $selectedYearId = $request->academic_year_id;
            if ($selectedYearId !== 'all') {
                $query->where('academic_year_id', $selectedYearId);
            }
        } elseif ($activeYear) {
            $selectedYearId = $activeYear->id;
            $query->where('academic_year_id', $activeYear->id);
        } else {
            $selectedYearId = 'all';
        }

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
        $trashedCount = Student::onlyTrashed()->count();

        return view('admin.students.index', compact('students', 'classes', 'academicYears', 'activeYear', 'selectedYearId', 'trashedCount'));
    }

    public function create()
    {
        $classes = SchoolClass::with('sections')->active()->ordered()->get();
        $academicYear = AcademicYear::getActive();
        $customFields = $this->getCustomFields('student');
        $fieldSettings = $this->getFormFieldSettings('student');

        return view('admin.students.create', compact('classes', 'academicYear', 'customFields', 'fieldSettings'));
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
            'roll_no' => ['nullable', 'string', 'max:50', 'unique:students,roll_no,NULL,id,class_id,' . $request->class_id . ',section_id,' . $request->section_id],
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

            // Aadhaar Card
            'aadhaar_number' => ['nullable', 'string', 'size:12', 'regex:/^[0-9]{12}$/'],
            'aadhaar_front' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'aadhaar_back' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],

            // Login Passwords (optional - auto-generate if empty)
            'student_password' => ['nullable', 'string', 'min:6', 'max:50'],
            'parent_password' => ['nullable', 'string', 'min:6', 'max:50'],
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

            // Create parent user account if email is provided
            $parentUserId = null;
            $parentEmail = $validated['father_email'] ?? $validated['mother_email'] ?? null;
            $parentPassword = !empty($validated['parent_password']) ? $validated['parent_password'] : $admissionNo;

            if ($parentEmail) {
                // Check if parent user with this email already exists
                $existingParentUser = User::where('email', $parentEmail)->first();

                if ($existingParentUser) {
                    // Use existing parent user
                    $parentUserId = $existingParentUser->id;
                } else {
                    // Create new parent user account
                    $parentName = $validated['father_name'] ?? $validated['mother_name'] ?? 'Parent';
                    $parentUser = User::create([
                        'name' => $parentName,
                        'email' => $parentEmail,
                        'password' => Hash::make($parentPassword),
                        'plain_password' => $parentPassword,
                    ]);

                    // Assign parent role
                    $parentUser->assignRole('Parent');
                    $parentUserId = $parentUser->id;
                }
            }

            // Create parent record
            $parent = ParentGuardian::create([
                'user_id' => $parentUserId,
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

            // Create user account for student login
            $studentEmail = $validated['email'] ?? strtolower(str_replace(' ', '', $validated['first_name'])) . '.' . $admissionNo . '@student.school.com';
            $studentPassword = !empty($validated['student_password']) ? $validated['student_password'] : $admissionNo;

            $user = User::create([
                'name' => trim($validated['first_name'] . ' ' . ($validated['last_name'] ?? '')),
                'email' => $studentEmail,
                'password' => Hash::make($studentPassword),
                'plain_password' => $studentPassword,
            ]);

            // Assign student role
            $user->assignRole('Student');

            // Create student record
            $student = Student::create([
                'user_id' => $user->id,
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
                'aadhaar_number' => $validated['aadhaar_number'] ?? null,
                'aadhaar_front' => $request->hasFile('aadhaar_front') ? $request->file('aadhaar_front')->store('students/aadhaar', 'public') : null,
                'aadhaar_back' => $request->hasFile('aadhaar_back') ? $request->file('aadhaar_back')->store('students/aadhaar', 'public') : null,
                'status' => 'active',
            ]);

            // Save custom field values
            $this->saveCustomFieldValues($request, $student, 'student');

            DB::commit();

            $message = "Student registered successfully.\n";
            $message .= "Admission No: {$admissionNo}\n";
            $studentPwdNote = !empty($validated['student_password']) ? '' : ' (default: Admission Number)';
            $message .= "Student Login - Email: {$studentEmail}, Password: {$studentPassword}{$studentPwdNote}";

            // Add parent login details if parent account was created
            if ($parentEmail && !isset($existingParentUser)) {
                $parentPwdNote = !empty($validated['parent_password']) ? '' : ' (default: Admission Number)';
                $message .= "\nParent Login - Email: {$parentEmail}, Password: {$parentPassword}{$parentPwdNote}";
            } elseif ($parentEmail && isset($existingParentUser)) {
                $message .= "\nParent Login - Email: {$parentEmail} (existing account, use existing password)";
            }

            return redirect()->route('admin.students.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Student $student)
    {
        $student->load(['schoolClass', 'section', 'academicYear', 'parent', 'user', 'customFieldValues.customField']);
        $customFields = $this->getCustomFields('student');
        $customFieldValues = $this->getCustomFieldValues($student);
        $fieldSettings = $this->getFormFieldSettings('student');

        // Load promotion history for academic progression
        $promotionHistory = \App\Models\StudentPromotion::where('student_id', $student->id)
            ->with(['fromClass', 'toClass', 'fromAcademicYear', 'toAcademicYear'])
            ->orderBy('promoted_at', 'desc')
            ->get();

        // Check if alumni
        $isAlumni = in_array($student->status, ['graduated', 'transferred', 'expelled']);

        return view('admin.students.show', compact('student', 'customFields', 'customFieldValues', 'fieldSettings', 'promotionHistory', 'isAlumni'));
    }

    public function edit(Student $student)
    {
        $student->load(['parent']);
        $classes = SchoolClass::with('sections')->active()->ordered()->get();
        $academicYear = AcademicYear::getActive();
        $customFields = $this->getCustomFields('student');
        $customFieldValues = $this->getCustomFieldValues($student);
        $fieldSettings = $this->getFormFieldSettings('student');
        $isAlumni = in_array($student->status, ['graduated', 'transferred', 'expelled']);

        return view('admin.students.edit', compact('student', 'classes', 'academicYear', 'customFields', 'customFieldValues', 'fieldSettings', 'isAlumni'));
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
            'roll_no' => ['nullable', 'string', 'max:50', 'unique:students,roll_no,' . $student->id . ',id,class_id,' . $request->class_id . ',section_id,' . $request->section_id],

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

            // Aadhaar Card
            'aadhaar_number' => ['nullable', 'string', 'size:12', 'regex:/^[0-9]{12}$/'],
            'aadhaar_front' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'aadhaar_back' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],

            // Status
            'status' => ['required', 'in:active,inactive,graduated,transferred,expelled'],
            'leaving_date' => ['nullable', 'date'],
            'leaving_reason' => ['nullable', 'string', 'max:500'],
        ]);

        // Alumni protection — block academic field changes
        $isAlumni = in_array($student->status, ['graduated', 'transferred', 'expelled']);
        if ($isAlumni) {
            // Force academic fields to stay unchanged
            $validated['class_id'] = $student->class_id;
            $validated['section_id'] = $student->section_id;
            $validated['status'] = $student->status;
            $validated['roll_no'] = $student->roll_no;
        }

        // Status-specific validation
        $newStatus = $validated['status'];
        $oldStatus = $student->status;

        if ($newStatus !== $oldStatus) {
            if ($newStatus === 'graduated') {
                // Only Class 12 students can graduate
                $class12Ids = SchoolClass::where('name', 'like', 'Class 12%')->pluck('id')->toArray();
                if (!in_array($student->class_id, $class12Ids)) {
                    return back()->with('error', 'Only Class 12 students can be marked as Graduated.')->withInput();
                }
            }

            if ($newStatus === 'transferred') {
                if (empty($request->leaving_date)) {
                    return back()->withErrors(['leaving_date' => 'Leaving date is required for transferred students.'])->withInput();
                }
                if (empty($request->leaving_reason)) {
                    return back()->withErrors(['leaving_reason' => 'Leaving reason is required for transferred students.'])->withInput();
                }
            }

            if ($newStatus === 'expelled') {
                if (empty($request->leaving_reason)) {
                    return back()->withErrors(['leaving_reason' => 'Leaving reason is required for expelled students.'])->withInput();
                }
            }
        }

        try {
            DB::beginTransaction();

            // Handle Aadhaar file uploads
            if ($request->hasFile('aadhaar_front')) {
                if ($student->aadhaar_front) {
                    Storage::disk('public')->delete($student->aadhaar_front);
                }
                $validated['aadhaar_front'] = $request->file('aadhaar_front')->store('students/aadhaar', 'public');
            }
            if ($request->hasFile('aadhaar_back')) {
                if ($student->aadhaar_back) {
                    Storage::disk('public')->delete($student->aadhaar_back);
                }
                $validated['aadhaar_back'] = $request->file('aadhaar_back')->store('students/aadhaar', 'public');
            }

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
                'aadhaar_number' => $validated['aadhaar_number'] ?? $student->aadhaar_number,
                'aadhaar_front' => $validated['aadhaar_front'] ?? $student->aadhaar_front,
                'aadhaar_back' => $validated['aadhaar_back'] ?? $student->aadhaar_back,
                'status' => $validated['status'],
            ]);

            // Handle leaving fields when status changes to non-active
            if (in_array($validated['status'], ['graduated', 'transferred', 'expelled']) && $validated['status'] !== $student->getOriginal('status')) {
                $leavingData = [];

                if ($validated['status'] === 'graduated') {
                    $leavingData['leaving_date'] = $student->academicYear?->end_date ?? now();
                    $leavingData['leaving_reason'] = 'Completed Class 12 (' . ($student->academicYear?->name ?? '') . ')';
                } else {
                    $leavingData['leaving_date'] = $validated['leaving_date'] ?? now();
                    $leavingData['leaving_reason'] = $validated['leaving_reason'] ?? null;
                }

                $student->update($leavingData);
            }

            // Clear leaving fields if status changed back to active
            if ($validated['status'] === 'active' && $student->getOriginal('status') !== 'active') {
                $student->update([
                    'leaving_date' => null,
                    'leaving_reason' => null,
                ]);
            }

            // Save custom field values
            $this->saveCustomFieldValues($request, $student, 'student');

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
            $student->delete();

            return redirect()->route('admin.students.index')
                ->with('success', 'Student moved to trash successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function getSections($classId)
    {
        $sections = Section::where('class_id', $classId)->active()->get();
        return response()->json($sections);
    }

    public function idCard(Student $student)
    {
        $student->load(['schoolClass', 'section', 'academicYear', 'parent']);
        return view('admin.students.id-card', compact('student'));
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['exists:students,id'],
        ]);

        try {
            $count = Student::whereIn('id', $request->student_ids)->count();
            Student::whereIn('id', $request->student_ids)->delete();

            return response()->json([
                'success' => true,
                'message' => "{$count} student(s) moved to trash.",
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
        $query = Student::onlyTrashed()->with(['schoolClass', 'section', 'academicYear']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('admission_no', 'like', "%{$search}%");
            });
        }

        $students = $query->latest('deleted_at')->paginate(15);
        $trashedCount = Student::onlyTrashed()->count();

        return view('admin.students.trash', compact('students', 'trashedCount'));
    }

    public function restore($id)
    {
        try {
            $student = Student::onlyTrashed()->findOrFail($id);
            $student->restore();

            return redirect()->route('admin.students.trash')
                ->with('success', "Student '{$student->full_name}' restored successfully.");

        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function forceDelete($id)
    {
        try {
            $student = Student::onlyTrashed()->findOrFail($id);

            // Delete photo and aadhaar files if exist
            foreach (['photo', 'aadhaar_front', 'aadhaar_back'] as $file) {
                if ($student->$file) {
                    Storage::disk('public')->delete($student->$file);
                }
            }

            $name = $student->full_name;
            $parentId = $student->parent_id;
            $userId = $student->user_id;

            $student->forceDelete();

            // Delete parent if no other students linked
            $this->cleanupParent($parentId);

            // Delete student user account if exists
            if ($userId) {
                $user = \App\Models\User::find($userId);
                if ($user) {
                    $user->forceDelete();
                }
            }

            return redirect()->route('admin.students.trash')
                ->with('success', "Student '{$name}' permanently deleted.");

        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function bulkRestore(Request $request)
    {
        $request->validate([
            'student_ids' => ['required', 'array', 'min:1'],
        ]);

        try {
            $count = Student::onlyTrashed()->whereIn('id', $request->student_ids)->count();
            Student::onlyTrashed()->whereIn('id', $request->student_ids)->restore();

            return response()->json([
                'success' => true,
                'message' => "{$count} student(s) restored successfully.",
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
            'student_ids' => ['required', 'array', 'min:1'],
        ]);

        try {
            DB::beginTransaction();

            $students = Student::onlyTrashed()->whereIn('id', $request->student_ids)->get();
            $count = $students->count();

            $parentIds = [];
            $userIds = [];

            foreach ($students as $student) {
                foreach (['photo', 'aadhaar_front', 'aadhaar_back'] as $file) {
                    if ($student->$file) {
                        Storage::disk('public')->delete($student->$file);
                    }
                }
                if ($student->parent_id) {
                    $parentIds[] = $student->parent_id;
                }
                if ($student->user_id) {
                    $userIds[] = $student->user_id;
                }
                $student->forceDelete();
            }

            // Cleanup orphaned parents and user accounts
            foreach (array_unique($parentIds) as $parentId) {
                $this->cleanupParent($parentId);
            }
            if (!empty($userIds)) {
                \App\Models\User::whereIn('id', $userIds)->forceDelete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$count} student(s) permanently deleted.",
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

            $students = Student::onlyTrashed()->get();
            $count = $students->count();

            $parentIds = [];
            $userIds = [];

            foreach ($students as $student) {
                foreach (['photo', 'aadhaar_front', 'aadhaar_back'] as $file) {
                    if ($student->$file) {
                        Storage::disk('public')->delete($student->$file);
                    }
                }
                if ($student->parent_id) {
                    $parentIds[] = $student->parent_id;
                }
                if ($student->user_id) {
                    $userIds[] = $student->user_id;
                }
                $student->forceDelete();
            }

            foreach (array_unique($parentIds) as $parentId) {
                $this->cleanupParent($parentId);
            }
            if (!empty($userIds)) {
                \App\Models\User::whereIn('id', $userIds)->forceDelete();
            }

            DB::commit();

            return redirect()->route('admin.students.trash')
                ->with('success', "{$count} student(s) permanently deleted from trash.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Reset password for a student's user account.
     */
    public function resetPassword(Request $request, Student $student)
    {
        $request->validate([
            'new_password' => 'required|string|min:6|max:50',
        ]);

        if (!$student->user) {
            return back()->with('error', 'No user account linked to this student.');
        }

        $student->user->update([
            'password' => Hash::make($request->new_password),
            'plain_password' => $request->new_password,
        ]);

        return back()->with('success', 'Student password has been reset successfully.');
    }

    public function updateEmail(Request $request, Student $student)
    {
        $request->validate([
            'new_email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' . ($student->user_id ?? 0),
            ],
        ], [
            'new_email.unique' => 'This email is already used by another account. Please use a different email.',
        ]);

        if (!$student->user) {
            return back()->with('error', 'No user account linked to this student.');
        }

        $student->user->update([
            'email' => $request->new_email,
        ]);

        return back()->with('success', 'Login email updated successfully.');
    }

    /**
     * Delete parent and their user account if no other students are linked.
     */
    private function cleanupParent(?int $parentId): void
    {
        if (!$parentId) {
            return;
        }

        $parent = \App\Models\ParentGuardian::withTrashed()->find($parentId);
        if (!$parent) {
            return;
        }

        // Check if any other students (including trashed) still reference this parent
        $otherStudents = Student::withTrashed()->where('parent_id', $parentId)->count();
        if ($otherStudents > 0) {
            return;
        }

        // Delete parent's user account
        if ($parent->user_id) {
            $user = \App\Models\User::find($parent->user_id);
            if ($user) {
                $user->forceDelete();
            }
        }

        // Delete parent record
        $parent->forceDelete();
    }
}
