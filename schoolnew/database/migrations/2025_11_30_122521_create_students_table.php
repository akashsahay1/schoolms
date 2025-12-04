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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->foreignId('section_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');

            // Basic Information
            $table->string('admission_no')->unique();
            $table->string('roll_no')->nullable();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->enum('gender', ['male', 'female', 'other']);
            $table->date('date_of_birth');
            $table->string('blood_group', 5)->nullable();
            $table->string('religion', 50)->nullable();
            $table->string('caste', 50)->nullable();
            $table->string('nationality', 50)->default('Indian');
            $table->string('mother_tongue', 50)->nullable();

            // Contact Information
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('current_address')->nullable();
            $table->text('permanent_address')->nullable();

            // Identification
            $table->string('national_id', 50)->nullable();
            $table->string('passport_no', 50)->nullable();

            // Photo & Documents
            $table->string('photo')->nullable();
            $table->string('birth_certificate')->nullable();
            $table->string('transfer_certificate')->nullable();

            // Academic Information
            $table->date('admission_date');
            $table->string('previous_school')->nullable();
            $table->string('previous_class')->nullable();

            // Health Information
            $table->decimal('height', 5, 2)->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->text('medical_conditions')->nullable();
            $table->text('allergies')->nullable();

            // Transport & Hostel
            $table->boolean('uses_transport')->default(false);
            $table->foreignId('route_id')->nullable();
            $table->boolean('is_boarder')->default(false);
            $table->foreignId('room_id')->nullable();

            // Fees & Discount
            $table->foreignId('fee_category_id')->nullable();
            $table->decimal('discount_percent', 5, 2)->default(0);

            // Status
            $table->enum('status', ['active', 'inactive', 'graduated', 'transferred', 'expelled'])->default('active');
            $table->date('leaving_date')->nullable();
            $table->text('leaving_reason')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['class_id', 'section_id', 'academic_year_id']);
            $table->index('admission_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
