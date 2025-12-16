<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FeeType;
use App\Models\FeeGroup;
use App\Models\FeeDiscount;

class FeeStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Fee Types
        $feeTypes = [
            ['name' => 'Tuition Fee', 'code' => 'TF', 'description' => 'Monthly tuition fee'],
            ['name' => 'Admission Fee', 'code' => 'AF', 'description' => 'One time admission fee'],
            ['name' => 'Annual Charges', 'code' => 'AC', 'description' => 'Yearly maintenance charges'],
            ['name' => 'Library Fee', 'code' => 'LF', 'description' => 'Library usage fee'],
            ['name' => 'Computer Lab Fee', 'code' => 'CLF', 'description' => 'Computer lab usage fee'],
            ['name' => 'Sports Fee', 'code' => 'SF', 'description' => 'Sports and physical education fee'],
            ['name' => 'Transport Fee', 'code' => 'TRF', 'description' => 'School bus transportation fee'],
            ['name' => 'Exam Fee', 'code' => 'EF', 'description' => 'Examination fee'],
            ['name' => 'Uniform Fee', 'code' => 'UF', 'description' => 'School uniform fee'],
            ['name' => 'Book Fee', 'code' => 'BF', 'description' => 'Textbook and notebook fee'],
        ];

        foreach ($feeTypes as $feeType) {
            FeeType::create($feeType);
        }

        // Create Fee Groups
        $feeGroups = [
            ['name' => 'Monthly', 'description' => 'Fees collected on monthly basis'],
            ['name' => 'Quarterly', 'description' => 'Fees collected every 3 months'],
            ['name' => 'Half Yearly', 'description' => 'Fees collected every 6 months'],
            ['name' => 'Yearly', 'description' => 'Fees collected annually'],
            ['name' => 'One Time', 'description' => 'One time payment'],
        ];

        foreach ($feeGroups as $feeGroup) {
            FeeGroup::create($feeGroup);
        }

        // Create Fee Discounts
        $feeDiscounts = [
            ['name' => 'Staff Child Discount', 'code' => 'SCD', 'type' => 'percentage', 'amount' => 50, 'description' => '50% discount for staff children'],
            ['name' => 'Sibling Discount', 'code' => 'SBD', 'type' => 'percentage', 'amount' => 25, 'description' => '25% discount for siblings'],
            ['name' => 'Merit Scholarship', 'code' => 'MS', 'type' => 'percentage', 'amount' => 100, 'description' => '100% scholarship for merit students'],
            ['name' => 'Financial Aid', 'code' => 'FA', 'type' => 'fixed', 'amount' => 5000, 'description' => 'Fixed financial aid'],
            ['name' => 'Early Payment Discount', 'code' => 'EPD', 'type' => 'percentage', 'amount' => 5, 'description' => '5% discount for early payment'],
        ];

        foreach ($feeDiscounts as $feeDiscount) {
            FeeDiscount::create($feeDiscount);
        }

        $this->command->info('Fee structure seed data created successfully!');
    }
}