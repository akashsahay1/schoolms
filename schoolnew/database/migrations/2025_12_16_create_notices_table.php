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
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('type')->default('general'); // general, urgent, academic, exam, holiday, event
            $table->date('publish_date');
            $table->date('expiry_date')->nullable();
            $table->json('target_audience')->nullable(); // ['all', 'students', 'parents', 'teachers', 'staff']
            $table->json('target_classes')->nullable(); // specific class IDs, null for all
            $table->string('attachment')->nullable();
            $table->boolean('is_published')->default(true);
            $table->boolean('send_email')->default(false);
            $table->boolean('send_sms')->default(false);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('academic_year_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['publish_date', 'expiry_date']);
            $table->index('type');
            $table->index('is_published');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notices');
    }
};
