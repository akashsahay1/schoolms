<?php

namespace App\PaymentGateway;

use App\User;
use Exception;
use App\SmPaymentGatewaySetting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Modules\Fees\Entities\FmFeesTransaction;
use Modules\Wallet\Entities\WalletTransaction;
use Modules\Fees\Http\Controllers\FeesController;

class PayUPayment
{
    private string $merchantKey;
    private string $merchantSalt;
    private string $baseUrl;

    public function __construct()
    {
        $payUDetails = SmPaymentGatewaySetting::where('school_id', auth()->user()->school_id)
            ->where('gateway_name', '=', 'PayU')
            ->first();

        if (!$payUDetails || !$payUDetails->gateway_secret_key) {
            Toastr::warning('PayU Credentials Cannot Be Blank', 'Warning');
            return redirect()->send()->back();
        }

        $this->merchantKey = $payUDetails->gateway_client_id;
        $this->merchantSalt = $payUDetails->gateway_secret_key;

        // Use test URL for testing, production for live
        $this->baseUrl = $payUDetails->gateway_mode === 'sandbox'
            ? 'https://test.payu.in/_payment'
            : 'https://secure.payu.in/_payment';
    }

    public function handle(array $data): string
    {
        try {
            $txnId = 'TXN_' . time() . '_' . uniqid();
            $amount = number_format($data['amount'], 2, '.', '');
            $productInfo = 'Fees Payment';
            $firstName = auth()->user()->first_name ?? 'User';
            $email = auth()->user()->email;
            $phone = auth()->user()->mobile ?? '';

            $successUrl = URL::to('payment_gateway_success_callback', 'PayU');
            $failureUrl = URL::to('payment_gateway_cancel_callback', 'PayU');

            // Generate hash
            $hashString = $this->merchantKey . '|' . $txnId . '|' . $amount . '|' . $productInfo . '|' . $firstName . '|' . $email . '|||||||||||' . $this->merchantSalt;
            $hash = strtolower(hash('sha512', $hashString));

            // Store transaction details in session
            if ($data['type'] === 'Wallet') {
                $addPayment = new WalletTransaction();
                $addPayment->amount = $data['amount'];
                $addPayment->payment_method = $data['payment_method'];
                $addPayment->user_id = $data['user_id'];
                $addPayment->type = $data['wallet_type'];
                $addPayment->school_id = Auth::user()->school_id;
                $addPayment->academic_id = getAcademicId();
                $addPayment->save();

                Session::put('payu_txn_id', $txnId);
                Session::put('payment_type', $data['type']);
                Session::put('wallet_payment_id', $addPayment->id);
            } else {
                Session::put('payu_txn_id', $txnId);
                Session::put('payment_type', $data['type']);
                Session::put('invoice_id', $data['invoice_id']);
                Session::put('amount', $data['amount']);
                Session::put('payment_method', $data['payment_method']);
                Session::put('fees_payment_id', $data['transcationId']);
            }

            // Build the PayU form data
            $payuData = [
                'key' => $this->merchantKey,
                'txnid' => $txnId,
                'amount' => $amount,
                'productinfo' => $productInfo,
                'firstname' => $firstName,
                'email' => $email,
                'phone' => $phone,
                'surl' => $successUrl,
                'furl' => $failureUrl,
                'hash' => $hash,
                'service_provider' => 'payu_paisa',
            ];

            // Store PayU form data in session for redirection
            Session::put('payu_form_data', $payuData);
            Session::put('payu_action_url', $this->baseUrl);

            // Return a redirect URL to a form that will auto-submit
            return route('payu.redirect');

        } catch (Exception $e) {
            Log::error('PayU Payment Error: ' . $e->getMessage());
            Toastr::error('Payment initiation failed', 'Error');
            throw $e;
        }
    }

    public function successCallback()
    {
        $request = App::make(Request::class);

        try {
            $txnId = $request->input('txnid');
            $status = $request->input('status');
            $amount = $request->input('amount');
            $mihpayid = $request->input('mihpayid');
            $productInfo = $request->input('productinfo');
            $firstName = $request->input('firstname');
            $email = $request->input('email');
            $hash = $request->input('hash');

            // Verify the response hash
            $reverseHashString = $this->merchantSalt . '|' . $status . '|||||||||||' . $email . '|' . $firstName . '|' . $productInfo . '|' . $amount . '|' . $txnId . '|' . $this->merchantKey;
            $calculatedHash = strtolower(hash('sha512', $reverseHashString));

            if ($hash !== $calculatedHash) {
                Log::error('PayU hash verification failed');
                Toastr::error('Payment verification failed - Invalid hash', 'Failed');
                return redirect()->back();
            }

            if ($status === 'success') {
                if (Session::get('payment_type') === 'Wallet' && Session::get('wallet_payment_id')) {
                    $walletTransaction = WalletTransaction::find(Session::get('wallet_payment_id'));
                    $walletTransaction->status = 'approve';
                    $walletTransaction->updated_at = date('Y-m-d');
                    $result = $walletTransaction->update();

                    if ($result) {
                        $user = User::find($walletTransaction->user_id);
                        $currentBalance = $user->wallet_balance;
                        $user->wallet_balance = $currentBalance + $walletTransaction->amount;
                        $user->update();

                        $gs = generalSetting();
                        $compact = [
                            'full_name' => $user->full_name,
                            'method' => $walletTransaction->payment_method,
                            'create_date' => date('Y-m-d'),
                            'school_name' => $gs->school_name,
                            'current_balance' => $user->wallet_balance,
                            'add_balance' => $walletTransaction->amount,
                        ];

                        @send_mail($user->email, $user->full_name, 'wallet_approve', $compact);
                    }

                    $this->clearSession();
                    Toastr::success('Payment successful', 'Success');
                    return redirect()->route('wallet.my-wallet');

                } elseif (Session::get('payment_type') === 'Fees' && Session::get('fees_payment_id')) {
                    $transaction = FmFeesTransaction::find(Session::get('fees_payment_id'));

                    $addAmount = new FeesController();
                    $addAmount->addFeesAmount(Session::get('fees_payment_id'), null);

                    $this->clearSession();
                    Toastr::success('Payment successful', 'Success');
                    return redirect()->to(url('fees/student-fees-list', $transaction->student_id));
                }
            }

            Toastr::error('Payment failed', 'Failed');
            return redirect()->back();

        } catch (Exception $e) {
            Log::error('PayU Callback Error: ' . $e->getMessage());
            Toastr::error('Payment verification failed', 'Failed');
            return redirect()->back();
        }
    }

    public function cancelCallback()
    {
        $this->clearSession();
        Toastr::error('Payment was cancelled or failed', 'Failed');
        return redirect()->route('wallet.my-wallet');
    }

    private function clearSession(): void
    {
        Session::forget([
            'payu_txn_id',
            'payu_form_data',
            'payu_action_url',
            'payment_type',
            'wallet_payment_id',
            'invoice_id',
            'amount',
            'payment_method',
            'fees_payment_id',
        ]);
    }
}
