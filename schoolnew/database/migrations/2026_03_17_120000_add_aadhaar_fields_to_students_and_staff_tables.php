<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('aadhaar_number', 12)->nullable()->after('national_id');
            $table->string('aadhaar_front')->nullable()->after('aadhaar_number');
            $table->string('aadhaar_back')->nullable()->after('aadhaar_front');
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->string('aadhaar_number', 12)->nullable()->after('national_id');
            $table->string('aadhaar_front')->nullable()->after('aadhaar_number');
            $table->string('aadhaar_back')->nullable()->after('aadhaar_front');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['aadhaar_number', 'aadhaar_front', 'aadhaar_back']);
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn(['aadhaar_number', 'aadhaar_front', 'aadhaar_back']);
        });
    }
};
