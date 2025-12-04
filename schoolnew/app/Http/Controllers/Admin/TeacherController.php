<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TeacherController extends Controller
{
	public function index(Request $request)
	{
		$query = Staff::with(['department', 'designation'])->teachers();

		// Search filter
		if ($request->filled('search')) {
			$search = $request->search;
			$query->where(function ($q) use ($search) {
				$q->where('first_name', 'like', "%{$search}%")
					->orWhere('last_name', 'like', "%{$search}%")
					->orWhere('staff_id', 'like', "%{$search}%")
					->orWhere('email', 'like', "%{$search}%");
			});
		}

		// Department filter
		if ($request->filled('department_id')) {
			$query->where('department_id', $request->department_id);
		}

		// Status filter
		if ($request->filled('status')) {
			$query->where('status', $request->status);
		}

		$teachers = $query->latest()->paginate(15);
		$departments = Department::active()->orderBy('name')->get();

		return view('admin.teachers.index', compact('teachers', 'departments'));
	}

	public function create()
	{
		$departments = Department::active()->orderBy('name')->get();
		$designations = Designation::active()->where('name', 'like', '%teacher%')->orderBy('name')->get();
		$subjects = Subject::active()->orderBy('name')->get();

		return view('admin.teachers.create', compact('departments', 'designations', 'subjects'));
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
			'marital_status' => ['nullable', 'string', 'max:20'],
			'nationality' => ['nullable', 'string', 'max:50'],

			// Contact Information
			'email' => ['required', 'email', 'max:255', 'unique:staff,email'],
			'phone' => ['required', 'string', 'max:20'],
			'emergency_contact' => ['nullable', 'string', 'max:20'],
			'current_address' => ['nullable', 'string'],
			'permanent_address' => ['nullable', 'string'],

			// Employment Information
			'department_id' => ['required', 'exists:departments,id'],
			'designation_id' => ['required', 'exists:designations,id'],
			'joining_date' => ['required', 'date_format:d-m-Y'],
			'contract_type' => ['required', 'in:permanent,temporary,contractual'],
			'basic_salary' => ['nullable', 'numeric', 'min:0'],

			// Qualifications
			'qualification' => ['nullable', 'string'],
			'experience' => ['nullable', 'string'],

			// Photo
			'photo' => ['nullable', 'image', 'max:2048'],
		]);

		try {
			DB::beginTransaction();

			// Generate staff ID for teacher
			$lastStaff = Staff::orderBy('id', 'desc')->first();
			$staffId = 'TCH' . str_pad(($lastStaff ? $lastStaff->id + 1 : 1), 5, '0', STR_PAD_LEFT);

			// Handle photo upload
			$photoPath = null;
			if ($request->hasFile('photo')) {
				$photoPath = $request->file('photo')->store('teachers', 'public');
			}

			// Create teacher record (using Staff model)
			$teacher = Staff::create([
				'staff_id' => $staffId,
				'first_name' => $validated['first_name'],
				'last_name' => $validated['last_name'] ?? null,
				'gender' => $validated['gender'],
				'date_of_birth' => Carbon::createFromFormat('d-m-Y', $validated['date_of_birth'])->format('Y-m-d'),
				'blood_group' => $validated['blood_group'] ?? null,
				'religion' => $validated['religion'] ?? null,
				'marital_status' => $validated['marital_status'] ?? null,
				'nationality' => $validated['nationality'] ?? 'Indian',
				'email' => $validated['email'],
				'phone' => $validated['phone'],
				'emergency_contact' => $validated['emergency_contact'] ?? null,
				'current_address' => $validated['current_address'] ?? null,
				'permanent_address' => $validated['permanent_address'] ?? null,
				'department_id' => $validated['department_id'],
				'designation_id' => $validated['designation_id'],
				'joining_date' => Carbon::createFromFormat('d-m-Y', $validated['joining_date'])->format('Y-m-d'),
				'contract_type' => $validated['contract_type'],
				'basic_salary' => $validated['basic_salary'] ?? null,
				'qualification' => $validated['qualification'] ?? null,
				'experience' => $validated['experience'] ?? null,
				'photo' => $photoPath,
				'status' => 'active',
			]);

			DB::commit();

			return redirect()->route('admin.teachers.index')
				->with('success', 'Teacher added successfully. Teacher ID: ' . $staffId);

		} catch (\Exception $e) {
			DB::rollBack();
			return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
		}
	}

	public function show(Staff $teacher)
	{
		$teacher->load(['department', 'designation', 'user']);
		return view('admin.teachers.show', compact('teacher'));
	}

	public function edit(Staff $teacher)
	{
		$departments = Department::active()->orderBy('name')->get();
		$designations = Designation::active()->where('name', 'like', '%teacher%')->orderBy('name')->get();
		$subjects = Subject::active()->orderBy('name')->get();

		return view('admin.teachers.edit', compact('teacher', 'departments', 'designations', 'subjects'));
	}

	public function update(Request $request, Staff $teacher)
	{
		$validated = $request->validate([
			// Basic Information
			'first_name' => ['required', 'string', 'max:255'],
			'last_name' => ['nullable', 'string', 'max:255'],
			'gender' => ['required', 'in:male,female,other'],
			'date_of_birth' => ['required', 'date_format:d-m-Y'],
			'blood_group' => ['nullable', 'string', 'max:5'],
			'religion' => ['nullable', 'string', 'max:50'],
			'marital_status' => ['nullable', 'string', 'max:20'],
			'nationality' => ['nullable', 'string', 'max:50'],

			// Contact Information
			'email' => ['required', 'email', 'max:255', 'unique:staff,email,' . $teacher->id],
			'phone' => ['required', 'string', 'max:20'],
			'emergency_contact' => ['nullable', 'string', 'max:20'],
			'current_address' => ['nullable', 'string'],
			'permanent_address' => ['nullable', 'string'],

			// Employment Information
			'department_id' => ['required', 'exists:departments,id'],
			'designation_id' => ['required', 'exists:designations,id'],
			'contract_type' => ['required', 'in:permanent,temporary,contractual'],
			'basic_salary' => ['nullable', 'numeric', 'min:0'],

			// Qualifications
			'qualification' => ['nullable', 'string'],
			'experience' => ['nullable', 'string'],

			// Photo
			'photo' => ['nullable', 'image', 'max:2048'],

			// Status
			'status' => ['required', 'in:active,inactive,resigned,terminated'],
		]);

		try {
			DB::beginTransaction();

			// Handle photo upload
			if ($request->hasFile('photo')) {
				// Delete old photo
				if ($teacher->photo) {
					Storage::disk('public')->delete($teacher->photo);
				}
				$validated['photo'] = $request->file('photo')->store('teachers', 'public');
			}

			// Update teacher record
			$teacher->update([
				'first_name' => $validated['first_name'],
				'last_name' => $validated['last_name'] ?? null,
				'gender' => $validated['gender'],
				'date_of_birth' => Carbon::createFromFormat('d-m-Y', $validated['date_of_birth'])->format('Y-m-d'),
				'blood_group' => $validated['blood_group'] ?? null,
				'religion' => $validated['religion'] ?? null,
				'marital_status' => $validated['marital_status'] ?? null,
				'nationality' => $validated['nationality'] ?? 'Indian',
				'email' => $validated['email'],
				'phone' => $validated['phone'],
				'emergency_contact' => $validated['emergency_contact'] ?? null,
				'current_address' => $validated['current_address'] ?? null,
				'permanent_address' => $validated['permanent_address'] ?? null,
				'department_id' => $validated['department_id'],
				'designation_id' => $validated['designation_id'],
				'contract_type' => $validated['contract_type'],
				'basic_salary' => $validated['basic_salary'] ?? null,
				'qualification' => $validated['qualification'] ?? null,
				'experience' => $validated['experience'] ?? null,
				'photo' => $validated['photo'] ?? $teacher->photo,
				'status' => $validated['status'],
			]);

			DB::commit();

			return redirect()->route('admin.teachers.index')
				->with('success', 'Teacher updated successfully.');

		} catch (\Exception $e) {
			DB::rollBack();
			return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
		}
	}

	public function destroy(Staff $teacher)
	{
		try {
			// Delete photo if exists
			if ($teacher->photo) {
				Storage::disk('public')->delete($teacher->photo);
			}

			$teacher->delete();

			return redirect()->route('admin.teachers.index')
				->with('success', 'Teacher deleted successfully.');

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}
}
