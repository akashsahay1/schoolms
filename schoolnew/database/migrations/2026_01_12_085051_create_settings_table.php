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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, textarea, image, boolean
            $table->string('group')->default('general'); // general, school, contact, etc.
            $table->timestamps();
        });

        // Insert default school settings
        DB::table('settings')->insert([
            ['key' => 'school_name', 'value' => 'School Management System', 'type' => 'text', 'group' => 'school', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'school_address', 'value' => '123 Education Street, Knowledge City, State - 123456', 'type' => 'textarea', 'group' => 'school', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'school_phone', 'value' => '+91 1234567890', 'type' => 'text', 'group' => 'school', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'school_email', 'value' => 'info@school.edu', 'type' => 'text', 'group' => 'school', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'school_logo', 'value' => null, 'type' => 'image', 'group' => 'school', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'school_website', 'value' => 'www.school.edu', 'type' => 'text', 'group' => 'school', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'school_tagline', 'value' => 'Excellence in Education', 'type' => 'text', 'group' => 'school', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
