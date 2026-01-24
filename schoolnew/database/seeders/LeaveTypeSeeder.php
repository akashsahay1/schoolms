<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leaveTypes = [
            [
                'name' => 'Casual Leave',
                'code' => 'CL',
                'description' => 'Leave for personal matters and short absences',
                'allowed_days' => 12,
                'is_paid' => true,
                'requires_attachment' => false,
                'applicable_to' => 'all',
                'is_active' => true,
            ],
            [
                'name' => 'Sick Leave',
                'code' => 'SL',
                'description' => 'Leave for medical reasons and illness',
                'allowed_days' => 15,
                'is_paid' => true,
                'requires_attachment' => true,
                'applicable_to' => 'all',
                'is_active' => true,
            ],
            [
                'name' => 'Earned Leave',
                'code' => 'EL',
                'description' => 'Leave earned based on service, can be accumulated',
                'allowed_days' => 20,
                'is_paid' => true,
                'requires_attachment' => false,
                'applicable_to' => 'staff',
                'is_active' => true,
            ],
            [
                'name' => 'Maternity Leave',
                'code' => 'ML',
                'description' => 'Leave for expecting mothers',
                'allowed_days' => 180,
                'is_paid' => true,
                'requires_attachment' => true,
                'applicable_to' => 'staff',
                'is_active' => true,
            ],
            [
                'name' => 'Paternity Leave',
                'code' => 'PL',
                'description' => 'Leave for new fathers',
                'allowed_days' => 15,
                'is_paid' => true,
                'requires_attachment' => true,
                'applicable_to' => 'staff',
                'is_active' => true,
            ],
            [
                'name' => 'Leave Without Pay',
                'code' => 'LWP',
                'description' => 'Unpaid leave when other leave types are exhausted',
                'allowed_days' => 30,
                'is_paid' => false,
                'requires_attachment' => false,
                'applicable_to' => 'all',
                'is_active' => true,
            ],
        ];

        foreach ($leaveTypes as $type) {
            LeaveType::updateOrCreate(
                ['code' => $type['code']],
                $type
            );
        }
    }
}
