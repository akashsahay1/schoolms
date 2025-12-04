<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add PhonePe gateway settings for all schools
        $schools = DB::table('sm_schools')->pluck('id');

        foreach ($schools as $schoolId) {
            // Check if PhonePe already exists for this school
            $phonePeExists = DB::table('sm_payment_gateway_settings')
                ->where('gateway_name', 'PhonePe')
                ->where('school_id', $schoolId)
                ->exists();

            if (!$phonePeExists) {
                DB::table('sm_payment_gateway_settings')->insert([
                    'gateway_name' => 'PhonePe',
                    'gateway_username' => '',
                    'gateway_password' => '',
                    'gateway_client_id' => '', // Merchant ID
                    'gateway_secret_key' => '', // Salt Key
                    'gateway_signature' => '1', // Salt Index
                    'gateway_mode' => 'sandbox',
                    'active_status' => 0,
                    'school_id' => $schoolId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Check if PayU already exists for this school
            $payuExists = DB::table('sm_payment_gateway_settings')
                ->where('gateway_name', 'PayU')
                ->where('school_id', $schoolId)
                ->exists();

            if (!$payuExists) {
                DB::table('sm_payment_gateway_settings')->insert([
                    'gateway_name' => 'PayU',
                    'gateway_username' => '',
                    'gateway_password' => '',
                    'gateway_client_id' => '', // Merchant Key
                    'gateway_secret_key' => '', // Merchant Salt
                    'gateway_mode' => 'sandbox',
                    'active_status' => 0,
                    'school_id' => $schoolId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Also insert for default school_id = 1 if not using multi-tenancy
        if ($schools->isEmpty()) {
            DB::table('sm_payment_gateway_settings')->insert([
                [
                    'gateway_name' => 'PhonePe',
                    'gateway_username' => '',
                    'gateway_password' => '',
                    'gateway_client_id' => '', // Merchant ID
                    'gateway_secret_key' => '', // Salt Key
                    'gateway_signature' => '1', // Salt Index
                    'gateway_mode' => 'sandbox',
                    'active_status' => 0,
                    'school_id' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'gateway_name' => 'PayU',
                    'gateway_username' => '',
                    'gateway_password' => '',
                    'gateway_client_id' => '', // Merchant Key
                    'gateway_secret_key' => '', // Merchant Salt
                    'gateway_mode' => 'sandbox',
                    'active_status' => 0,
                    'school_id' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('sm_payment_gateway_settings')
            ->whereIn('gateway_name', ['PhonePe', 'PayU'])
            ->delete();
    }
};
