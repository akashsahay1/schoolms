<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Department;
use App\Models\Designation;

class AcademicDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Academic Year
        $academicYear = AcademicYear::create([
            'name' => '2024-2025',
            'start_date' => '2024-04-01',
            'end_date' => '2025-03-31',
            'is_active' => true,
            'description' => 'Academic Year 2024-2025',
        ]);

        // Create Classes
        $classes = [
            ['name' => 'Nursery', 'numeric_name' => 'N', 'order' => 1],
            ['name' => 'LKG', 'numeric_name' => 'LKG', 'order' => 2],
            ['name' => 'UKG', 'numeric_name' => 'UKG', 'order' => 3],
            ['name' => 'Class 1', 'numeric_name' => '1', 'order' => 4],
            ['name' => 'Class 2', 'numeric_name' => '2', 'order' => 5],
            ['name' => 'Class 3', 'numeric_name' => '3', 'order' => 6],
            ['name' => 'Class 4', 'numeric_name' => '4', 'order' => 7],
            ['name' => 'Class 5', 'numeric_name' => '5', 'order' => 8],
            ['name' => 'Class 6', 'numeric_name' => '6', 'order' => 9],
            ['name' => 'Class 7', 'numeric_name' => '7', 'order' => 10],
            ['name' => 'Class 8', 'numeric_name' => '8', 'order' => 11],
            ['name' => 'Class 9', 'numeric_name' => '9', 'order' => 12],
            ['name' => 'Class 10', 'numeric_name' => '10', 'order' => 13],
            ['name' => 'Class 11 Science', 'numeric_name' => '11S', 'order' => 14],
            ['name' => 'Class 11 Commerce', 'numeric_name' => '11C', 'order' => 15],
            ['name' => 'Class 12 Science', 'numeric_name' => '12S', 'order' => 16],
            ['name' => 'Class 12 Commerce', 'numeric_name' => '12C', 'order' => 17],
        ];

        $sections = ['A', 'B', 'C'];

        foreach ($classes as $classData) {
            $class = SchoolClass::create([
                'name' => $classData['name'],
                'numeric_name' => $classData['numeric_name'],
                'academic_year_id' => $academicYear->id,
                'order' => $classData['order'],
                'is_active' => true,
            ]);

            // Create sections for each class
            foreach ($sections as $sectionName) {
                Section::create([
                    'name' => $sectionName,
                    'class_id' => $class->id,
                    'capacity' => 40,
                    'is_active' => true,
                ]);
            }
        }

        // Create Subjects
        $subjects = [
            ['name' => 'English', 'code' => 'ENG', 'type' => 'theory'],
            ['name' => 'Hindi', 'code' => 'HIN', 'type' => 'theory'],
            ['name' => 'Mathematics', 'code' => 'MAT', 'type' => 'theory'],
            ['name' => 'Science', 'code' => 'SCI', 'type' => 'both'],
            ['name' => 'Social Science', 'code' => 'SSC', 'type' => 'theory'],
            ['name' => 'Computer Science', 'code' => 'CS', 'type' => 'both'],
            ['name' => 'Physical Education', 'code' => 'PE', 'type' => 'practical'],
            ['name' => 'Art & Craft', 'code' => 'ART', 'type' => 'practical'],
            ['name' => 'Music', 'code' => 'MUS', 'type' => 'practical', 'is_optional' => true],
            ['name' => 'Physics', 'code' => 'PHY', 'type' => 'both'],
            ['name' => 'Chemistry', 'code' => 'CHE', 'type' => 'both'],
            ['name' => 'Biology', 'code' => 'BIO', 'type' => 'both'],
            ['name' => 'Accountancy', 'code' => 'ACC', 'type' => 'theory'],
            ['name' => 'Business Studies', 'code' => 'BUS', 'type' => 'theory'],
            ['name' => 'Economics', 'code' => 'ECO', 'type' => 'theory'],
        ];

        foreach ($subjects as $subjectData) {
            Subject::create([
                'name' => $subjectData['name'],
                'code' => $subjectData['code'],
                'type' => $subjectData['type'],
                'is_optional' => $subjectData['is_optional'] ?? false,
                'is_active' => true,
            ]);
        }

        // Create Departments
        $departments = [
            ['name' => 'Administration', 'description' => 'School administration department'],
            ['name' => 'Academics', 'description' => 'Academic affairs department'],
            ['name' => 'Finance', 'description' => 'Finance and accounts department'],
            ['name' => 'Library', 'description' => 'Library management'],
            ['name' => 'Sports', 'description' => 'Sports and physical education'],
            ['name' => 'IT', 'description' => 'Information technology department'],
            ['name' => 'Maintenance', 'description' => 'Facility maintenance'],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }

        // Create Designations
        $designations = [
            ['name' => 'Principal', 'description' => 'School Principal'],
            ['name' => 'Vice Principal', 'description' => 'Vice Principal'],
            ['name' => 'Head Teacher', 'description' => 'Head of Teachers'],
            ['name' => 'Senior Teacher', 'description' => 'Senior Teaching Staff'],
            ['name' => 'Teacher', 'description' => 'Teaching Staff'],
            ['name' => 'Assistant Teacher', 'description' => 'Assistant Teaching Staff'],
            ['name' => 'Lab Assistant', 'description' => 'Laboratory Assistant'],
            ['name' => 'Librarian', 'description' => 'Library In-charge'],
            ['name' => 'Accountant', 'description' => 'Accounts Staff'],
            ['name' => 'Clerk', 'description' => 'Office Clerk'],
            ['name' => 'Receptionist', 'description' => 'Front Desk'],
            ['name' => 'Security', 'description' => 'Security Staff'],
            ['name' => 'Peon', 'description' => 'Support Staff'],
        ];

        foreach ($designations as $desig) {
            Designation::create($desig);
        }
    }
}
