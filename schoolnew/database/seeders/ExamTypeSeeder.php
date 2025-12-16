<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ExamType;

class ExamTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $examTypes = [
            [
                'name' => 'First Terminal Exam',
                'code' => 'FIRST_TERM',
                'description' => 'First terminal examination',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Second Terminal Exam',
                'code' => 'SECOND_TERM',
                'description' => 'Second terminal examination',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Final Exam',
                'code' => 'FINAL',
                'description' => 'Final annual examination',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Monthly Test',
                'code' => 'MONTHLY',
                'description' => 'Monthly assessment test',
                'order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($examTypes as $examType) {
            ExamType::updateOrCreate(
                ['code' => $examType['code']],
                $examType
            );
        }
    }
}
