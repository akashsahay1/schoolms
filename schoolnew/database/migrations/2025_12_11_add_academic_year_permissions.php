<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create academic year permissions
        $permissions = [
            'academic_year_create',
            'academic_year_read',
            'academic_year_update',
            'academic_year_delete',
        ];

        foreach ($permissions as $permission) {
            if (!Permission::where('name', $permission)->exists()) {
                Permission::create(['name' => $permission]);
            }
        }

        // Assign permissions to roles
        $superAdmin = Role::where('name', 'Super Admin')->first();
        $admin = Role::where('name', 'Admin')->first();

        // Give permissions to Super Admin
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
        }

        // Give permissions to Admin
        if ($admin) {
            $admin->givePermissionTo($permissions);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Permission::whereIn('name', [
            'academic_year_create',
            'academic_year_read',
            'academic_year_update',
            'academic_year_delete',
        ])->delete();
    }
};