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
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50); // e.g., "A", "B", "Science"
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->integer('capacity')->nullable(); // Max students
            $table->foreignId('class_teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('room_no', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['name', 'class_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
