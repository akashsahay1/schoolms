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
        Schema::create('homework', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('staff')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('assign_date');
            $table->date('due_date');
            $table->string('attachment')->nullable();
            $table->enum('status', ['active', 'expired'])->default('active');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('homework_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('homework_id')->constrained('homework')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->text('submission_text')->nullable();
            $table->string('attachment')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->enum('status', ['pending', 'submitted', 'late', 'evaluated'])->default('pending');
            $table->decimal('marks_obtained', 5, 2)->nullable();
            $table->decimal('total_marks', 5, 2)->nullable();
            $table->text('teacher_feedback')->nullable();
            $table->timestamp('evaluated_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homework_submissions');
        Schema::dropIfExists('homework');
    }
};
