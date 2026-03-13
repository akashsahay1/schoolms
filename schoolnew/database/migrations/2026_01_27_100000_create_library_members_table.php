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
        Schema::create('library_members', function (Blueprint $table) {
            $table->id();
            $table->string('member_id')->unique();
            $table->morphs('memberable'); // student or staff
            $table->date('membership_start');
            $table->date('membership_end')->nullable();
            $table->enum('status', ['active', 'expired', 'suspended'])->default('active');
            $table->integer('max_books_allowed')->default(3);
            $table->integer('current_books_count')->default(0);
            $table->decimal('total_fines', 10, 2)->default(0);
            $table->decimal('paid_fines', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('library_members');
    }
};
