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
        Schema::create('sms_settings', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->default('twilio'); // twilio, textlocal, msg91, etc.
            $table->string('api_key')->nullable();
            $table->string('api_secret')->nullable();
            $table->string('sender_id')->nullable();
            $table->string('account_sid')->nullable(); // For Twilio
            $table->string('auth_token')->nullable(); // For Twilio
            $table->string('from_number')->nullable(); // For Twilio
            $table->boolean('is_enabled')->default(false);
            $table->boolean('send_on_admission')->default(false);
            $table->boolean('send_on_fee_collection')->default(false);
            $table->boolean('send_on_attendance')->default(false);
            $table->boolean('send_on_exam_result')->default(false);
            $table->boolean('send_on_leave_approval')->default(false);
            $table->text('admission_template')->nullable();
            $table->text('fee_template')->nullable();
            $table->text('attendance_template')->nullable();
            $table->text('result_template')->nullable();
            $table->text('leave_template')->nullable();
            $table->timestamps();
        });

        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->string('recipient_phone');
            $table->string('recipient_name')->nullable();
            $table->string('recipient_type')->nullable(); // student, parent, staff
            $table->foreignId('recipient_id')->nullable();
            $table->string('message_type')->nullable(); // admission, fee, attendance, etc.
            $table->text('message');
            $table->enum('status', ['pending', 'sent', 'delivered', 'failed'])->default('pending');
            $table->string('message_id')->nullable(); // External message ID from provider
            $table->text('error_message')->nullable();
            $table->decimal('cost', 8, 4)->nullable();
            $table->foreignId('sent_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });

        Schema::create('sms_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category')->nullable(); // academic, financial, attendance, etc.
            $table->text('content');
            $table->text('variables')->nullable(); // JSON: available variables like {student_name}, {class}, etc.
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
        Schema::dropIfExists('sms_templates');
        Schema::dropIfExists('sms_logs');
        Schema::dropIfExists('sms_settings');
    }
};
