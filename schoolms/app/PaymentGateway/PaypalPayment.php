<?php
namespace App\PaymentGateway;

use App\User;
use Exception;
use App\SmSchool;
use App\SmFeesType;
use App\SmAddIncome;
use App\SmFeesPayment;
use App\SmPaymentMethhod;
use Illuminate\Http\Request;
use App\SmPaymentGatewaySetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Modules\Fees\Entities\FmFeesTransaction;
use Illuminate\Validation\ValidationException;
use Modules\Fees\Entities\FmFeesInvoiceChield;
use Modules\Wallet\Entities\WalletTransaction;
use Modules\Fees\Http\Controllers\FeesController;
use Modules\Fees\Entities\FmFeesTransactionChield;

class PaypalPayment
{
    private $provider;
    private $client_id;
    private $secret;
    private $mode;

    public function __construct()
    {
        $paypalDetails = SmPaymentGatewaySetting::where('school_id', auth()->user()->school_id)
            ->select('gateway_username', 'gateway_password', 'gateway_signature', 'gateway_client_id', 'gateway_secret_key')
            ->where('gateway_name', '=', 'Paypal')
            ->first();

        if (!$paypalDetails || !$paypalDetails->gateway_secret_key) {
            Toastr::warning('Paypal Credentials Can Not Be Blank', 'Warning');
            return redirect()->send()->back();
        }

        $this->client_id = $paypalDetails->gateway_client_id;
        $this->secret = $paypalDetails->gateway_secret_key;
        $this->mode = config('paypal.settings.mode', 'sandbox');

        // Initialize PayPal provider with dynamic credentials
        $this->provider = new PayPalClient;

        // Set credentials dynamically from database
        $config = [
            'mode' => $this->mode,
            'sandbox' => [
                'client_id' => $this->client_id,
                'client_secret' => $this->secret,
            ],
            'live' => [
                'client_id' => $this->client_id,
                'client_secret' => $this->secret,
            ],
            'payment_action' => 'Sale',
            'currency' => 'USD',
            'notify_url' => '',
            'locale' => 'en_US',
            'validate_ssl' => true,
        ];

        $this->provider->setApiCredentials($config);
        $this->provider->getAccessToken();
    }

    public function handle($data)
    {
        try {
            // Create PayPal order using Orders API v2
            $response = $this->provider->createOrder([
                "intent" => "CAPTURE",
                "application_context" => [
                    "return_url" => URL::to('payment_gateway_success_callback/PayPal'),
                    "cancel_url" => URL::to('payment_gateway_cancel_callback/PayPal'),
                    "brand_name" => config('app.name', 'School Management'),
                    "user_action" => "PAY_NOW",
                ],
                "purchase_units" => [
                    [
                        "reference_id" => uniqid(),
                        "description" => "Fees Collection",
                        "amount" => [
                            "currency_code" => "USD",
                            "value" => number_format((float)$data['amount'], 2, '.', '')
                        ]
                    ]
                ]
            ]);

            if (isset($response['id']) && $response['status'] === 'CREATED') {
                // Store session data based on payment type
                if ($data['type'] == "Wallet") {
                    $addPayment = new WalletTransaction();
                    $addPayment->amount = $data['amount'];
                    $addPayment->payment_method = $data['payment_method'];
                    $addPayment->user_id = $data['user_id'];
                    $addPayment->type = $data['wallet_type'];
                    $addPayment->school_id = Auth::user()->school_id;
                    $addPayment->academic_id = getAcademicId();
                    $addPayment->save();

                    Session::put('paypal_order_id', $response['id']);
                    Session::put('payment_type', $data['type']);
                    Session::put('wallet_payment_id', $addPayment->id);
                } else {
                    Session::forget('amount');
                    Session::put('payment_type', $data['type']);
                    Session::put('invoice_id', $data['invoice_id']);
                    Session::put('amount', $data['amount']);
                    Session::put('payment_method', $data['payment_method']);
                    Session::put('transcation_id', $data['transcationId']);

                    Session::put('paypal_order_id', $response['id']);
                    Session::put('fees_payment_id', $data['transcationId']);
                }

                // Get approval URL from links
                foreach ($response['links'] as $link) {
                    if ($link['rel'] === 'approve') {
                        return $link['href'];
                    }
                }
            }

            throw new Exception('Failed to create PayPal order: ' . json_encode($response));

        } catch (Exception $e) {
            Log::error('PayPal Error: ' . $e->getMessage());
            throw ValidationException::withMessages(['amount' => $e->getMessage()]);
        }
    }

    public function successCallback()
    {
        $request = App::make(Request::class);

        try {
            $order_id = Session::get('paypal_order_id');
            $token = $request->input('token');

            if (empty($token) || empty($order_id)) {
                Session::put('error', 'Payment failed - missing token');
                Toastr::error('Payment failed', 'Failed');
                return redirect()->back();
            }

            // Reinitialize provider for callback
            $this->initializeProvider();

            // Capture the payment
            $response = $this->provider->capturePaymentOrder($token);

            if (isset($response['status']) && $response['status'] === 'COMPLETED') {
                if (Session::get('payment_type') == "Wallet" && !is_null(Session::get('wallet_payment_id'))) {
                    $status = WalletTransaction::find(Session::get('wallet_payment_id'));
                    $status->status = "approve";
                    $status->updated_at = date('Y-m-d');
                    $result = $status->update();

                    if ($result) {
                        $user = User::find($status->user_id);
                        $currentBalance = $user->wallet_balance;
                        $user->wallet_balance = $currentBalance + $status->amount;
                        $user->update();

                        $gs = generalSetting();
                        $compact['full_name'] = $user->full_name;
                        $compact['method'] = $status->payment_method;
                        $compact['create_date'] = date('Y-m-d');
                        $compact['school_name'] = $gs->school_name;
                        $compact['current_balance'] = $user->wallet_balance;
                        $compact['add_balance'] = $status->amount;

                        @send_mail($user->email, $user->full_name, "wallet_approve", $compact);
                    }

                    $this->clearSession();
                    Toastr::success('Payment successful', 'Success');
                    return redirect()->route('wallet.my-wallet');

                } elseif (Session::get('payment_type') == "Fees" && !is_null(Session::get('fees_payment_id'))) {
                    $transcation = FmFeesTransaction::find(Session::get('fees_payment_id'));

                    $addAmount = new FeesController;
                    $addAmount->addFeesAmount(Session::get('fees_payment_id'), null);

                    $this->clearSession();
                    Toastr::success('Payment successful', 'Success');
                    return redirect()->to(url('fees/student-fees-list', $transcation->student_id));
                } else {
                    Toastr::error('Operation Failed - invalid payment type', 'Failed');
                    return redirect()->back();
                }
            } else {
                Log::error('PayPal capture failed: ' . json_encode($response));
                Toastr::error('Payment capture failed', 'Failed');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            Log::error('PayPal callback error: ' . $e->getMessage());
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->send()->back();
        }
    }

    public function cancelCallback()
    {
        $this->clearSession();
        Toastr::error('Payment Cancelled', 'Cancelled');
        return redirect()->route('wallet.my-wallet');
    }

    /**
     * Initialize PayPal provider (for callback when constructor may not have run)
     */
    private function initializeProvider()
    {
        if ($this->provider) {
            return;
        }

        $paypalDetails = SmPaymentGatewaySetting::where('school_id', auth()->user()->school_id)
            ->select('gateway_client_id', 'gateway_secret_key')
            ->where('gateway_name', '=', 'Paypal')
            ->first();

        if (!$paypalDetails) {
            throw new Exception('PayPal credentials not found');
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
    }

    /**
     * Clear all PayPal session data
     */
    private function clearSession()
    {
        Session::forget('paypal_order_id');
        Session::forget('payment_type');
        Session::forget('wallet_payment_id');
        Session::forget('invoice_id');
        Session::forget('amount');
        Session::forget('payment_method');
        Session::forget('transcation_id');
        Session::forget('fees_payment_id');
    }
}
