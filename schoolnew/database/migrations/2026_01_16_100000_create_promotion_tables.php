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
        // Promotion criteria/rules table
        Schema::create('promotion_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->decimal('min_attendance_percentage', 5, 2)->default(75);
            $table->decimal('min_marks_percentage', 5, 2)->default(33);
            $table->boolean('consider_attendance')->default(true);
            $table->boolean('consider_marks')->default(true);
            $table->boolean('auto_promote')->default(false);
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['academic_year_id', 'class_id']);
        });

        // Student promotions table
        Schema::create('student_promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('from_academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->foreignId('to_academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->foreignId('from_class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('to_class_id')->nullable()->constrained('classes')->onDelete('cascade');
            $table->foreignId('from_section_id')->nullable()->constrained('sections')->onDelete('set null');
            $table->foreignId('to_section_id')->nullable()->constrained('sections')->onDelete('set null');
            $table->enum('status', ['promoted', 'retained', 'alumni', 'pending', 'cancelled'])->default('pending');
            $table->enum('promotion_type', ['regular', 'conditional', 'special'])->default('regular');
            $table->decimal('final_percentage', 5, 2)->nullable();
            $table->decimal('attendance_percentage', 5, 2)->nullable();
            $table->string('grade')->nullable();
            $table->integer('rank')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('promoted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('promoted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['student_id', 'from_academic_year_id']);
        });

        // Promotion batch/session for bulk operations
        Schema::create('promotion_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->foreignId('to_academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->foreignId('from_class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('from_section_id')->nullable()->constrained('sections')->onDelete('set null');
            $table->integer('total_students')->default(0);
            $table->integer('promoted_count')->default(0);
            $table->integer('retained_count')->default(0);
            $table->integer('alumni_count')->default(0);
            $table->enum('status', ['draft', 'processed', 'finalized', 'rolled_back'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('finalized_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_batches');
        Schema::dropIfExists('student_promotions');
        Schema::dropIfExists('promotion_rules');
    }
};
