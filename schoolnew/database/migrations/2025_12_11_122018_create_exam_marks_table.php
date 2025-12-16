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
        if (!Schema::hasTable('exam_marks')) {
            Schema::create('exam_marks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('exam_id')->constrained()->onDelete('cascade');
                $table->foreignId('student_id')->constrained()->onDelete('cascade');
                $table->foreignId('subject_id')->constrained()->onDelete('cascade');
                $table->decimal('marks_obtained', 5, 2);
                $table->decimal('full_marks', 5, 2);
                $table->string('grade', 2)->nullable();
                $table->text('remarks')->nullable();
                $table->timestamps();

                $table->unique(['exam_id', 'student_id', 'subject_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_marks');
    }
};
