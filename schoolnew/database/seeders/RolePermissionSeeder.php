<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            // Dashboard
            'view dashboard',

            // Students
            'view students',
            'create students',
            'edit students',
            'delete students',
            'import students',
            'export students',

            // Parents
            'view parents',
            'create parents',
            'edit parents',
            'delete parents',

            // Staff
            'view staff',
            'create staff',
            'edit staff',
            'delete staff',

            // Academic Years
            'academic_year_create',
            'academic_year_read',
            'academic_year_update',
            'academic_year_delete',

            // Classes
            'view classes',
            'create classes',
            'edit classes',
            'delete classes',

            // Sections
            'view sections',
            'create sections',
            'edit sections',
            'delete sections',

            // Subjects
            'view subjects',
            'create subjects',
            'edit subjects',
            'delete subjects',

            // Attendance
            'view attendance',
            'mark attendance',
            'edit attendance',

            // Exams
            'view exams',
            'create exams',
            'edit exams',
            'delete exams',
            'view results',
            'enter marks',

            // Homework
            'view homework',
            'create homework',
            'edit homework',
            'delete homework',

            // Fees
            'view fees',
            'collect fees',
            'manage fee structure',
            'view fee reports',

            // Library
            'view library',
            'manage books',
            'issue books',

            // Transport
            'view transport',
            'manage vehicles',
            'manage routes',

            // Communication
            'view notices',
            'create notices',
            'view events',
            'create events',
            'send messages',

            // Reports
            'view reports',
            'export reports',

            // Settings
            'view settings',
            'manage settings',

            // Users & Roles
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
        ];

        foreach ($permissions as $permission) {
            if (!Permission::where('name', $permission)->exists()) {
                Permission::create(['name' => $permission]);
            }
        }

        // Create Roles and Assign Permissions
        // Super Admin - has all permissions
        $superAdmin = Role::create(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin - has most permissions except role management
        $admin = Role::create(['name' => 'Admin']);
        $admin->givePermissionTo([
            'view dashboard',
            'view students', 'create students', 'edit students', 'delete students', 'import students', 'export students',
            'view parents', 'create parents', 'edit parents', 'delete parents',
            'view staff', 'create staff', 'edit staff', 'delete staff',
            'academic_year_create', 'academic_year_read', 'academic_year_update', 'academic_year_delete',
            'view classes', 'create classes', 'edit classes', 'delete classes',
            'view sections', 'create sections', 'edit sections', 'delete sections',
            'view subjects', 'create subjects', 'edit subjects', 'delete subjects',
            'view attendance', 'mark attendance', 'edit attendance',
            'view exams', 'create exams', 'edit exams', 'delete exams', 'view results', 'enter marks',
            'view homework', 'create homework', 'edit homework', 'delete homework',
            'view fees', 'collect fees', 'manage fee structure', 'view fee reports',
            'view library', 'manage books', 'issue books',
            'view transport', 'manage vehicles', 'manage routes',
            'view notices', 'create notices', 'view events', 'create events', 'send messages',
            'view reports', 'export reports',
            'view settings', 'manage settings',
            'view users', 'create users', 'edit users',
        ]);

        // Teacher
        $teacher = Role::create(['name' => 'Teacher']);
        $teacher->givePermissionTo([
            'view dashboard',
            'view students',
            'view attendance', 'mark attendance',
            'view exams', 'view results', 'enter marks',
            'view homework', 'create homework', 'edit homework',
            'view notices', 'view events',
        ]);

        // Accountant
        $accountant = Role::create(['name' => 'Accountant']);
        $accountant->givePermissionTo([
            'view dashboard',
            'view students',
            'view fees', 'collect fees', 'view fee reports',
            'view reports', 'export reports',
        ]);

        // Librarian
        $librarian = Role::create(['name' => 'Librarian']);
        $librarian->givePermissionTo([
            'view dashboard',
            'view students',
            'view library', 'manage books', 'issue books',
        ]);

        // Receptionist
        $receptionist = Role::create(['name' => 'Receptionist']);
        $receptionist->givePermissionTo([
            'view dashboard',
            'view students',
            'view notices', 'view events',
            'send messages',
        ]);

        // Student
        $student = Role::create(['name' => 'Student']);
        $student->givePermissionTo([
            'view dashboard',
        ]);

        // Parent
        $parent = Role::create(['name' => 'Parent']);
        $parent->givePermissionTo([
            'view dashboard',
        ]);
    }
}
