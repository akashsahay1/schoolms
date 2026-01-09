<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;

class PaymentSettingController extends Controller
{
    /**
     * Show payment settings form.
     */
    public function index()
    {
        $setting = PaymentSetting::first();
        $gateways = PaymentSetting::gateways();

        return view('admin.settings.payment', compact('setting', 'gateways'));
    }

    /**
     * Update payment settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'gateway' => 'required|string|in:' . implode(',', array_keys(PaymentSetting::gateways())),
            'key_id' => 'nullable|string|max:255',
            'key_secret' => 'nullable|string|max:255',
            'merchant_id' => 'nullable|string|max:255',
            'salt' => 'nullable|string|max:255',
            'currency' => 'required|string|max:10',
            'webhook_secret' => 'nullable|string|max:255',
        ]);

        $setting = PaymentSetting::first();

        $data = [
            'gateway' => $request->gateway,
            'gateway_name' => PaymentSetting::gateways()[$request->gateway],
            'currency' => $request->currency,
            'is_active' => $request->has('is_active'),
            'is_demo_mode' => $request->has('is_demo_mode'),
        ];

        // Only update sensitive fields if provided (not empty)
        if ($request->filled('key_id')) {
            $data['key_id'] = $request->key_id;
        }
        if ($request->filled('key_secret')) {
            $data['key_secret'] = $request->key_secret;
        }
        if ($request->filled('merchant_id')) {
            $data['merchant_id'] = $request->merchant_id;
        }
        if ($request->filled('salt')) {
            $data['salt'] = $request->salt;
        }
        if ($request->filled('webhook_secret')) {
            $data['webhook_secret'] = $request->webhook_secret;
        }

        if ($setting) {
            $setting->update($data);
        } else {
            PaymentSetting::create($data);
        }

        return redirect()->route('admin.settings.payment')
            ->with('success', 'Payment settings updated successfully.');
    }

    /**
     * Test payment gateway connection.
     */
    public function test(Request $request)
    {
        $setting = PaymentSetting::first();

        if (!$setting || !$setting->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Payment gateway is not active.'
            ]);
        }

        if ($setting->is_demo_mode) {
            return response()->json([
                'success' => true,
                'message' => 'Demo mode is active. Gateway connection test skipped.'
            ]);
        }

        // Test connection based on gateway
        try {
            switch ($setting->gateway) {
                case 'razorpay':
                    return $this->testRazorpay($setting);
                case 'stripe':
                    return $this->testStripe($setting);
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Gateway test not implemented for ' . $setting->gateway_name
                    ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Test Razorpay connection.
     */
    private function testRazorpay(PaymentSetting $setting)
    {
        if (empty($setting->key_id) || empty($setting->key_secret)) {
            return response()->json([
                'success' => false,
                'message' => 'Razorpay API credentials are not configured.'
            ]);
        }

        try {
            $api = new \Razorpay\Api\Api($setting->key_id, $setting->key_secret);
            // Try to fetch orders to test connection
            $api->order->all(['count' => 1]);

            return response()->json([
                'success' => true,
                'message' => 'Razorpay connection successful!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Razorpay connection failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Test Stripe connection.
     */
    private function testStripe(PaymentSetting $setting)
    {
        if (empty($setting->key_secret)) {
            return response()->json([
                'success' => false,
                'message' => 'Stripe API key is not configured.'
            ]);
        }

        try {
            \Stripe\Stripe::setApiKey($setting->key_secret);
            \Stripe\Balance::retrieve();

            return response()->json([
                'success' => true,
                'message' => 'Stripe connection successful!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Stripe connection failed: ' . $e->getMessage()
            ]);
        }
    }
}
