<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');

        // Exclude students, parents, and teachers from the users list
        // These are managed separately under Academic section
        $excludedRoles = ['student', 'parent', 'teacher'];
        $query->whereDoesntHave('roles', function ($q) use ($excludedRoles) {
            $q->whereIn('name', $excludedRoles);
        });

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        $users = $query->latest()->paginate(15);
        // Only show admin roles (not student, parent, teacher)
        $roles = Role::whereNotIn('name', $excludedRoles)->get();

        // Count trashed users (excluding student, parent, teacher roles)
        $trashedCount = User::onlyTrashed()
            ->whereDoesntHave('roles', function ($q) use ($excludedRoles) {
                $q->whereIn('name', $excludedRoles);
            })->count();

        return view('admin.users.index', compact('users', 'roles', 'trashedCount'));
    }

    public function create()
    {
        // Only show admin roles (not student, parent, teacher)
        $excludedRoles = ['student', 'parent', 'teacher'];
        $roles = Role::whereNotIn('name', $excludedRoles)->get();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'exists:roles,name'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $user->load('roles');
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        // Only show admin roles (not student, parent, teacher)
        $excludedRoles = ['student', 'parent', 'teacher'];
        $roles = Role::whereNotIn('name', $excludedRoles)->get();
        $userRole = $user->roles->first()?->name;
        return view('admin.users.edit', compact('user', 'roles', 'userRole'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role' => ['required', 'exists:roles,name'],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        // Update role
        $user->syncRoles([$request->role]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User moved to trash successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['exists:users,id'],
        ]);

        // Filter out current user from deletion
        $userIds = array_filter($request->user_ids, function ($id) {
            return $id != auth()->id();
        });

        if (empty($userIds)) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.',
            ], 400);
        }

        try {
            $count = User::whereIn('id', $userIds)->count();
            User::whereIn('id', $userIds)->delete();

            return response()->json([
                'success' => true,
                'message' => "{$count} user(s) moved to trash.",
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
        $excludedRoles = ['student', 'parent', 'teacher'];

        $query = User::onlyTrashed()->with('roles')
            ->whereDoesntHave('roles', function ($q) use ($excludedRoles) {
                $q->whereIn('name', $excludedRoles);
            });

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest('deleted_at')->paginate(15);
        $trashedCount = User::onlyTrashed()
            ->whereDoesntHave('roles', function ($q) use ($excludedRoles) {
                $q->whereIn('name', $excludedRoles);
            })->count();

        return view('admin.users.trash', compact('users', 'trashedCount'));
    }

    public function restore($id)
    {
        try {
            $user = User::onlyTrashed()->findOrFail($id);
            $user->restore();

            return redirect()->route('admin.users.trash')
                ->with('success', "User '{$user->name}' restored successfully.");

        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function forceDelete($id)
    {
        try {
            $user = User::onlyTrashed()->findOrFail($id);

            // Delete avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $name = $user->name;
            $user->forceDelete();

            return redirect()->route('admin.users.trash')
                ->with('success', "User '{$name}' permanently deleted.");

        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function bulkRestore(Request $request)
    {
        $request->validate([
            'user_ids' => ['required', 'array', 'min:1'],
        ]);

        try {
            $count = User::onlyTrashed()->whereIn('id', $request->user_ids)->count();
            User::onlyTrashed()->whereIn('id', $request->user_ids)->restore();

            return response()->json([
                'success' => true,
                'message' => "{$count} user(s) restored successfully.",
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
            'user_ids' => ['required', 'array', 'min:1'],
        ]);

        try {
            DB::beginTransaction();

            $users = User::onlyTrashed()->whereIn('id', $request->user_ids)->get();
            $count = $users->count();

            foreach ($users as $user) {
                // Delete avatar if exists
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $user->forceDelete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$count} user(s) permanently deleted.",
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

            $excludedRoles = ['student', 'parent', 'teacher'];
            $users = User::onlyTrashed()
                ->whereDoesntHave('roles', function ($q) use ($excludedRoles) {
                    $q->whereIn('name', $excludedRoles);
                })->get();
            $count = $users->count();

            foreach ($users as $user) {
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $user->forceDelete();
            }

            DB::commit();

            return redirect()->route('admin.users.trash')
                ->with('success', "{$count} user(s) permanently deleted from trash.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
