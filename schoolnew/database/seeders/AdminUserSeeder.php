<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin user
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@school.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $superAdmin->assignRole('Super Admin');

        // Create Admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@school.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('Admin');

        // Create Teacher user
        $teacher = User::create([
            'name' => 'Demo Teacher',
            'email' => 'teacher@school.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $teacher->assignRole('Teacher');

        // Create Accountant user
        $accountant = User::create([
            'name' => 'Demo Accountant',
            'email' => 'accountant@school.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $accountant->assignRole('Accountant');

        // Create Student user
        $student = User::create([
            'name' => 'Demo Student',
            'email' => 'student@school.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $student->assignRole('Student');

        // Create Parent user
        $parent = User::create([
            'name' => 'Demo Parent',
            'email' => 'parent@school.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $parent->assignRole('Parent');
    }
}
