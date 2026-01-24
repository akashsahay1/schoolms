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
        // Messages table for parent-teacher communication
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->string('sender_type')->default('user'); // user, teacher, parent, admin
            $table->foreignId('recipient_id')->constrained('users')->cascadeOnDelete();
            $table->string('recipient_type')->default('user');
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->string('subject');
            $table->text('message');
            $table->string('attachment')->nullable();
            $table->foreignId('parent_message_id')->nullable()->constrained('messages')->nullOnDelete();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['sender_id', 'recipient_id']);
            $table->index(['recipient_id', 'is_read']);
        });

        // Bulk messages/campaigns table
        Schema::create('bulk_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('message');
            $table->string('message_type'); // sms, email, notification, all
            $table->string('recipient_type'); // all_students, all_parents, all_teachers, class_wise, custom
            $table->json('recipient_filters')->nullable(); // Class IDs, Section IDs, etc.
            $table->integer('total_recipients')->default(0);
            $table->integer('sent_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->enum('status', ['draft', 'scheduled', 'sending', 'completed', 'failed'])->default('draft');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        // Bulk message recipients log
        Schema::create('bulk_message_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bulk_message_id')->constrained('bulk_messages')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('recipient_name')->nullable();
            $table->string('recipient_phone')->nullable();
            $table->string('recipient_email')->nullable();
            $table->enum('channel', ['sms', 'email', 'notification']);
            $table->enum('status', ['pending', 'sent', 'delivered', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['bulk_message_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulk_message_logs');
        Schema::dropIfExists('bulk_messages');
        Schema::dropIfExists('messages');
    }
};
