<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\FeeCollection;
use App\Models\FeeStructure;
use App\Models\Payment;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class PaymentController extends Controller
{
    protected $paymentSetting;

    public function __construct()
    {
        $this->paymentSetting = PaymentSetting::getActive();
    }

    /**
     * Get Razorpay API instance.
     */
    protected function getRazorpayApi()
    {
        if (!$this->paymentSetting || $this->paymentSetting->gateway !== 'razorpay') {
            return null;
        }
        return new Api($this->paymentSetting->key_id, $this->paymentSetting->key_secret);
    }

    /**
     * Show payment checkout page.
     */
    public function checkout(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)
            ->with(['schoolClass', 'section'])
            ->first();

        if (!$student) {
            return redirect()->route('portal.dashboard')->with('error', 'Student profile not found.');
        }

        // Get fee structures for student's class
        $feeStructures = FeeStructure::where('class_id', $student->class_id)
            ->where('is_active', true)
            ->with(['feeType', 'feeGroup'])
            ->get();

        // Get fee structure IDs for this class
        $feeStructureIds = $feeStructures->pluck('id')->toArray();

        // Get already paid amounts (only for fee structures of this class)
        $paidAmounts = FeeCollection::where('student_id', $student->id)
            ->whereIn('fee_structure_id', $feeStructureIds)
            ->selectRaw('fee_structure_id, SUM(paid_amount) as total_paid')
            ->groupBy('fee_structure_id')
            ->pluck('total_paid', 'fee_structure_id')
            ->toArray();

        $discounts = FeeCollection::where('student_id', $student->id)
            ->whereIn('fee_structure_id', $feeStructureIds)
            ->selectRaw('fee_structure_id, SUM(discount_amount) as total_discount')
            ->groupBy('fee_structure_id')
            ->pluck('total_discount', 'fee_structure_id')
            ->toArray();

        // Calculate pending fees
        $pendingFees = [];
        $totalDue = 0;

        foreach ($feeStructures as $structure) {
            $paid = $paidAmounts[$structure->id] ?? 0;
            $discount = $discounts[$structure->id] ?? 0;
            $due = $structure->amount - $paid - $discount;

            if ($due > 0) {
                $pendingFees[] = [
                    'id' => $structure->id,
                    'name' => $structure->feeType->name ?? 'Fee',
                    'group' => $structure->feeGroup->name ?? '',
                    'total' => $structure->amount,
                    'paid' => $paid,
                    'discount' => $discount,
                    'due' => $due,
                ];
                $totalDue += $due;
            }
        }

        if ($totalDue <= 0) {
            return redirect()->route('portal.fees.overview')->with('success', 'All fees are already paid!');
        }

        // Check if payment gateway is active from database settings
        $razorpayConfigured = $this->paymentSetting && $this->paymentSetting->is_active;

        return view('portal.fees.checkout', compact('student', 'pendingFees', 'totalDue', 'razorpayConfigured'));
    }

    /**
     * Create Razorpay order.
     */
    public function createOrder(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'fee_structure_ids' => 'required|array',
            'fee_structure_ids.*' => 'exists:fee_structures,id',
        ]);

        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $amount = $request->amount;
        $currency = $this->paymentSetting->currency ?? 'INR';
        $amountInSmallestUnit = $amount * 100; // Convert to smallest currency unit (paise for INR)

        // Check if demo mode is enabled or credentials are missing
        $isDemoMode = !$this->paymentSetting
            || $this->paymentSetting->is_demo_mode
            || empty($this->paymentSetting->key_id)
            || empty($this->paymentSetting->key_secret);

        if ($isDemoMode) {
            // Demo mode - create fake order for simulation
            $demoOrderId = 'demo_order_' . time() . '_' . $student->id;

            // Store payment record
            $payment = Payment::create([
                'student_id' => $student->id,
                'razorpay_order_id' => $demoOrderId,
                'amount' => $amount,
                'currency' => $currency,
                'fee_structure_ids' => json_encode($request->fee_structure_ids),
                'status' => 'created',
            ]);

            return response()->json([
                'demo_mode' => true,
                'order_id' => $demoOrderId,
                'payment_id' => $payment->id,
                'amount' => $amountInSmallestUnit,
                'currency' => $currency,
                'name' => config('app.name'),
                'description' => 'Fee Payment (Demo)',
                'prefill' => [
                    'name' => $student->name,
                    'email' => $user->email,
                    'contact' => $student->phone ?? '',
                ],
            ]);
        }

        // Handle based on gateway type
        $gateway = $this->paymentSetting->gateway;

        try {
            switch ($gateway) {
                case 'razorpay':
                    return $this->createRazorpayOrder($student, $user, $request, $amount, $amountInSmallestUnit, $currency);
                case 'stripe':
                    return $this->createStripeOrder($student, $user, $request, $amount, $amountInSmallestUnit, $currency);
                default:
                    // For other gateways, use demo mode
                    return response()->json([
                        'error' => 'Gateway "' . $this->paymentSetting->gateway_name . '" is not yet fully implemented. Please use demo mode.'
                    ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Payment order creation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create order. Please try again.'], 500);
        }
    }

    /**
     * Create Razorpay order.
     */
    protected function createRazorpayOrder($student, $user, $request, $amount, $amountInSmallestUnit, $currency)
    {
        $razorpay = $this->getRazorpayApi();

        $order = $razorpay->order->create([
            'amount' => $amountInSmallestUnit,
            'currency' => $currency,
            'receipt' => 'rcpt_' . $student->id . '_' . time(),
            'notes' => [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'fee_structure_ids' => implode(',', $request->fee_structure_ids),
            ],
        ]);

        // Store payment record
        $payment = Payment::create([
            'student_id' => $student->id,
            'razorpay_order_id' => $order->id,
            'amount' => $amount,
            'currency' => $currency,
            'fee_structure_ids' => json_encode($request->fee_structure_ids),
            'status' => 'created',
        ]);

        return response()->json([
            'demo_mode' => false,
            'order_id' => $order->id,
            'amount' => $amountInSmallestUnit,
            'currency' => $currency,
            'key' => $this->paymentSetting->key_id,
            'name' => config('app.name'),
            'description' => 'Fee Payment',
            'prefill' => [
                'name' => $student->name,
                'email' => $user->email,
                'contact' => $student->phone ?? '',
            ],
        ]);
    }

    /**
     * Create Stripe payment intent (placeholder for future implementation).
     */
    protected function createStripeOrder($student, $user, $request, $amount, $amountInSmallestUnit, $currency)
    {
        // Stripe implementation would go here
        // For now, return error
        return response()->json([
            'error' => 'Stripe integration coming soon. Please use demo mode for testing.'
        ], 400);
    }

    /**
     * Handle payment success callback.
     */
    public function success(Request $request)
    {
        $request->validate([
            'razorpay_order_id' => 'required',
            'razorpay_payment_id' => 'required',
            'razorpay_signature' => 'required',
        ]);

        $payment = Payment::where('razorpay_order_id', $request->razorpay_order_id)->first();

        if (!$payment) {
            return redirect()->route('portal.fees.overview')->with('error', 'Payment record not found.');
        }

        try {
            // Verify signature
            $attributes = [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
            ];

            $razorpay = $this->getRazorpayApi();
            if ($razorpay) {
                $razorpay->utility->verifyPaymentSignature($attributes);
            }

            DB::beginTransaction();

            // Update payment record
            $payment->update([
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            // Create fee collection records
            $feeStructureIds = json_decode($payment->fee_structure_ids, true);
            $remainingAmount = $payment->amount;

            // Get current academic year
            $academicYear = \App\Models\AcademicYear::where('is_active', true)->first();
            $student = Student::find($payment->student_id);

            foreach ($feeStructureIds as $structureId) {
                if ($remainingAmount <= 0) break;

                $structure = FeeStructure::find($structureId);
                if (!$structure) continue;

                // Calculate how much is still due for this structure
                $paidForStructure = FeeCollection::where('student_id', $payment->student_id)
                    ->where('fee_structure_id', $structureId)
                    ->sum('paid_amount');

                $discountForStructure = FeeCollection::where('student_id', $payment->student_id)
                    ->where('fee_structure_id', $structureId)
                    ->sum('discount_amount');

                $dueForStructure = $structure->amount - $paidForStructure - $discountForStructure;

                if ($dueForStructure > 0) {
                    $payingNow = min($remainingAmount, $dueForStructure);

                    // Generate receipt number
                    $receiptNo = 'RZP-' . date('Ymd') . '-' . str_pad(FeeCollection::count() + 1, 5, '0', STR_PAD_LEFT);

                    FeeCollection::create([
                        'student_id' => $payment->student_id,
                        'fee_structure_id' => $structureId,
                        'academic_year_id' => $academicYear->id ?? $student->academic_year_id ?? 1,
                        'collected_by' => $student->user_id, // Self payment via portal
                        'amount' => $structure->amount,
                        'discount_amount' => 0,
                        'fine_amount' => 0,
                        'paid_amount' => $payingNow,
                        'payment_mode' => 'online',
                        'transaction_id' => $request->razorpay_payment_id,
                        'payment_date' => now(),
                        'remarks' => 'Paid via Razorpay',
                        'receipt_no' => $receiptNo,
                    ]);

                    $remainingAmount -= $payingNow;
                }
            }

            DB::commit();

            return redirect()->route('portal.payment.receipt', $payment->id)
                ->with('success', 'Payment successful! Thank you for your payment.');

        } catch (SignatureVerificationError $e) {
            Log::error('Razorpay signature verification failed: ' . $e->getMessage());
            $payment->update(['status' => 'failed']);
            return redirect()->route('portal.fees.overview')->with('error', 'Payment verification failed. Please contact support.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment processing error: ' . $e->getMessage());
            $payment->update(['status' => 'failed']);
            return redirect()->route('portal.fees.overview')->with('error', 'Payment processing failed. Please contact support.');
        }
    }

    /**
     * Handle payment failure.
     */
    public function failure(Request $request)
    {
        if ($request->razorpay_order_id) {
            $payment = Payment::where('razorpay_order_id', $request->razorpay_order_id)->first();
            if ($payment) {
                $payment->update(['status' => 'failed']);
            }
        }

        return redirect()->route('portal.fees.overview')->with('error', 'Payment was cancelled or failed. Please try again.');
    }

    /**
     * Handle demo payment success (simulation).
     */
    public function demoSuccess(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
        ]);

        $payment = Payment::find($request->payment_id);
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student || $payment->student_id !== $student->id) {
            return redirect()->route('portal.fees.overview')->with('error', 'Unauthorized access.');
        }

        if ($payment->status === 'paid') {
            return redirect()->route('portal.payment.receipt', $payment->id)
                ->with('info', 'This payment has already been processed.');
        }

        try {
            DB::beginTransaction();

            // Generate demo payment ID
            $demoPaymentId = 'demo_pay_' . time();

            // Update payment record
            $payment->update([
                'razorpay_payment_id' => $demoPaymentId,
                'razorpay_signature' => 'demo_signature_' . md5($demoPaymentId),
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            // Create fee collection records
            $feeStructureIds = json_decode($payment->fee_structure_ids, true);
            $remainingAmount = $payment->amount;

            // Get current academic year
            $academicYear = \App\Models\AcademicYear::where('is_active', true)->first();

            foreach ($feeStructureIds as $structureId) {
                if ($remainingAmount <= 0) break;

                $structure = FeeStructure::find($structureId);
                if (!$structure) continue;

                // Calculate how much is still due for this structure
                $paidForStructure = FeeCollection::where('student_id', $payment->student_id)
                    ->where('fee_structure_id', $structureId)
                    ->sum('paid_amount');

                $discountForStructure = FeeCollection::where('student_id', $payment->student_id)
                    ->where('fee_structure_id', $structureId)
                    ->sum('discount_amount');

                $dueForStructure = $structure->amount - $paidForStructure - $discountForStructure;

                if ($dueForStructure > 0) {
                    $payingNow = min($remainingAmount, $dueForStructure);

                    // Generate receipt number
                    $receiptNo = 'DEMO-' . date('Ymd') . '-' . str_pad(FeeCollection::count() + 1, 5, '0', STR_PAD_LEFT);

                    FeeCollection::create([
                        'student_id' => $payment->student_id,
                        'fee_structure_id' => $structureId,
                        'academic_year_id' => $academicYear->id ?? $student->academic_year_id ?? 1,
                        'collected_by' => $student->user_id,
                        'amount' => $structure->amount,
                        'discount_amount' => 0,
                        'fine_amount' => 0,
                        'paid_amount' => $payingNow,
                        'payment_mode' => 'online',
                        'transaction_id' => $demoPaymentId,
                        'payment_date' => now(),
                        'remarks' => 'Demo Payment - Simulated',
                        'receipt_no' => $receiptNo,
                    ]);

                    $remainingAmount -= $payingNow;
                }
            }

            DB::commit();

            return redirect()->route('portal.payment.receipt', $payment->id)
                ->with('success', 'Demo payment successful! This is a simulated payment for testing purposes.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Demo payment processing error: ' . $e->getMessage());
            $payment->update(['status' => 'failed']);
            return redirect()->route('portal.fees.overview')->with('error', 'Demo payment processing failed.');
        }
    }

    /**
     * Show payment receipt.
     */
    public function receipt(Payment $payment)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student || $payment->student_id !== $student->id) {
            abort(403, 'Unauthorized access');
        }

        $payment->load('student.schoolClass', 'student.section');

        $feeStructureIds = json_decode($payment->fee_structure_ids, true);
        $feeStructures = FeeStructure::whereIn('id', $feeStructureIds)
            ->with(['feeType', 'feeGroup'])
            ->get();

        return view('portal.fees.payment-receipt', compact('payment', 'student', 'feeStructures'));
    }
}
