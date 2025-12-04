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
        Schema::create('parents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Father's Information
            $table->string('father_name');
            $table->string('father_phone', 20)->nullable();
            $table->string('father_email')->nullable();
            $table->string('father_occupation')->nullable();
            $table->string('father_photo')->nullable();
            $table->string('father_national_id', 50)->nullable();

            // Mother's Information
            $table->string('mother_name')->nullable();
            $table->string('mother_phone', 20)->nullable();
            $table->string('mother_email')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->string('mother_photo')->nullable();
            $table->string('mother_national_id', 50)->nullable();

            // Guardian Information (if different from parents)
            $table->string('guardian_name')->nullable();
            $table->string('guardian_relation')->nullable();
            $table->string('guardian_phone', 20)->nullable();
            $table->string('guardian_email')->nullable();
            $table->string('guardian_occupation')->nullable();
            $table->string('guardian_photo')->nullable();
            $table->text('guardian_address')->nullable();

            // Address Information
            $table->text('current_address')->nullable();
            $table->text('permanent_address')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parents');
    }
};
