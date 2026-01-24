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
        Schema::create('transport_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('transport_route_id')->constrained()->cascadeOnDelete();
            $table->string('fee_type')->default('monthly'); // monthly, quarterly, yearly, one-time
            $table->decimal('amount', 12, 2);
            $table->decimal('fine_per_day', 10, 2)->default(0);
            $table->integer('fine_grace_days')->default(0);
            $table->date('due_date')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['academic_year_id', 'transport_route_id', 'fee_type'], 'transport_fees_unique');
        });

        // Transport fee collections linked to student route assignments
        Schema::create('transport_fee_collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transport_fee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('route_assignment_id')->constrained()->cascadeOnDelete();
            $table->string('month')->nullable(); // For monthly fee tracking (e.g., "2026-01")
            $table->decimal('amount', 12, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('fine', 10, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->date('payment_date')->nullable();
            $table->string('payment_mode')->nullable(); // cash, online, cheque, etc.
            $table->string('receipt_number')->nullable();
            $table->string('transaction_id')->nullable();
            $table->enum('status', ['pending', 'partial', 'paid', 'waived'])->default('pending');
            $table->text('remarks')->nullable();
            $table->foreignId('collected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_fee_collections');
        Schema::dropIfExists('transport_fees');
    }
};
