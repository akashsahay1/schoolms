<?php

namespace Database\Seeders;

use App\Models\FeeDiscount;
use Illuminate\Database\Seeder;

class FeeDiscountSeeder extends Seeder
{
    public function run(): void
    {
        $discounts = [
            [
                'name' => 'Sibling Discount',
                'code' => 'SIBLING',
                'type' => 'percentage',
                'amount' => 10.00,
                'description' => '10% discount for siblings studying in the same school',
                'is_active' => true,
            ],
            [
                'name' => 'Staff Ward Discount',
                'code' => 'STAFFWARD',
                'type' => 'percentage',
                'amount' => 50.00,
                'description' => '50% discount for children of school staff members',
                'is_active' => true,
            ],
            [
                'name' => 'Merit Scholarship',
                'code' => 'MERIT',
                'type' => 'percentage',
                'amount' => 25.00,
                'description' => '25% scholarship for students with outstanding academic performance',
                'is_active' => true,
            ],
            [
                'name' => 'Sports Scholarship',
                'code' => 'SPORTS',
                'type' => 'percentage',
                'amount' => 20.00,
                'description' => '20% scholarship for students with sports achievements',
                'is_active' => true,
            ],
            [
                'name' => 'Early Bird Discount',
                'code' => 'EARLYBIRD',
                'type' => 'fixed',
                'amount' => 500.00,
                'description' => 'Flat ₹500 discount for early fee payment before due date',
                'is_active' => true,
            ],
            [
                'name' => 'Financial Aid',
                'code' => 'FINAID',
                'type' => 'percentage',
                'amount' => 100.00,
                'description' => 'Full fee waiver for students with financial hardship',
                'is_active' => true,
            ],
            [
                'name' => 'Single Parent Discount',
                'code' => 'SINGPARENT',
                'type' => 'percentage',
                'amount' => 15.00,
                'description' => '15% discount for children of single parents',
                'is_active' => true,
            ],
            [
                'name' => 'Annual Payment Discount',
                'code' => 'ANNUAL',
                'type' => 'percentage',
                'amount' => 5.00,
                'description' => '5% discount for paying full year fees at once',
                'is_active' => true,
            ],
        ];

        foreach ($discounts as $discount) {
            FeeDiscount::firstOrCreate(
                ['code' => $discount['code']],
                $discount
            );
        }
    }
}
