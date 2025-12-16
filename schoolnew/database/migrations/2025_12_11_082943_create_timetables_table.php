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
        Schema::create('timetable_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Period 1", "Lunch Break"
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('type', ['class', 'break'])->default('class');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('timetables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained();
            $table->foreignId('class_id')->constrained('classes');
            $table->foreignId('section_id')->constrained();
            $table->foreignId('subject_id')->constrained();
            $table->foreignId('teacher_id')->nullable()->constrained('staff');
            $table->foreignId('period_id')->constrained('timetable_periods');
            $table->enum('day', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);
            $table->string('room_number')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Ensure no double booking for same class/section at same time
            $table->unique(['academic_year_id', 'class_id', 'section_id', 'day', 'period_id'], 'unique_class_timetable');
            // Ensure teacher is not double booked
            $table->index(['academic_year_id', 'teacher_id', 'day', 'period_id'], 'idx_teacher_timetable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetables');
        Schema::dropIfExists('timetable_periods');
    }
};
