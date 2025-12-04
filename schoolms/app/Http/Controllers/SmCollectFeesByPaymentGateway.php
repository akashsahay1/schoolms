<?php

namespace App\Http\Controllers;

use Stripe;
use App\YearCheck;
use App\SmFeesType;
use App\SmFeesPayment;
use App\SmGeneralSettings;
use Illuminate\Http\Request;
use App\SmFeesAssignDiscount;
use App\SmPaymentGatewaySetting;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class SmCollectFeesByPaymentGateway extends Controller
{
    private $provider = null;

    public function __construct()
    {
        // PayPal provider will be initialized when needed
    }

    /**
     * Initialize PayPal provider with credentials from database
     */
    private function initializePayPal()
    {
        if ($this->provider) {
            return true;
        }

        try {
            $paypalDetails = SmPaymentGatewaySetting::where('school_id', Auth::user()->school_id)
                ->select('gateway_client_id', 'gateway_secret_key')
                ->where('gateway_name', '=', 'Paypal')
                ->first();

            if (!$paypalDetails || !$paypalDetails->gateway_secret_key) {
                return false;
            }

            $this->provider = new PayPalClient;

            $config = [
                'mode' => config('paypal.settings.mode', 'sandbox'),
                'sandbox' => [
                    'client_id' => $paypalDetails->gateway_client_id,
                    'client_secret' => $paypalDetails->gateway_secret_key,
                ],
                'live' => [
                    'client_id' => $paypalDetails->gateway_client_id,
                    'client_secret' => $paypalDetails->gateway_secret_key,
                ],
                'payment_action' => 'Sale',
                'currency' => 'USD',
                'notify_url' => '',
                'locale' => 'en_US',
                'validate_ssl' => true,
            ];

            $this->provider->setApiCredentials($config);
            $this->provider->getAccessToken();

            return true;
        } catch (\Exception $e) {
            Log::error('PayPal initialization failed: ' . $e->getMessage());
            return false;
        }
    }

    public function collectFeesByGateway($amount, $student_id, $type)
    {

        try {
            $amount = $amount;
            $fees_type_id = $type;
            $student_id = $student_id;
            $discounts = SmFeesAssignDiscount::where('student_id', $student_id)->get();

            $applied_discount = [];
            foreach ($discounts as $fees_discount) {
                $fees_payment = SmFeesPayment::select('fees_discount_id')->where('fees_discount_id', $fees_discount->id)->first();
                if (isset($fees_payment->fees_discount_id)) {
                    $applied_discount[] = $fees_payment->fees_discount_id;
                }
            }
            return view('backEnd.feesCollection.collectFeesByGateway', compact('amount', 'discounts', 'fees_type_id', 'student_id', 'applied_discount'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function payByPaypal(Request $request)
    {
        try {
            if (!$this->initializePayPal()) {
                Toastr::error('PayPal credentials not configured', 'Failed');
                return redirect()->back();
            }

            $system_currency = 'USD';
            $currency_details = SmGeneralSettings::select('currency')->where('id', 1)->first();
            if (isset($currency_details) && $currency_details->currency) {
                $system_currency = $currency_details->currency;
            }

            // Create PayPal order
            $response = $this->provider->createOrder([
                "intent" => "CAPTURE",
                "application_context" => [
                    "return_url" => URL::to('fees/paypal/status'),
                    "cancel_url" => URL::to('fees/paypal/cancel'),
                    "brand_name" => config('app.name', 'School Management'),
                    "user_action" => "PAY_NOW",
                ],
                "purchase_units" => [
                    [
                        "reference_id" => uniqid(),
                        "description" => "Student Fees Payment",
                        "amount" => [
                            "currency_code" => $system_currency,
                            "value" => number_format((float)$request->real_amount, 2, '.', '')
                        ]
                    ]
                ]
            ]);

            if (isset($response['id']) && $response['status'] === 'CREATED') {
                // Store session data
                Session::put('paypal_order_id', $response['id']);
                Session::put('paypal_student_id', $request->student_id);
                Session::put('paypal_fees_type_id', $request->fees_type_id);
                Session::put('paypal_real_amount', $request->real_amount);

                // Get approval URL
                foreach ($response['links'] as $link) {
                    if ($link['rel'] === 'approve') {
                        return redirect()->away($link['href']);
                    }
                }
            }

            Log::error('PayPal order creation failed: ' . json_encode($response));
            Toastr::error('Failed to create PayPal payment', 'Failed');
            return redirect()->back();

        } catch (\Exception $e) {
            Log::error('PayPal payment error: ' . $e->getMessage());
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function getPaymentStatus(Request $request)
    {
        try {
            if (!$this->initializePayPal()) {
                Toastr::error('PayPal credentials not configured', 'Failed');
                return redirect('student-fees');
            }

            $token = $request->input('token');
            $order_id = Session::get('paypal_order_id');

            if (empty($token)) {
                Toastr::error('Payment failed - missing token', 'Failed');
                return redirect('student-fees');
            }

            // Capture the payment
            $response = $this->provider->capturePaymentOrder($token);

            if (isset($response['status']) && $response['status'] === 'COMPLETED') {
                $user = Auth::user();
                $fees_payment = new SmFeesPayment();
                $fees_payment->student_id = Session::get('paypal_student_id');
                $fees_payment->fees_type_id = Session::get('paypal_fees_type_id');
                $fees_payment->amount = Session::get('paypal_real_amount');
                $fees_payment->payment_date = date('Y-m-d');
                $fees_payment->payment_mode = 'PayPal';
                $fees_payment->created_by = $user->id;
                $fees_payment->school_id = $user->school_id;
                $fees_payment->save();

                // Clear session
                Session::forget('paypal_order_id');
                Session::forget('paypal_student_id');
                Session::forget('paypal_fees_type_id');
                Session::forget('paypal_real_amount');

                Toastr::success('Payment successful', 'Success');
                return redirect('student-fees');
            } else {
                Log::error('PayPal capture failed: ' . json_encode($response));
                Toastr::error('Payment capture failed', 'Failed');
                return redirect('student-fees');
            }

        } catch (\Exception $e) {
            Log::error('PayPal status error: ' . $e->getMessage());
            Toastr::error('Operation Failed', 'Failed');
            return redirect('student-fees');
        }
    }

    public function paypalCancel()
    {
        Session::forget('paypal_order_id');
        Session::forget('paypal_student_id');
        Session::forget('paypal_fees_type_id');
        Session::forget('paypal_real_amount');

        Toastr::warning('Payment Cancelled', 'Cancelled');
        return redirect('student-fees');
    }


    public function collectFeesStripe($amount, $student_id, $type)
    {
        try {
            $amount = $amount;
            $fees_type_id = $type;
            $student_id = $student_id;
            $discounts = SmFeesAssignDiscount::where('student_id', $student_id)->get();
            $stripe_publisher_key = SmPaymentGatewaySetting::where('gateway_name', '=', 'Stripe')->first()->stripe_publisher_key;

            $applied_discount = SmFeesPayment::select('fees_discount_id')->whereIn('fees_discount_id', $discounts->pluck('id')->toArray())->pluck('fees_discount_id')->toArray();

            return view('backEnd.feesCollection.collectFeesStripeView', compact('amount', 'discounts', 'fees_type_id', 'student_id', 'applied_discount', 'stripe_publisher_key'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function stripeStore(Request $request)
    {
        try {
            $system_currency = '';
            $currency_details = SmGeneralSettings::select('currency')->where('id', 1)->first();
            if (isset($currency_details)) {
                $system_currency = $currency_details->currency;
            }
            $stripeDetails = SmPaymentGatewaySetting::select('stripe_api_secret_key', 'stripe_publisher_key')->where('gateway_name', '=', 'Stripe')->first();

            Stripe\Stripe::setApiKey($stripeDetails->stripe_api_secret_key);
            $charge = Stripe\Charge::create([
                "amount" => $request->real_amount * 100,
                "currency" => $system_currency,
                "source" => $request->stripeToken,
                "description" => "Student Fees payment"
            ]);
            if ($charge) {
                $user = Auth::user();
                $fees_payment = new SmFeesPayment();
                $fees_payment->student_id = $request->student_id;
                $fees_payment->fees_type_id = $request->fees_type_id;
                $fees_payment->amount = $request->real_amount;
                $fees_payment->payment_date = date('Y-m-d');
                $fees_payment->payment_mode = 'Stripe';
                $fees_payment->created_by = $user->id;
                $fees_payment->school_id = Auth::user()->school_id;
                $fees_payment->save();

                Toastr::success('Operation successful', 'Success');
                return redirect('student-fees');

            } else {
                Toastr::error('Operation Failed', 'Failed');
                return redirect('student-fees');

            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
}
