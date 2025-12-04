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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Modules\Fees\Entities\FmFeesTransaction;
use Modules\Wallet\Entities\WalletTransaction;
use Modules\Fees\Http\Controllers\FeesController;

class PhonePePayment
{
    private string $merchantId;
    private string $saltKey;
    private string $saltIndex;
    private string $baseUrl;

    public function __construct()
    {
        $phonePeDetails = SmPaymentGatewaySetting::where('school_id', auth()->user()->school_id)
            ->where('gateway_name', '=', 'PhonePe')
            ->first();

        if (!$phonePeDetails || !$phonePeDetails->gateway_secret_key) {
            Toastr::warning('PhonePe Credentials Cannot Be Blank', 'Warning');
            return redirect()->send()->back();
        }

        $this->merchantId = $phonePeDetails->gateway_client_id;
        $this->saltKey = $phonePeDetails->gateway_secret_key;
        $this->saltIndex = $phonePeDetails->gateway_signature ?? '1';

        // Use sandbox for testing, production for live
        $this->baseUrl = $phonePeDetails->gateway_mode === 'sandbox'
            ? 'https://api-preprod.phonepe.com/apis/pg-sandbox'
            : 'https://api.phonepe.com/apis/hermes';
    }

    public function handle(array $data): string
    {
        try {
            $transactionId = 'TXN_' . time() . '_' . uniqid();
            $amount = (int)($data['amount'] * 100); // Convert to paise

            $payload = [
                'merchantId' => $this->merchantId,
                'merchantTransactionId' => $transactionId,
                'merchantUserId' => 'USER_' . auth()->id(),
                'amount' => $amount,
                'redirectUrl' => URL::to('payment_gateway_success_callback', 'PhonePe'),
                'redirectMode' => 'POST',
                'callbackUrl' => URL::to('payment_gateway_callback', 'PhonePe'),
                'paymentInstrument' => [
                    'type' => 'PAY_PAGE'
                ]
            ];

            $base64Payload = base64_encode(json_encode($payload));
            $checksum = hash('sha256', $base64Payload . '/pg/v1/pay' . $this->saltKey) . '###' . $this->saltIndex;

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-VERIFY' => $checksum,
            ])->post($this->baseUrl . '/pg/v1/pay', [
                'request' => $base64Payload
            ]);

            $responseData = $response->json();

            if ($responseData['success'] ?? false) {
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

                    Session::put('phonepe_transaction_id', $transactionId);
                    Session::put('payment_type', $data['type']);
                    Session::put('wallet_payment_id', $addPayment->id);
                } else {
                    Session::put('phonepe_transaction_id', $transactionId);
                    Session::put('payment_type', $data['type']);
                    Session::put('invoice_id', $data['invoice_id']);
                    Session::put('amount', $data['amount']);
                    Session::put('payment_method', $data['payment_method']);
                    Session::put('fees_payment_id', $data['transcationId']);
                }

                return $responseData['data']['instrumentResponse']['redirectInfo']['url'];
            }

            throw new Exception($responseData['message'] ?? 'PhonePe payment initiation failed');
        } catch (Exception $e) {
            Log::error('PhonePe Payment Error: ' . $e->getMessage());
            Toastr::error('Payment initiation failed', 'Error');
            throw $e;
        }
    }

    public function successCallback()
    {
        $request = App::make(Request::class);

        try {
            $transactionId = Session::get('phonepe_transaction_id');

            // Verify payment status
            $statusUrl = $this->baseUrl . '/pg/v1/status/' . $this->merchantId . '/' . $transactionId;
            $checksum = hash('sha256', '/pg/v1/status/' . $this->merchantId . '/' . $transactionId . $this->saltKey) . '###' . $this->saltIndex;

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-VERIFY' => $checksum,
                'X-MERCHANT-ID' => $this->merchantId,
            ])->get($statusUrl);

            $responseData = $response->json();

            if (($responseData['success'] ?? false) && ($responseData['data']['state'] ?? '') === 'COMPLETED') {
                if (Session::get('payment_type') === 'Wallet' && Session::get('wallet_payment_id')) {
                    $status = WalletTransaction::find(Session::get('wallet_payment_id'));
                    $status->status = 'approve';
                    $status->updated_at = date('Y-m-d');
                    $result = $status->update();

                    if ($result) {
                        $user = User::find($status->user_id);
                        $currentBalance = $user->wallet_balance;
                        $user->wallet_balance = $currentBalance + $status->amount;
                        $user->update();

                        $gs = generalSetting();
                        $compact = [
                            'full_name' => $user->full_name,
                            'method' => $status->payment_method,
                            'create_date' => date('Y-m-d'),
                            'school_name' => $gs->school_name,
                            'current_balance' => $user->wallet_balance,
                            'add_balance' => $status->amount,
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

            Toastr::error('Payment verification failed', 'Failed');
            return redirect()->back();

        } catch (Exception $e) {
            Log::error('PhonePe Callback Error: ' . $e->getMessage());
            Toastr::error('Payment verification failed', 'Failed');
            return redirect()->back();
        }
    }

    public function cancelCallback()
    {
        $this->clearSession();
        Toastr::error('Payment was cancelled', 'Cancelled');
        return redirect()->route('wallet.my-wallet');
    }

    private function clearSession(): void
    {
        Session::forget([
            'phonepe_transaction_id',
            'payment_type',
            'wallet_payment_id',
            'invoice_id',
            'amount',
            'payment_method',
            'fees_payment_id',
        ]);
    }
}
