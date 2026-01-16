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
        Schema::table('fee_collections', function (Blueprint $table) {
            $table->enum('reconciliation_status', ['pending', 'reconciled', 'disputed'])->default('pending')->after('payment_mode');
            $table->foreignId('bank_statement_id')->nullable()->after('reconciliation_status')->constrained()->nullOnDelete();
            $table->foreignId('reconciled_by')->nullable()->after('bank_statement_id')->constrained('users')->nullOnDelete();
            $table->timestamp('reconciled_at')->nullable()->after('reconciled_by');
            $table->text('reconciliation_notes')->nullable()->after('reconciled_at');

            $table->index('reconciliation_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fee_collections', function (Blueprint $table) {
            $table->dropForeign(['bank_statement_id']);
            $table->dropForeign(['reconciled_by']);
            $table->dropIndex(['reconciliation_status']);
            $table->dropColumn([
                'reconciliation_status',
                'bank_statement_id',
                'reconciled_by',
                'reconciled_at',
                'reconciliation_notes'
            ]);
        });
    }
};
