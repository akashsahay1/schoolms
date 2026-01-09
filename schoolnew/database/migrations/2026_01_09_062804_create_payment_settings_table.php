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
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->string('gateway')->default('razorpay'); // razorpay, stripe, payu, paytm, phonepe, cashfree
            $table->string('gateway_name')->default('Razorpay');
            $table->text('key_id')->nullable();
            $table->text('key_secret')->nullable();
            $table->text('merchant_id')->nullable(); // For gateways like PayU, Paytm
            $table->text('salt')->nullable(); // For gateways like PayU
            $table->string('currency')->default('INR');
            $table->boolean('is_active')->default(false);
            $table->boolean('is_demo_mode')->default(true);
            $table->text('webhook_secret')->nullable();
            $table->json('extra_settings')->nullable(); // For any additional gateway-specific settings
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
    }
};
