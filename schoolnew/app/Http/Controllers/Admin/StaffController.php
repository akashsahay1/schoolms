<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Department;
use App\Models\Designation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class StaffController extends Controller
{
	public function index(Request $request)
	{
		$query = Staff::with(['department', 'designation', 'user']);

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

		$staff = $query->latest()->paginate(15);
		$departments = Department::active()->orderBy('name')->get();
		$trashedCount = Staff::onlyTrashed()->count();

		return view('admin.staff.index', compact('staff', 'departments', 'trashedCount'));
	}

	public function create()
	{
		$departments = Department::active()->orderBy('name')->get();
		$designations = Designation::active()->orderBy('name')->get();

		return view('admin.staff.create', compact('departments', 'designations'));
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

			// Login Password (optional - auto-generate if empty)
			'password' => ['nullable', 'string', 'min:6', 'max:50'],
		]);

		try {
			DB::beginTransaction();

			// Generate staff ID
			$lastStaff = Staff::orderBy('id', 'desc')->first();
			$staffId = 'EMP' . str_pad(($lastStaff ? $lastStaff->id + 1 : 1), 5, '0', STR_PAD_LEFT);

			// Handle photo upload
			$photoPath = null;
			if ($request->hasFile('photo')) {
				$photoPath = $request->file('photo')->store('staff', 'public');
			}

			// Create user account for staff login
			$staffPassword = !empty($validated['password']) ? $validated['password'] : $staffId;
			$user = User::create([
				'name' => trim($validated['first_name'] . ' ' . ($validated['last_name'] ?? '')),
				'email' => $validated['email'],
				'password' => Hash::make($staffPassword),
				'plain_password' => $staffPassword,
			]);

			// Determine role based on designation
			$designation = Designation::find($validated['designation_id']);
			$designationName = strtolower($designation->name ?? '');

			// Assign appropriate role
			if (str_contains($designationName, 'teacher') || str_contains($designationName, 'professor') || str_contains($designationName, 'lecturer')) {
				$user->assignRole('Teacher');
			} elseif (str_contains($designationName, 'accountant') || str_contains($designationName, 'finance')) {
				$user->assignRole('Accountant');
			} elseif (str_contains($designationName, 'librarian')) {
				$user->assignRole('Librarian');
			} elseif (str_contains($designationName, 'receptionist') || str_contains($designationName, 'front desk')) {
				$user->assignRole('Receptionist');
			} else {
				$user->assignRole('Teacher'); // Default to Teacher role
			}

			// Create staff record
			$staff = Staff::create([
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

			$pwdNote = !empty($validated['password']) ? '' : ' (auto-generated)';
			return redirect()->route('admin.staff.index')
				->with('success', 'Staff member added successfully. Staff ID: ' . $staffId . '. Login: Email: ' . $validated['email'] . ', Password: ' . $staffPassword . $pwdNote);

		} catch (\Exception $e) {
			DB::rollBack();
			return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
		}
	}

	public function show(Staff $staff)
	{
		$staff->load(['department', 'designation', 'user']);
		return view('admin.staff.show', compact('staff'));
	}

	public function idCard(Staff $staff)
	{
		$staff->load(['department', 'designation']);
		return view('admin.staff.id-card', compact('staff'));
	}

	public function edit(Staff $staff)
	{
		// Prevent non-Super Admin from editing Super Admin staff
		if ($staff->user && $staff->user->hasRole('Super Admin') && !auth()->user()->hasRole('Super Admin')) {
			return redirect()->route('admin.staff.index')
				->with('error', 'You do not have permission to edit Super Admin accounts.');
		}

		$departments = Department::active()->orderBy('name')->get();
		$designations = Designation::active()->orderBy('name')->get();

		return view('admin.staff.edit', compact('staff', 'departments', 'designations'));
	}

	public function update(Request $request, Staff $staff)
	{
		// Prevent non-Super Admin from updating Super Admin staff
		if ($staff->user && $staff->user->hasRole('Super Admin') && !auth()->user()->hasRole('Super Admin')) {
			return redirect()->route('admin.staff.index')
				->with('error', 'You do not have permission to modify Super Admin accounts.');
		}

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
			'email' => ['required', 'email', 'max:255', 'unique:staff,email,' . $staff->id],
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
				if ($staff->photo) {
					Storage::disk('public')->delete($staff->photo);
				}
				$validated['photo'] = $request->file('photo')->store('staff', 'public');
			}

			// Update staff record
			$staff->update([
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
				'photo' => $validated['photo'] ?? $staff->photo,
				'status' => $validated['status'],
			]);

			DB::commit();

			return redirect()->route('admin.staff.index')
				->with('success', 'Staff member updated successfully.');

		} catch (\Exception $e) {
			DB::rollBack();
			return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
		}
	}

	public function destroy(Staff $staff)
	{
		// Prevent non-Super Admin from deleting Super Admin staff
		if ($staff->user && $staff->user->hasRole('Super Admin') && !auth()->user()->hasRole('Super Admin')) {
			return redirect()->route('admin.staff.index')
				->with('error', 'You do not have permission to delete Super Admin accounts.');
		}

		try {
			$staff->delete();

			return redirect()->route('admin.staff.index')
				->with('success', 'Staff member moved to trash successfully.');

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}

	public function bulkDelete(Request $request)
	{
		$request->validate([
			'staff_ids' => ['required', 'array', 'min:1'],
			'staff_ids.*' => ['exists:staff,id'],
		]);

		try {
			// Filter out Super Admin staff if current user is not Super Admin
			$staffIds = $request->staff_ids;
			if (!auth()->user()->hasRole('Super Admin')) {
				$superAdminStaffIds = Staff::whereIn('id', $staffIds)
					->whereHas('user', fn($q) => $q->role('Super Admin'))
					->pluck('id')->toArray();
				$staffIds = array_diff($staffIds, $superAdminStaffIds);
			}

			if (empty($staffIds)) {
				return response()->json([
					'success' => false,
					'message' => 'You do not have permission to delete Super Admin accounts.',
				], 403);
			}

			$count = Staff::whereIn('id', $staffIds)->count();
			Staff::whereIn('id', $staffIds)->delete();

			return response()->json([
				'success' => true,
				'message' => "{$count} staff member(s) moved to trash.",
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
		$query = Staff::onlyTrashed()->with(['department', 'designation']);

		// Search filter
		if ($request->filled('search')) {
			$search = $request->search;
			$query->where(function ($q) use ($search) {
				$q->where('first_name', 'like', "%{$search}%")
					->orWhere('last_name', 'like', "%{$search}%")
					->orWhere('staff_id', 'like', "%{$search}%");
			});
		}

		$staff = $query->latest('deleted_at')->paginate(15);
		$trashedCount = Staff::onlyTrashed()->count();

		return view('admin.staff.trash', compact('staff', 'trashedCount'));
	}

	public function restore($id)
	{
		try {
			$staff = Staff::onlyTrashed()->findOrFail($id);
			$staff->restore();

			return redirect()->route('admin.staff.trash')
				->with('success', "Staff member '{$staff->full_name}' restored successfully.");

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}

	public function forceDelete($id)
	{
		try {
			$staff = Staff::onlyTrashed()->findOrFail($id);

			// Delete photo if exists
			if ($staff->photo) {
				Storage::disk('public')->delete($staff->photo);
			}

			$name = $staff->full_name;
			$staff->forceDelete();

			return redirect()->route('admin.staff.trash')
				->with('success', "Staff member '{$name}' permanently deleted.");

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}

	public function bulkRestore(Request $request)
	{
		$request->validate([
			'staff_ids' => ['required', 'array', 'min:1'],
		]);

		try {
			$count = Staff::onlyTrashed()->whereIn('id', $request->staff_ids)->count();
			Staff::onlyTrashed()->whereIn('id', $request->staff_ids)->restore();

			return response()->json([
				'success' => true,
				'message' => "{$count} staff member(s) restored successfully.",
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
			'staff_ids' => ['required', 'array', 'min:1'],
		]);

		try {
			DB::beginTransaction();

			$staffMembers = Staff::onlyTrashed()->whereIn('id', $request->staff_ids)->get();
			$count = $staffMembers->count();

			foreach ($staffMembers as $staff) {
				// Delete photo if exists
				if ($staff->photo) {
					Storage::disk('public')->delete($staff->photo);
				}
				$staff->forceDelete();
			}

			DB::commit();

			return response()->json([
				'success' => true,
				'message' => "{$count} staff member(s) permanently deleted.",
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

			$staffMembers = Staff::onlyTrashed()->get();
			$count = $staffMembers->count();

			foreach ($staffMembers as $staff) {
				if ($staff->photo) {
					Storage::disk('public')->delete($staff->photo);
				}
				$staff->forceDelete();
			}

			DB::commit();

			return redirect()->route('admin.staff.trash')
				->with('success', "{$count} staff member(s) permanently deleted from trash.");

		} catch (\Exception $e) {
			DB::rollBack();
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}

	/**
	 * Reset password for a staff member's user account.
	 */
	public function resetPassword(Request $request, Staff $staff)
	{
		$request->validate([
			'new_password' => 'required|string|min:6|max:50',
		]);

		if (!$staff->user) {
			return back()->with('error', 'No user account linked to this staff member.');
		}

		// Prevent non-Super Admin from resetting Super Admin password
		if ($staff->user->hasRole('Super Admin') && !auth()->user()->hasRole('Super Admin')) {
			return back()->with('error', 'You do not have permission to reset Super Admin passwords.');
		}

		$staff->user->update([
			'password' => Hash::make($request->new_password),
			'plain_password' => $request->new_password,
		]);

		return back()->with('success', 'Staff password has been reset successfully.');
	}
}
