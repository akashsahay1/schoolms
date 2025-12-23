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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_no')->unique();
            $table->string('vehicle_model');
            $table->string('registration_no')->unique();
            $table->year('year_made')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('driver_contact')->nullable();
            $table->string('driver_license')->nullable();
            $table->integer('max_seating_capacity');
            $table->enum('status', ['active', 'maintenance', 'inactive'])->default('active');
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('transport_routes', function (Blueprint $table) {
            $table->id();
            $table->string('route_name');
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->onDelete('set null');
            $table->string('start_place');
            $table->string('end_place');
            $table->text('stops')->nullable();
            $table->decimal('fare_amount', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('route_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('route_id')->constrained('transport_routes')->onDelete('cascade');
            $table->string('pickup_point')->nullable();
            $table->decimal('monthly_fee', 10, 2)->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('route_assignments');
        Schema::dropIfExists('transport_routes');
        Schema::dropIfExists('vehicles');
    }
};
