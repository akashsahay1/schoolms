<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class PaymentSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'gateway',
        'gateway_name',
        'key_id',
        'key_secret',
        'merchant_id',
        'salt',
        'currency',
        'is_active',
        'is_demo_mode',
        'webhook_secret',
        'extra_settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_demo_mode' => 'boolean',
        'extra_settings' => 'array',
    ];

    /**
     * Available payment gateways.
     */
    public static function gateways(): array
    {
        return [
            'razorpay' => 'Razorpay',
            'stripe' => 'Stripe',
            'payu' => 'PayU',
            'paytm' => 'Paytm',
            'phonepe' => 'PhonePe',
            'cashfree' => 'Cashfree',
            'instamojo' => 'Instamojo',
        ];
    }

    /**
     * Get the active payment setting.
     */
    public static function getActive()
    {
        return self::where('is_active', true)->first();
    }

    /**
     * Encrypt sensitive data before saving.
     */
    public function setKeySecretAttribute($value)
    {
        $this->attributes['key_secret'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Decrypt sensitive data when retrieving.
     */
    public function getKeySecretAttribute($value)
    {
        try {
            return $value ? Crypt::decryptString($value) : null;
        } catch (\Exception $e) {
            return $value;
        }
    }

    /**
     * Encrypt salt before saving.
     */
    public function setSaltAttribute($value)
    {
        $this->attributes['salt'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Decrypt salt when retrieving.
     */
    public function getSaltAttribute($value)
    {
        try {
            return $value ? Crypt::decryptString($value) : null;
        } catch (\Exception $e) {
            return $value;
        }
    }

    /**
     * Encrypt webhook secret before saving.
     */
    public function setWebhookSecretAttribute($value)
    {
        $this->attributes['webhook_secret'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Decrypt webhook secret when retrieving.
     */
    public function getWebhookSecretAttribute($value)
    {
        try {
            return $value ? Crypt::decryptString($value) : null;
        } catch (\Exception $e) {
            return $value;
        }
    }
}
