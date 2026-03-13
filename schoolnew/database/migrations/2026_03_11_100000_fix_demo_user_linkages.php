<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Fix demo user records: reset student password, link teacher to staff,
     * link parent to parent_guardians, and link student to students table.
     */
    public function up(): void
    {
        // Reset student password to 'password'
        $studentUser = DB::table('users')->where('email', 'student@school.com')->first();
        if ($studentUser) {
            DB::table('users')->where('id', $studentUser->id)->update([
                'password' => Hash::make('password'),
            ]);
        }

        // Link teacher user to staff record if not already linked
        $teacherUser = DB::table('users')->where('email', 'teacher@school.com')->first();
        if ($teacherUser) {
            $existingStaff = DB::table('staff')->where('user_id', $teacherUser->id)->first();
            if (!$existingStaff) {
                $department = DB::table('departments')->first();
                $designation = DB::table('designations')->where('name', 'Teacher')->first()
                    ?? DB::table('designations')->first();

                if ($department && $designation) {
                    DB::table('staff')->insert([
                        'user_id' => $teacherUser->id,
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
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Link parent user to parent_guardians record if not already linked
        $parentUser = DB::table('users')->where('email', 'parent@school.com')->first();
        $parentRecordId = null;
        if ($parentUser) {
            $existingParent = DB::table('parents')->where('user_id', $parentUser->id)->first();
            if (!$existingParent) {
                // Also check by email
                $existingParent = DB::table('parents')->where('father_email', 'parent@school.com')->first();
            }

            if (!$existingParent) {
                $parentRecordId = DB::table('parents')->insertGetId([
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
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $parentRecordId = $existingParent->id;
            }
        }

        // Link student user to students record if not already linked
        if ($studentUser) {
            $existingStudent = DB::table('students')->where('user_id', $studentUser->id)->first();
            if (!$existingStudent) {
                $academicYear = DB::table('academic_years')->where('is_active', true)->first();
                $class = DB::table('classes')->first();
                $section = $class ? DB::table('sections')->where('class_id', $class->id)->first() : null;

                if ($academicYear && $class && $section) {
                    DB::table('students')->insert([
                        'user_id' => $studentUser->id,
                        'parent_id' => $parentRecordId,
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
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove demo records created by this migration
        $teacherUser = DB::table('users')->where('email', 'teacher@school.com')->first();
        if ($teacherUser) {
            DB::table('staff')->where('user_id', $teacherUser->id)->where('staff_id', 'TCH-001')->delete();
        }

        $studentUser = DB::table('users')->where('email', 'student@school.com')->first();
        if ($studentUser) {
            DB::table('students')->where('user_id', $studentUser->id)->where('admission_no', 'STU-001')->delete();
        }

        $parentUser = DB::table('users')->where('email', 'parent@school.com')->first();
        if ($parentUser) {
            DB::table('parents')->where('user_id', $parentUser->id)->where('father_email', 'parent@school.com')->delete();
        }
    }
};
