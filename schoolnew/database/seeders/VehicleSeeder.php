<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = [
            [
                'vehicle_no' => 'BUS-001',
                'vehicle_model' => 'Tata Starbus',
                'year_made' => 2022,
                'registration_no' => 'MH12AB1234',
                'chasis_no' => 'MAT123456789',
                'max_seating_capacity' => 40,
                'driver_name' => 'Ramesh Kumar',
                'driver_license' => 'MH1220210012345',
                'driver_contact' => '9876543210',
                'status' => 'active',
                'note' => 'Main school bus for city route',
            ],
            [
                'vehicle_no' => 'BUS-002',
                'vehicle_model' => 'Ashok Leyland Lynx',
                'year_made' => 2021,
                'registration_no' => 'MH12CD5678',
                'chasis_no' => 'MAT987654321',
                'max_seating_capacity' => 35,
                'driver_name' => 'Suresh Patil',
                'driver_license' => 'MH1220200054321',
                'driver_contact' => '9876543211',
                'status' => 'active',
                'note' => 'Secondary bus for suburb route',
            ],
            [
                'vehicle_no' => 'BUS-003',
                'vehicle_model' => 'Force Traveller',
                'year_made' => 2023,
                'registration_no' => 'MH12EF9012',
                'chasis_no' => 'FOR456789123',
                'max_seating_capacity' => 26,
                'driver_name' => 'Vijay Singh',
                'driver_license' => 'MH1220220067890',
                'driver_contact' => '9876543212',
                'status' => 'active',
                'note' => 'Mini bus for nearby areas',
            ],
            [
                'vehicle_no' => 'VAN-001',
                'vehicle_model' => 'Maruti Eeco',
                'year_made' => 2022,
                'registration_no' => 'MH12GH3456',
                'chasis_no' => 'MAR789123456',
                'max_seating_capacity' => 7,
                'driver_name' => 'Amit Sharma',
                'driver_license' => 'MH1220210098765',
                'driver_contact' => '9876543213',
                'status' => 'active',
                'note' => 'Staff pickup van',
            ],
            [
                'vehicle_no' => 'BUS-004',
                'vehicle_model' => 'Tata Winger',
                'year_made' => 2020,
                'registration_no' => 'MH12IJ7890',
                'chasis_no' => 'TAT321654987',
                'max_seating_capacity' => 15,
                'driver_name' => 'Rajesh Yadav',
                'driver_license' => 'MH1220190011223',
                'driver_contact' => '9876543214',
                'status' => 'maintenance',
                'note' => 'Under maintenance - brake repair',
            ],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::create($vehicle);
        }
    }
}
