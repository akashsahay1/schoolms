<?php

namespace App\Http\Controllers\Admin\Administration;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\SmStaff;
use App\Scopes\ActiveStatusSchoolScope;
use Modules\RolePermission\Entities\InfixRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;

class AdminUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }

    /**
     * Display list of admin users (excluding Students, Parents, Teachers)
     * Only shows users with roles: Admin, Accountant, Receptionist, Librarian, etc.
     */
    public function index()
    {
        try {
            // Get roles that are NOT student (2), parent (3), or teacher (4)
            $excluded_roles = [2, 3, 4]; // Student, Parent, Teacher

            $users = User::whereNotIn('role_id', $excluded_roles)
                ->where('school_id', Auth::user()->school_id)
                ->with('roles')
                ->orderBy('id', 'desc')
                ->get();

            // Get available roles for filtering (excluding Student, Parent, Teacher)
            $roles = InfixRole::where('is_saas', 0)
                ->where('active_status', 1)
                ->whereNotIn('id', $excluded_roles)
                ->where(function ($q) {
                    $q->where('school_id', Auth::user()->school_id)->orWhere('type', 'System');
                })
                ->orderBy('name', 'asc')
                ->get();

            return view('backEnd.administration.users.index', compact('users', 'roles'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    /**
     * Show the form for creating a new admin user
     */
    public function create()
    {
        try {
            // Get roles that are NOT student (2), parent (3), or teacher (4)
            $excluded_roles = [2, 3, 4];

            $roles = InfixRole::where('is_saas', 0)
                ->where('active_status', 1)
                ->whereNotIn('id', $excluded_roles)
                ->where(function ($q) {
                    $q->where('school_id', Auth::user()->school_id)->orWhere('type', 'System');
                })
                ->orderBy('name', 'asc')
                ->get();

            return view('backEnd.administration.users.create', compact('roles'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    /**
     * Store a newly created admin user
     */
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role_id' => 'required|exists:roles,id',
            'password' => 'required|min:6|confirmed',
        ]);

        try {
            DB::beginTransaction();

            // Prevent creating Student, Parent, or Teacher from this form
            $excluded_roles = [2, 3, 4];
            if (in_array($request->role_id, $excluded_roles)) {
                Toastr::error('Cannot create Student, Parent or Teacher from this form', 'Failed');
                return redirect()->back();
            }

            $user = new User();
            $user->role_id = $request->role_id;
            $user->full_name = $request->full_name;
            $user->username = $request->email;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->school_id = Auth::user()->school_id;
            $user->active_status = 1;
            $user->save();

            DB::commit();

            Toastr::success('User created successfully', 'Success');
            return redirect()->route('admin.users.index');
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Operation Failed: ' . $e->getMessage(), 'Failed');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Show the form for editing a user
     */
    public function edit($id)
    {
        try {
            $user = User::where('id', $id)
                ->where('school_id', Auth::user()->school_id)
                ->firstOrFail();

            // Prevent editing Student, Parent, or Teacher from this form
            $excluded_roles = [2, 3, 4];
            if (in_array($user->role_id, $excluded_roles)) {
                Toastr::error('Cannot edit Student, Parent or Teacher from this form', 'Failed');
                return redirect()->back();
            }

            $roles = InfixRole::where('is_saas', 0)
                ->where('active_status', 1)
                ->whereNotIn('id', $excluded_roles)
                ->where(function ($q) {
                    $q->where('school_id', Auth::user()->school_id)->orWhere('type', 'System');
                })
                ->orderBy('name', 'asc')
                ->get();

            return view('backEnd.administration.users.edit', compact('user', 'roles'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|min:6|confirmed',
        ]);

        try {
            $user = User::where('id', $id)
                ->where('school_id', Auth::user()->school_id)
                ->firstOrFail();

            // Prevent updating to Student, Parent, or Teacher role
            $excluded_roles = [2, 3, 4];
            if (in_array($request->role_id, $excluded_roles)) {
                Toastr::error('Cannot assign Student, Parent or Teacher role from this form', 'Failed');
                return redirect()->back();
            }

            $user->role_id = $request->role_id;
            $user->full_name = $request->full_name;
            $user->username = $request->email;
            $user->email = $request->email;
            $user->phone = $request->phone;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            Toastr::success('User updated successfully', 'Success');
            return redirect()->route('admin.users.index');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Toggle user status (enable/disable)
     */
    public function toggleStatus(Request $request)
    {
        try {
            $user = User::where('id', $request->id)
                ->where('school_id', Auth::user()->school_id)
                ->firstOrFail();

            // Prevent toggling Admin user status
            if ($user->role_id == 1 && $user->id != Auth::user()->id) {
                return response()->json(['error' => 'Cannot disable admin user'], 403);
            }

            $user->active_status = $request->status == 'on' ? 1 : 0;
            $user->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Operation Failed'], 500);
        }
    }

    /**
     * Delete a user
     */
    public function destroy($id)
    {
        try {
            $user = User::where('id', $id)
                ->where('school_id', Auth::user()->school_id)
                ->firstOrFail();

            // Prevent deleting Student, Parent, or Teacher from this form
            $excluded_roles = [2, 3, 4];
            if (in_array($user->role_id, $excluded_roles)) {
                Toastr::error('Cannot delete Student, Parent or Teacher from this form', 'Failed');
                return redirect()->back();
            }

            // Prevent self-deletion
            if ($user->id == Auth::user()->id) {
                Toastr::error('Cannot delete your own account', 'Failed');
                return redirect()->back();
            }

            // Check if user has associated staff record and delete it
            $staff = SmStaff::withOutGlobalScope(ActiveStatusSchoolScope::class)
                ->where('user_id', $id)
                ->first();

            if ($staff) {
                $staff->delete();
            }

            $user->delete();

            Toastr::success('User deleted successfully', 'Success');
            return redirect()->route('admin.users.index');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    /**
     * Search users with filters
     */
    public function search(Request $request)
    {
        try {
            $excluded_roles = [2, 3, 4];

            $query = User::whereNotIn('role_id', $excluded_roles)
                ->where('school_id', Auth::user()->school_id);

            if ($request->filled('role_id')) {
                $query->where('role_id', $request->role_id);
            }

            if ($request->filled('search_name')) {
                $query->where('full_name', 'like', '%' . $request->search_name . '%');
            }

            if ($request->filled('search_email')) {
                $query->where('email', 'like', '%' . $request->search_email . '%');
            }

            $users = $query->with('roles')->orderBy('id', 'desc')->get();

            $roles = InfixRole::where('is_saas', 0)
                ->where('active_status', 1)
                ->whereNotIn('id', $excluded_roles)
                ->where(function ($q) {
                    $q->where('school_id', Auth::user()->school_id)->orWhere('type', 'System');
                })
                ->orderBy('name', 'asc')
                ->get();

            return view('backEnd.administration.users.index', compact('users', 'roles'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
}
