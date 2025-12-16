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
        // Fee Types (Tuition, Transport, Library, etc.)
        Schema::create('fee_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Fee Groups (Monthly, Quarterly, Yearly)
        Schema::create('fee_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Fee Structure Master
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained();
            $table->foreignId('class_id')->constrained('classes');
            $table->foreignId('fee_type_id')->constrained();
            $table->foreignId('fee_group_id')->constrained();
            $table->decimal('amount', 10, 2);
            $table->date('due_date')->nullable();
            $table->decimal('fine_amount', 8, 2)->default(0);
            $table->enum('fine_type', ['none', 'percentage', 'fixed'])->default('none');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Unique constraint: One fee type per class per academic year
            $table->unique(['academic_year_id', 'class_id', 'fee_type_id']);
        });

        // Fee Discounts
        Schema::create('fee_discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->enum('type', ['percentage', 'fixed']);
            $table->decimal('amount', 8, 2);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Fee Collections
        Schema::create('fee_collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained();
            $table->foreignId('fee_structure_id')->constrained();
            $table->foreignId('academic_year_id')->constrained();
            $table->foreignId('collected_by')->constrained('users');
            $table->decimal('amount', 10, 2);
            $table->decimal('discount_amount', 8, 2)->default(0);
            $table->decimal('fine_amount', 8, 2)->default(0);
            $table->decimal('paid_amount', 10, 2);
            $table->enum('payment_mode', ['cash', 'cheque', 'dd', 'online', 'bank_transfer']);
            $table->string('transaction_id')->nullable();
            $table->date('payment_date');
            $table->text('remarks')->nullable();
            $table->string('receipt_no')->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_collections');
        Schema::dropIfExists('fee_discounts');
        Schema::dropIfExists('fee_structures');
        Schema::dropIfExists('fee_groups');
        Schema::dropIfExists('fee_types');
    }
};