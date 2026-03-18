<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Designation;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TeacherController extends Controller
{
	use \App\Traits\HandlesCustomFields;

	public function index(Request $request)
	{
		$query = Staff::with(['subject', 'designation'])->teachers();

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

		// Subject filter
		if ($request->filled('subject_id')) {
			$query->where('subject_id', $request->subject_id);
		}

		// Status filter
		if ($request->filled('status')) {
			$query->where('status', $request->status);
		}

		$teachers = $query->latest()->paginate(15);
		$subjects = Subject::active()->orderBy('name')->get();
		$trashedCount = Staff::onlyTrashed()->teachers()->count();

		return view('admin.teachers.index', compact('teachers', 'subjects', 'trashedCount'));
	}

	public function create()
	{
		$designations = Designation::active()->whereIn('name', ['Principal', 'Vice Principal', 'Class Teacher', 'Subject Teacher', 'Assistant Teacher'])->orderBy('name')->get();
		$subjects = Subject::active()->orderBy('name')->get();
		$customFields = $this->getCustomFields('teacher');
		$fieldSettings = $this->getFormFieldSettings('teacher');

		return view('admin.teachers.create', compact('designations', 'subjects', 'customFields', 'fieldSettings'));
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
			'subject_id' => ['required', 'exists:subjects,id'],
			'designation_id' => ['required', 'exists:designations,id'],
			'joining_date' => ['required', 'date_format:d-m-Y'],
			'contract_type' => ['required', 'in:permanent,temporary,contractual'],
			'basic_salary' => ['nullable', 'numeric', 'min:0'],

			// Qualifications
			'qualification' => ['nullable', 'string'],
			'experience' => ['nullable', 'string'],

			// Photo
			'photo' => ['nullable', 'image', 'max:2048'],

			// Aadhaar & PAN Card
			'aadhaar_number' => ['nullable', 'string', 'size:12', 'regex:/^[0-9]{12}$/'],
			'aadhaar_front' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
			'aadhaar_back' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
			'pan_number' => ['nullable', 'string', 'max:10', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/i'],
			'pan_front' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],

			// Login Password (optional - auto-generate if empty)
			'password' => ['nullable', 'string', 'min:6', 'max:50'],
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

			// Create user account for teacher login
			$teacherPassword = !empty($validated['password']) ? $validated['password'] : $staffId;
			$user = User::create([
				'name' => trim($validated['first_name'] . ' ' . ($validated['last_name'] ?? '')),
				'email' => $validated['email'],
				'password' => Hash::make($teacherPassword),
			]);

			// Assign Teacher role
			$user->assignRole('Teacher');

			// Create teacher record (using Staff model)
			$teacher = Staff::create([
				'user_id' => $user->id,
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
				'subject_id' => $validated['subject_id'],
				'designation_id' => $validated['designation_id'],
				'joining_date' => Carbon::createFromFormat('d-m-Y', $validated['joining_date'])->format('Y-m-d'),
				'contract_type' => $validated['contract_type'],
				'basic_salary' => $validated['basic_salary'] ?? null,
				'qualification' => $validated['qualification'] ?? null,
				'experience' => $validated['experience'] ?? null,
				'photo' => $photoPath,
				'aadhaar_number' => $validated['aadhaar_number'] ?? null,
				'aadhaar_front' => $request->hasFile('aadhaar_front') ? $request->file('aadhaar_front')->store('staff/aadhaar', 'public') : null,
				'aadhaar_back' => $request->hasFile('aadhaar_back') ? $request->file('aadhaar_back')->store('staff/aadhaar', 'public') : null,
				'pan_number' => !empty($validated['pan_number']) ? strtoupper($validated['pan_number']) : null,
				'pan_front' => $request->hasFile('pan_front') ? $request->file('pan_front')->store('staff/pan', 'public') : null,
				'status' => 'active',
			]);

			// Save custom field values
			$this->saveCustomFieldValues($request, $staff, 'teacher');

			DB::commit();

			$pwdNote = !empty($validated['password']) ? '' : ' (auto-generated)';
			return redirect()->route('admin.teachers.index')
				->with('success', 'Teacher added successfully. Teacher ID: ' . $staffId . '. Login: Email: ' . $validated['email'] . ', Password: ' . $teacherPassword . $pwdNote);

		} catch (\Exception $e) {
			DB::rollBack();
			return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
		}
	}

	public function show(Staff $teacher)
	{
		$teacher->load(['subject', 'designation', 'user']);
		return view('admin.teachers.show', compact('teacher'));
	}

	public function edit(Staff $teacher)
	{
		$designations = Designation::active()->whereIn('name', ['Principal', 'Vice Principal', 'Class Teacher', 'Subject Teacher', 'Assistant Teacher'])->orderBy('name')->get();
		$subjects = Subject::active()->orderBy('name')->get();
		$customFields = $this->getCustomFields('teacher');
		$customFieldValues = $this->getCustomFieldValues($teacher);
		$fieldSettings = $this->getFormFieldSettings('teacher');

		return view('admin.teachers.edit', compact('teacher', 'designations', 'subjects', 'customFields', 'customFieldValues', 'fieldSettings'));
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
			'subject_id' => ['required', 'exists:subjects,id'],
			'designation_id' => ['required', 'exists:designations,id'],
			'contract_type' => ['required', 'in:permanent,temporary,contractual'],
			'basic_salary' => ['nullable', 'numeric', 'min:0'],

			// Qualifications
			'qualification' => ['nullable', 'string'],
			'experience' => ['nullable', 'string'],

			// Photo
			'photo' => ['nullable', 'image', 'max:2048'],

			// Aadhaar & PAN Card
			'aadhaar_number' => ['nullable', 'string', 'size:12', 'regex:/^[0-9]{12}$/'],
			'aadhaar_front' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
			'aadhaar_back' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
			'pan_number' => ['nullable', 'string', 'max:10', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/i'],
			'pan_front' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],

			// Status
			'status' => ['required', 'in:active,inactive,resigned,terminated'],
		]);

		try {
			DB::beginTransaction();

			// Handle photo upload
			if ($request->hasFile('photo')) {
				if ($teacher->photo) {
					Storage::disk('public')->delete($teacher->photo);
				}
				$validated['photo'] = $request->file('photo')->store('teachers', 'public');
			}

			// Handle Aadhaar/PAN file uploads
			foreach (['aadhaar_front', 'aadhaar_back'] as $file) {
				if ($request->hasFile($file)) {
					if ($teacher->$file) {
						Storage::disk('public')->delete($teacher->$file);
					}
					$validated[$file] = $request->file($file)->store('staff/aadhaar', 'public');
				}
			}
			if ($request->hasFile('pan_front')) {
				if ($teacher->pan_front) {
					Storage::disk('public')->delete($teacher->pan_front);
				}
				$validated['pan_front'] = $request->file('pan_front')->store('staff/pan', 'public');
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
				'subject_id' => $validated['subject_id'],
				'designation_id' => $validated['designation_id'],
				'contract_type' => $validated['contract_type'],
				'basic_salary' => $validated['basic_salary'] ?? null,
				'qualification' => $validated['qualification'] ?? null,
				'experience' => $validated['experience'] ?? null,
				'photo' => $validated['photo'] ?? $teacher->photo,
				'aadhaar_number' => $validated['aadhaar_number'] ?? $teacher->aadhaar_number,
				'aadhaar_front' => $validated['aadhaar_front'] ?? $teacher->aadhaar_front,
				'aadhaar_back' => $validated['aadhaar_back'] ?? $teacher->aadhaar_back,
				'pan_number' => isset($validated['pan_number']) ? strtoupper($validated['pan_number']) : $teacher->pan_number,
				'pan_front' => $validated['pan_front'] ?? $teacher->pan_front,
				'status' => $validated['status'],
			]);

			// Save custom field values
			$this->saveCustomFieldValues($request, $teacher, 'teacher');

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
			// Check class-subject assignments
			$assignments = \DB::table('class_subject')->where('teacher_id', $teacher->id)->count();
			$teacher->delete();

			$msg = "Teacher \"{$teacher->full_name}\" moved to trash.";
			if ($assignments > 0) {
				$msg .= " They had {$assignments} class-subject assignment(s) which will be kept. Restore the teacher to reactivate them, or permanently delete to clear them.";
			}

			return redirect()->route('admin.teachers.index')->with('success', $msg);

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}

	public function bulkDelete(Request $request)
	{
		$request->validate([
			'teacher_ids' => ['required', 'array', 'min:1'],
			'teacher_ids.*' => ['exists:staff,id'],
		]);

		try {
			$count = Staff::whereIn('id', $request->teacher_ids)->count();
			Staff::whereIn('id', $request->teacher_ids)->delete();

			return response()->json([
				'success' => true,
				'message' => "{$count} teacher(s) moved to trash.",
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
		$query = Staff::onlyTrashed()->with(['subject', 'designation'])->teachers();

		// Search filter
		if ($request->filled('search')) {
			$search = $request->search;
			$query->where(function ($q) use ($search) {
				$q->where('first_name', 'like', "%{$search}%")
					->orWhere('last_name', 'like', "%{$search}%")
					->orWhere('staff_id', 'like', "%{$search}%");
			});
		}

		$teachers = $query->latest('deleted_at')->paginate(15);
		$trashedCount = Staff::onlyTrashed()->teachers()->count();

		return view('admin.teachers.trash', compact('teachers', 'trashedCount'));
	}

	public function restore($id)
	{
		try {
			$teacher = Staff::onlyTrashed()->findOrFail($id);
			$teacher->restore();

			return redirect()->route('admin.teachers.trash')
				->with('success', "Teacher '{$teacher->full_name}' restored successfully.");

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}

	public function forceDelete($id)
	{
		try {
			$teacher = Staff::onlyTrashed()->findOrFail($id);

			// Delete photo if exists
			if ($teacher->photo) {
				Storage::disk('public')->delete($teacher->photo);
			}

			$name = $teacher->full_name;
			$teacher->forceDelete();

			return redirect()->route('admin.teachers.trash')
				->with('success', "Teacher '{$name}' permanently deleted.");

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}

	public function bulkRestore(Request $request)
	{
		$request->validate([
			'teacher_ids' => ['required', 'array', 'min:1'],
		]);

		try {
			$count = Staff::onlyTrashed()->whereIn('id', $request->teacher_ids)->count();
			Staff::onlyTrashed()->whereIn('id', $request->teacher_ids)->restore();

			return response()->json([
				'success' => true,
				'message' => "{$count} teacher(s) restored successfully.",
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
			'teacher_ids' => ['required', 'array', 'min:1'],
		]);

		try {
			DB::beginTransaction();

			$teachers = Staff::onlyTrashed()->whereIn('id', $request->teacher_ids)->get();
			$count = $teachers->count();

			foreach ($teachers as $teacher) {
				// Delete photo if exists
				if ($teacher->photo) {
					Storage::disk('public')->delete($teacher->photo);
				}
				$teacher->forceDelete();
			}

			DB::commit();

			return response()->json([
				'success' => true,
				'message' => "{$count} teacher(s) permanently deleted.",
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

			$teachers = Staff::onlyTrashed()->teachers()->get();
			$count = $teachers->count();

			foreach ($teachers as $teacher) {
				if ($teacher->photo) {
					Storage::disk('public')->delete($teacher->photo);
				}
				$teacher->forceDelete();
			}

			DB::commit();

			return redirect()->route('admin.teachers.trash')
				->with('success', "{$count} teacher(s) permanently deleted from trash.");

		} catch (\Exception $e) {
			DB::rollBack();
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}

	/**
	 * Reset password for a teacher's user account.
	 */
	public function resetPassword(Request $request, Staff $teacher)
	{
		$request->validate([
			'new_password' => 'required|string|min:6|max:50',
		]);

		if (!$teacher->user) {
			return back()->with('error', 'No user account linked to this teacher.');
		}

		$teacher->user->update([
			'password' => Hash::make($request->new_password),
			'plain_password' => $request->new_password,
		]);

		return back()->with('success', 'Teacher password has been reset successfully.');
	}
}
