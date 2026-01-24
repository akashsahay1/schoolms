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
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->integer('allowed_days')->default(0); // Per year
            $table->boolean('is_paid')->default(true);
            $table->boolean('requires_attachment')->default(false);
            $table->boolean('is_active')->default(true);
            $table->enum('applicable_to', ['all', 'staff', 'students'])->default('all');
            $table->timestamps();
            $table->softDeletes();
        });

        // Staff leave balances (per academic year)
        Schema::create('staff_leave_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained()->onDelete('cascade');
            $table->foreignId('leave_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->integer('allocated_days')->default(0);
            $table->integer('used_days')->default(0);
            $table->integer('carried_forward')->default(0);
            $table->timestamps();

            $table->unique(['staff_id', 'leave_type_id', 'academic_year_id'], 'unique_staff_leave_balance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_leave_balances');
        Schema::dropIfExists('leave_types');
    }
};
