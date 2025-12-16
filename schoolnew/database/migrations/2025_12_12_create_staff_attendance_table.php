<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('staff_attendance')) {
            Schema::create('staff_attendance', function (Blueprint $table) {
                $table->id();
                $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
                $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
                $table->date('attendance_date');
                $table->enum('status', ['present', 'absent', 'late', 'half_day', 'on_leave'])->default('present');
                $table->time('check_in_time')->nullable();
                $table->time('check_out_time')->nullable();
                $table->text('remarks')->nullable();
                $table->foreignId('marked_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->unique(['staff_id', 'attendance_date'], 'staff_att_unique');
                $table->index(['attendance_date', 'status'], 'staff_att_date_status_idx');
            });
        }

        if (!Schema::hasTable('staff_attendance_summaries')) {
            Schema::create('staff_attendance_summaries', function (Blueprint $table) {
                $table->id();
                $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
                $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
                $table->tinyInteger('month');
                $table->smallInteger('year');
                $table->integer('total_days')->default(0);
                $table->integer('present_days')->default(0);
                $table->integer('absent_days')->default(0);
                $table->integer('late_days')->default(0);
                $table->integer('half_days')->default(0);
                $table->integer('leave_days')->default(0);
                $table->decimal('attendance_percentage', 5, 2)->default(0);
                $table->timestamps();

                $table->unique(['staff_id', 'month', 'year', 'academic_year_id'], 'staff_att_summary_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_attendance_summaries');
        Schema::dropIfExists('staff_attendance');
    }
};
