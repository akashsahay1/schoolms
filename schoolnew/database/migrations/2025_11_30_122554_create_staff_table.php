<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Departments table
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Designations table
        Schema::create('designations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Staff table
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('designation_id')->nullable()->constrained()->nullOnDelete();

            // Basic Information
            $table->string('staff_id')->unique(); // Employee ID
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->enum('gender', ['male', 'female', 'other']);
            $table->date('date_of_birth');
            $table->string('blood_group', 5)->nullable();
            $table->string('religion', 50)->nullable();
            $table->string('marital_status', 20)->nullable();
            $table->string('nationality', 50)->default('Indian');

            // Contact Information
            $table->string('email')->unique();
            $table->string('phone', 20);
            $table->string('emergency_contact', 20)->nullable();
            $table->text('current_address')->nullable();
            $table->text('permanent_address')->nullable();

            // Identification
            $table->string('national_id', 50)->nullable();
            $table->string('passport_no', 50)->nullable();
            $table->string('driving_license', 50)->nullable();

            // Photo
            $table->string('photo')->nullable();

            // Employment Information
            $table->date('joining_date');
            $table->string('contract_type', 50)->default('permanent'); // permanent, temporary, contractual
            $table->decimal('basic_salary', 12, 2)->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_no')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('pan_number', 20)->nullable();
            $table->string('epf_no', 50)->nullable();

            // Qualifications
            $table->text('qualification')->nullable();
            $table->text('experience')->nullable();
            $table->text('skills')->nullable();

            // Social Links
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('linkedin')->nullable();
            $table->text('bio')->nullable();

            // Status
            $table->enum('status', ['active', 'inactive', 'resigned', 'terminated'])->default('active');
            $table->date('leaving_date')->nullable();
            $table->text('leaving_reason')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('staff_id');
            $table->index('department_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
        Schema::dropIfExists('designations');
        Schema::dropIfExists('departments');
    }
};
