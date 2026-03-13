<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Staff;
use App\Models\Student;
use App\Models\ParentGuardian;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\Designation;
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

        // Link teacher to a Staff record
        $department = Department::first();
        $designation = Designation::where('name', 'Teacher')->first() ?? Designation::first();
        if ($department && $designation) {
            Staff::create([
                'user_id' => $teacher->id,
                'department_id' => $department->id,
                'designation_id' => $designation->id,
                'staff_id' => 'TCH-001',
                'first_name' => 'Demo',
                'last_name' => 'Teacher',
                'gender' => 'male',
                'date_of_birth' => '1985-06-15',
                'email' => 'teacher@school.com',
                'phone' => '9876543210',
                'joining_date' => '2020-04-01',
                'status' => 'active',
            ]);
        }

        // Create Accountant user
        $accountant = User::create([
            'name' => 'Demo Accountant',
            'email' => 'accountant@school.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $accountant->assignRole('Accountant');

        // Link accountant to a Staff record
        $accDepartment = Department::first();
        $accDesignation = Designation::where('name', 'Accountant')->first() ?? Designation::first();
        if ($accDepartment && $accDesignation) {
            Staff::create([
                'user_id' => $accountant->id,
                'department_id' => $accDepartment->id,
                'designation_id' => $accDesignation->id,
                'staff_id' => 'ACC-001',
                'first_name' => 'Demo',
                'last_name' => 'Accountant',
                'gender' => 'male',
                'date_of_birth' => '1988-03-10',
                'email' => 'accountant@school.com',
                'phone' => '9876543220',
                'joining_date' => '2021-06-01',
                'status' => 'active',
            ]);
        }

        // Create Librarian user
        $librarian = User::create([
            'name' => 'Demo Librarian',
            'email' => 'librarian@school.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $librarian->assignRole('Librarian');

        // Link librarian to a Staff record
        $libDepartment = Department::first();
        $libDesignation = Designation::where('name', 'Librarian')->first() ?? Designation::first();
        if ($libDepartment && $libDesignation) {
            Staff::create([
                'user_id' => $librarian->id,
                'department_id' => $libDepartment->id,
                'designation_id' => $libDesignation->id,
                'staff_id' => 'LIB-001',
                'first_name' => 'Demo',
                'last_name' => 'Librarian',
                'gender' => 'female',
                'date_of_birth' => '1990-07-25',
                'email' => 'librarian@school.com',
                'phone' => '9876543221',
                'joining_date' => '2022-01-15',
                'status' => 'active',
            ]);
        }

        // Create Receptionist user
        $receptionist = User::create([
            'name' => 'Demo Receptionist',
            'email' => 'receptionist@school.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $receptionist->assignRole('Receptionist');

        // Link receptionist to a Staff record
        $recDepartment = Department::first();
        $recDesignation = Designation::where('name', 'Receptionist')->first() ?? Designation::first();
        if ($recDepartment && $recDesignation) {
            Staff::create([
                'user_id' => $receptionist->id,
                'department_id' => $recDepartment->id,
                'designation_id' => $recDesignation->id,
                'staff_id' => 'REC-001',
                'first_name' => 'Demo',
                'last_name' => 'Receptionist',
                'gender' => 'female',
                'date_of_birth' => '1992-11-05',
                'email' => 'receptionist@school.com',
                'phone' => '9876543222',
                'joining_date' => '2023-03-01',
                'status' => 'active',
            ]);
        }

        // Create Parent user (must be created before student so we can link)
        $parentUser = User::create([
            'name' => 'Demo Parent',
            'email' => 'parent@school.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $parentUser->assignRole('Parent');

        // Link parent to a ParentGuardian record
        $parentRecord = ParentGuardian::create([
            'user_id' => $parentUser->id,
            'father_name' => 'Demo Father',
            'father_phone' => '9876543211',
            'father_email' => 'parent@school.com',
            'father_occupation' => 'Business',
            'mother_name' => 'Demo Mother',
            'mother_phone' => '9876543212',
            'guardian_name' => 'Demo Father',
            'guardian_relation' => 'Father',
            'guardian_phone' => '9876543211',
            'guardian_email' => 'parent@school.com',
            'is_active' => true,
        ]);

        // Create Student user
        $studentUser = User::create([
            'name' => 'Demo Student',
            'email' => 'student@school.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $studentUser->assignRole('Student');

        // Link student to a Student record
        $academicYear = AcademicYear::where('is_active', true)->first();
        $class = SchoolClass::first();
        $section = $class ? Section::where('class_id', $class->id)->first() : null;
        if ($academicYear && $class && $section) {
            Student::create([
                'user_id' => $studentUser->id,
                'parent_id' => $parentRecord->id,
                'class_id' => $class->id,
                'section_id' => $section->id,
                'academic_year_id' => $academicYear->id,
                'admission_no' => 'STU-001',
                'roll_no' => '1',
                'first_name' => 'Demo',
                'last_name' => 'Student',
                'gender' => 'male',
                'date_of_birth' => '2010-05-20',
                'email' => 'student@school.com',
                'phone' => '9876543213',
                'admission_date' => '2024-04-01',
                'status' => 'active',
            ]);
        }
    }
}
