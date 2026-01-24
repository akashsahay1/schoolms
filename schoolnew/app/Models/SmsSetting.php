<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsSetting extends Model
{
    protected $fillable = [
        'provider',
        'api_key',
        'api_secret',
        'sender_id',
        'account_sid',
        'auth_token',
        'from_number',
        'is_enabled',
        'send_on_admission',
        'send_on_fee_collection',
        'send_on_attendance',
        'send_on_exam_result',
        'send_on_leave_approval',
        'admission_template',
        'fee_template',
        'attendance_template',
        'result_template',
        'leave_template',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'send_on_admission' => 'boolean',
        'send_on_fee_collection' => 'boolean',
        'send_on_attendance' => 'boolean',
        'send_on_exam_result' => 'boolean',
        'send_on_leave_approval' => 'boolean',
    ];

    protected $hidden = [
        'api_key',
        'api_secret',
        'auth_token',
    ];

    const PROVIDERS = [
        'twilio' => 'Twilio',
        'textlocal' => 'Textlocal',
        'msg91' => 'MSG91',
        'nexmo' => 'Nexmo/Vonage',
        'fast2sms' => 'Fast2SMS',
        'custom' => 'Custom API',
    ];

    /**
     * Get settings instance (singleton).
     */
    public static function getInstance(): self
    {
        $settings = self::first();

        if (!$settings) {
            $settings = self::create([
                'provider' => 'twilio',
                'is_enabled' => false,
            ]);
        }

        return $settings;
    }

    /**
     * Check if SMS is configured and enabled.
     */
    public function isConfigured(): bool
    {
        if (!$this->is_enabled) {
            return false;
        }

        return match ($this->provider) {
            'twilio' => !empty($this->account_sid) && !empty($this->auth_token) && !empty($this->from_number),
            'textlocal' => !empty($this->api_key) && !empty($this->sender_id),
            'msg91' => !empty($this->api_key) && !empty($this->sender_id),
            default => !empty($this->api_key),
        };
    }

    /**
     * Get provider label.
     */
    public function getProviderLabelAttribute(): string
    {
        return self::PROVIDERS[$this->provider] ?? $this->provider;
    }
}
