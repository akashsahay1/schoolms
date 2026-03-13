<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of the roles.
     */
    public function index(Request $request)
    {
        $query = Role::with('permissions')->withCount(['users', 'permissions']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $roles = $query->orderBy('name')->paginate(15);

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $permissions = Permission::orderBy('name')->get();
        $permissionGroups = $this->groupPermissions($permissions);

        return view('admin.roles.create', compact('permissions', 'permissionGroups'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role = Role::create(['name' => $request->name]);

        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)->pluck('name');
            $role->syncPermissions($permissions);
        }

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$role->name}' created successfully.");
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        $role->load('permissions');
        $permissionGroups = $this->groupPermissions($role->permissions);
        $usersCount = $role->users()->count();

        return view('admin.roles.show', compact('role', 'permissionGroups', 'usersCount'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('name')->get();
        $permissionGroups = $this->groupPermissions($permissions);
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'permissionGroups', 'rolePermissions'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role)
    {
        // Prevent editing Super Admin role name
        if ($role->name === 'Super Admin' && $request->name !== 'Super Admin') {
            return redirect()->back()
                ->with('error', 'Cannot change the name of Super Admin role.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role->update(['name' => $request->name]);

        // Sync permissions
        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)->pluck('name');
            $role->syncPermissions($permissions);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$role->name}' updated successfully.");
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role)
    {
        // Prevent deleting core roles
        $protectedRoles = ['Super Admin', 'Admin', 'Student', 'Parent', 'Teacher'];

        if (in_array($role->name, $protectedRoles)) {
            return redirect()->route('admin.roles.index')
                ->with('error', "Cannot delete the '{$role->name}' role. It is a protected system role.");
        }

        // Check if role has users
        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')
                ->with('error', "Cannot delete the '{$role->name}' role. It has users assigned to it.");
        }

        $roleName = $role->name;
        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$roleName}' deleted successfully.");
    }

    /**
     * Bulk delete roles.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'role_ids' => ['required', 'array', 'min:1'],
            'role_ids.*' => ['exists:roles,id'],
        ]);

        $protectedRoles = ['Super Admin', 'Admin', 'Student', 'Parent', 'Teacher'];

        try {
            $roles = Role::whereIn('id', $request->role_ids)->get();
            $deletedCount = 0;
            $errors = [];

            foreach ($roles as $role) {
                if (in_array($role->name, $protectedRoles)) {
                    $errors[] = "'{$role->name}' is a protected role.";
                    continue;
                }

                if ($role->users()->count() > 0) {
                    $errors[] = "'{$role->name}' has users assigned.";
                    continue;
                }

                $role->delete();
                $deletedCount++;
            }

            $message = "{$deletedCount} role(s) deleted.";
            if (!empty($errors)) {
                $message .= " Skipped: " . implode(' ', $errors);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Group permissions by their prefix (e.g., "view students" -> "students").
     */
    private function groupPermissions($permissions)
    {
        $groups = [];

        foreach ($permissions as $permission) {
            $name = $permission->name;

            // Extract the group from permission name
            // Patterns like "view students", "create students", "edit classes"
            // or underscore format like "academic_year_create"
            if (str_contains($name, '_')) {
                // Handle underscore format: academic_year_create -> academic year
                $parts = explode('_', $name);
                if (count($parts) >= 2) {
                    // Remove action part (last element like create, read, update, delete)
                    array_pop($parts);
                    $group = implode(' ', $parts);
                } else {
                    $group = 'General';
                }
            } else {
                // Handle space format: view students -> students
                $parts = explode(' ', $name);
                if (count($parts) >= 2) {
                    // The group is usually the last word(s)
                    array_shift($parts); // Remove action word (view, create, etc.)
                    $group = implode(' ', $parts);
                } else {
                    $group = 'General';
                }
            }

            $group = ucwords($group);

            if (!isset($groups[$group])) {
                $groups[$group] = [];
            }

            $groups[$group][] = $permission;
        }

        // Sort groups alphabetically
        ksort($groups);

        return $groups;
    }
}
