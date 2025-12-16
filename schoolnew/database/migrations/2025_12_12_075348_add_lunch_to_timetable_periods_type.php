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
        // Modify enum to include 'lunch'
        DB::statement("ALTER TABLE timetable_periods MODIFY COLUMN type ENUM('class', 'break', 'lunch') DEFAULT 'class'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum (this may fail if 'lunch' records exist)
        DB::statement("ALTER TABLE timetable_periods MODIFY COLUMN type ENUM('class', 'break') DEFAULT 'class'");
    }
};
