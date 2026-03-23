<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Portal\Traits\PortalStudentTrait;
use App\Models\Student;
use App\Models\FeeCollection;
use App\Models\FeeStructure;
use App\Models\AcademicYear;
use App\Models\Payment;
use App\Models\RouteAssignment;
use App\Models\TransportFee;
use App\Models\TransportFeeCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class PaymentController extends Controller
{
    use PortalStudentTrait;

    protected function getRazorpayApi()
    {
        $keyId = config('razorpay.key_id');
        $keySecret = config('razorpay.key_secret');

        if (empty($keyId) || empty($keySecret)) {
            return null;
        }

        return new Api($keyId, $keySecret);
    }

    protected function isGatewayConfigured(): bool
    {
        return !empty(config('razorpay.key_id')) && !empty(config('razorpay.key_secret'));
    }

    /**
     * Show payment checkout page with academic + transport fees.
     */
    public function checkout(Request $request)
    {
        $activeYear = AcademicYear::getActive();
        if (!$activeYear) {
            return redirect()->route('portal.fees.overview')->with('error', 'The current academic session is inactive. Online payments are temporarily unavailable.');
        }

        $student = $this->getCurrentStudent();
        if (!$student) {
            return redirect()->route('portal.dashboard')->with('error', 'Student profile not found.');
        }

        $student->load(['schoolClass', 'section']);

        // ─── Academic Fees ───
        $feeStructures = FeeStructure::where('class_id', $student->class_id)
            ->where('academic_year_id', $activeYear->id)
            ->where('is_active', true)
            ->with(['feeType', 'feeGroup'])
            ->get();

        $feeStructureIds = $feeStructures->pluck('id')->toArray();

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

        $pendingAcademic = [];
        $academicDue = 0;

        foreach ($feeStructures as $structure) {
            $paid = $paidAmounts[$structure->id] ?? 0;
            $discount = $discounts[$structure->id] ?? 0;
            $due = $structure->amount - $paid - $discount;

            if ($due > 0) {
                $pendingAcademic[] = [
                    'id' => $structure->id,
                    'type' => 'academic',
                    'name' => $structure->feeType->name ?? 'Fee',
                    'group' => $structure->feeGroup->name ?? '',
                    'total' => $structure->amount,
                    'paid' => $paid,
                    'discount' => $discount,
                    'due' => $due,
                ];
                $academicDue += $due;
            }
        }

        // ─── Transport Fees ───
        $pendingTransport = [];
        $transportDue = 0;

        $assignment = RouteAssignment::where('student_id', $student->id)
            ->where('academic_year_id', $activeYear->id)
            ->where('is_active', true)
            ->with('route')
            ->first();

        if ($assignment) {
            $transportFee = TransportFee::where('transport_route_id', $assignment->transport_route_id)
                ->where('academic_year_id', $activeYear->id)
                ->where('is_active', true)
                ->first();

            if ($transportFee) {
                $transportCollections = TransportFeeCollection::where('student_id', $student->id)
                    ->where('transport_fee_id', $transportFee->id)
                    ->where('status', '!=', 'paid')
                    ->get();

                foreach ($transportCollections as $tc) {
                    $tcDue = ($tc->amount + $tc->fine - $tc->discount) - $tc->paid_amount;
                    if ($tcDue > 0) {
                        $pendingTransport[] = [
                            'id' => $tc->id,
                            'type' => 'transport',
                            'name' => 'Transport - ' . ($tc->month ?? 'Monthly'),
                            'group' => $assignment->route->route_name ?? 'Route',
                            'total' => $tc->amount + $tc->fine - $tc->discount,
                            'paid' => $tc->paid_amount,
                            'discount' => 0,
                            'due' => $tcDue,
                        ];
                        $transportDue += $tcDue;
                    }
                }
            }
        }

        $pendingFees = array_merge($pendingAcademic, $pendingTransport);
        $totalDue = $academicDue + $transportDue;

        if ($totalDue <= 0) {
            return redirect()->route('portal.fees.overview')->with('success', 'All fees are already paid!');
        }

        $razorpayConfigured = $this->isGatewayConfigured();

        return view('portal.fees.checkout', compact(
            'student', 'pendingFees', 'totalDue', 'academicDue', 'transportDue', 'razorpayConfigured'
        ));
    }

    /**
     * Create Razorpay order with server-side amount validation.
     */
    public function createOrder(Request $request)
    {
        $activeYear = AcademicYear::getActive();
        if (!$activeYear) {
            return response()->json(['error' => 'Academic session is inactive.'], 403);
        }

        $request->validate([
            'fee_structure_ids' => 'nullable|array',
            'fee_structure_ids.*' => 'exists:fee_structures,id',
            'transport_collection_ids' => 'nullable|array',
            'transport_collection_ids.*' => 'exists:transport_fee_collections,id',
        ]);

        $user = Auth::user();
        $student = $this->getCurrentStudent();

        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $feeStructureIds = $request->fee_structure_ids ?? [];
        $transportCollectionIds = $request->transport_collection_ids ?? [];

        if (empty($feeStructureIds) && empty($transportCollectionIds)) {
            return response()->json(['error' => 'No fees selected'], 400);
        }

        // ─── Server-side amount calculation (prevents frontend tampering) ───
        $amount = 0;

        foreach ($feeStructureIds as $structureId) {
            $structure = FeeStructure::find($structureId);
            if (!$structure) continue;

            $paid = FeeCollection::where('student_id', $student->id)
                ->where('fee_structure_id', $structureId)
                ->sum('paid_amount');
            $discount = FeeCollection::where('student_id', $student->id)
                ->where('fee_structure_id', $structureId)
                ->sum('discount_amount');

            $due = $structure->amount - $paid - $discount;
            if ($due > 0) {
                $amount += $due;
            }
        }

        foreach ($transportCollectionIds as $tcId) {
            $tc = TransportFeeCollection::find($tcId);
            if (!$tc || $tc->status === 'paid' || $tc->student_id !== $student->id) continue;

            $tcDue = ($tc->amount + $tc->fine - $tc->discount) - $tc->paid_amount;
            if ($tcDue > 0) {
                $amount += $tcDue;
            }
        }

        if ($amount <= 0) {
            return response()->json(['error' => 'All selected fees are already paid.'], 400);
        }

        $currency = 'INR';
        $amountInPaise = round($amount * 100);

        // ─── Check for pending (in-flight) payment for same student ───
        $pendingPayment = Payment::where('student_id', $student->id)
            ->where('status', 'created')
            ->where('created_at', '>', now()->subMinutes(30))
            ->first();

        if ($pendingPayment) {
            // Expire old pending payment
            $pendingPayment->update(['status' => 'expired']);
        }

        $isDemoMode = !$this->isGatewayConfigured();

        if ($isDemoMode) {
            $demoOrderId = 'demo_order_' . time() . '_' . $student->id;

            $payment = Payment::create([
                'student_id' => $student->id,
                'razorpay_order_id' => $demoOrderId,
                'amount' => $amount,
                'currency' => $currency,
                'fee_structure_ids' => json_encode($feeStructureIds),
                'transport_fee_collection_ids' => json_encode($transportCollectionIds),
                'status' => 'created',
            ]);

            return response()->json([
                'demo_mode' => true,
                'order_id' => $demoOrderId,
                'payment_id' => $payment->id,
                'amount' => $amountInPaise,
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

        try {
            $razorpay = $this->getRazorpayApi();

            $order = $razorpay->order->create([
                'amount' => $amountInPaise,
                'currency' => $currency,
                'receipt' => 'rcpt_' . $student->id . '_' . time(),
                'notes' => [
                    'student_id' => $student->id,
                    'student_name' => $student->name,
                ],
            ]);

            $payment = Payment::create([
                'student_id' => $student->id,
                'razorpay_order_id' => $order->id,
                'amount' => $amount,
                'currency' => $currency,
                'fee_structure_ids' => json_encode($feeStructureIds),
                'transport_fee_collection_ids' => json_encode($transportCollectionIds),
                'status' => 'created',
            ]);

            return response()->json([
                'demo_mode' => false,
                'order_id' => $order->id,
                'amount' => $amountInPaise,
                'currency' => $currency,
                'key' => config('razorpay.key_id'),
                'name' => config('app.name'),
                'description' => 'Fee Payment',
                'prefill' => [
                    'name' => $student->name,
                    'email' => $user->email,
                    'contact' => $student->phone ?? '',
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Payment order creation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create order. Please try again.'], 500);
        }
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

        // Prevent duplicate processing
        if ($payment->status === 'paid') {
            return redirect()->route('portal.payment.receipt', $payment->id)
                ->with('info', 'This payment has already been processed.');
        }

        try {
            $razorpay = $this->getRazorpayApi();
            if ($razorpay) {
                $razorpay->utility->verifyPaymentSignature([
                    'razorpay_order_id' => $request->razorpay_order_id,
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                    'razorpay_signature' => $request->razorpay_signature,
                ]);
            }

            $this->processPayment($payment, $request->razorpay_payment_id, $request->razorpay_signature);

            return redirect()->route('portal.payment.receipt', $payment->id)
                ->with('success', 'Payment successful! Thank you for your payment.');

        } catch (SignatureVerificationError $e) {
            Log::error('Razorpay signature verification failed: ' . $e->getMessage(), [
                'order_id' => $request->razorpay_order_id,
                'payment_id' => $request->razorpay_payment_id,
            ]);
            $payment->update(['status' => 'failed']);
            return redirect()->route('portal.fees.overview')->with('error', 'Payment verification failed. Please contact support.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment processing error: ' . $e->getMessage(), [
                'payment_id' => $payment->id,
                'trace' => $e->getTraceAsString(),
            ]);
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
            if ($payment && $payment->status !== 'paid') {
                $payment->update(['status' => 'failed']);
                Log::warning('Payment failed/cancelled', [
                    'order_id' => $request->razorpay_order_id,
                    'student_id' => $payment->student_id,
                ]);
            }
        }

        return redirect()->route('portal.fees.overview')->with('error', 'Payment was cancelled or failed. Please try again.');
    }

    /**
     * Handle demo payment success.
     */
    public function demoSuccess(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
        ]);

        $payment = Payment::find($request->payment_id);
        $student = $this->getCurrentStudent();

        if (!$student || $payment->student_id !== $student->id) {
            return redirect()->route('portal.fees.overview')->with('error', 'Unauthorized access.');
        }

        if ($payment->status === 'paid') {
            return redirect()->route('portal.payment.receipt', $payment->id)
                ->with('info', 'This payment has already been processed.');
        }

        try {
            $demoPaymentId = 'demo_pay_' . time();
            $this->processPayment($payment, $demoPaymentId, 'demo_signature_' . md5($demoPaymentId));

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
     * Razorpay webhook handler — catches payments even if frontend fails.
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $webhookSecret = config('razorpay.webhook_secret');

        // Verify webhook signature if secret is configured
        if ($webhookSecret) {
            $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);
            $receivedSignature = $request->header('X-Razorpay-Signature');

            if (!hash_equals($expectedSignature, $receivedSignature ?? '')) {
                Log::warning('Razorpay webhook: invalid signature');
                return response()->json(['status' => 'invalid_signature'], 400);
            }
        }

        $data = json_decode($payload, true);
        $event = $data['event'] ?? '';

        Log::info('Razorpay webhook received', ['event' => $event]);

        if ($event === 'payment.captured' || $event === 'order.paid') {
            $orderId = $data['payload']['payment']['entity']['order_id'] ?? null;
            $paymentId = $data['payload']['payment']['entity']['id'] ?? null;

            if (!$orderId || !$paymentId) {
                return response()->json(['status' => 'missing_data'], 400);
            }

            $payment = Payment::where('razorpay_order_id', $orderId)->first();

            if (!$payment) {
                Log::warning('Razorpay webhook: payment record not found', ['order_id' => $orderId]);
                return response()->json(['status' => 'not_found'], 404);
            }

            // Already processed — skip
            if ($payment->status === 'paid') {
                return response()->json(['status' => 'already_processed']);
            }

            try {
                $this->processPayment($payment, $paymentId, 'webhook_verified');
                Log::info('Razorpay webhook: payment processed successfully', ['payment_id' => $payment->id]);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Razorpay webhook: processing failed', [
                    'payment_id' => $payment->id,
                    'error' => $e->getMessage(),
                ]);
                return response()->json(['status' => 'processing_failed'], 500);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Process payment — create fee collection records for both academic and transport.
     * Uses pessimistic locking to prevent concurrent duplicate processing.
     * Atomic transaction: all succeed or all fail.
     */
    private function processPayment(Payment $payment, string $paymentId, string $signature): void
    {
        DB::beginTransaction();

        // Pessimistic lock — re-fetch and lock the row to prevent concurrent processing
        $payment = Payment::where('id', $payment->id)->lockForUpdate()->first();

        if ($payment->status === 'paid') {
            DB::rollBack();
            return; // Already processed by another request (e.g., webhook + frontend race)
        }

        // Generate a single receipt number for this entire payment
        $receiptNo = 'PAY-' . date('Ymd') . '-' . str_pad($payment->id, 5, '0', STR_PAD_LEFT);

        $payment->update([
            'razorpay_payment_id' => $paymentId,
            'razorpay_signature' => $signature,
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $academicYear = AcademicYear::getActive();
        $student = Student::find($payment->student_id);
        $remainingAmount = $payment->amount;

        // ─── Process Academic Fees ───
        $feeStructureIds = json_decode($payment->fee_structure_ids, true) ?? [];

        foreach ($feeStructureIds as $structureId) {
            if ($remainingAmount <= 0) break;

            $structure = FeeStructure::find($structureId);
            if (!$structure) continue;

            $paidForStructure = FeeCollection::where('student_id', $payment->student_id)
                ->where('fee_structure_id', $structureId)
                ->sum('paid_amount');

            $discountForStructure = FeeCollection::where('student_id', $payment->student_id)
                ->where('fee_structure_id', $structureId)
                ->sum('discount_amount');

            $dueForStructure = $structure->amount - $paidForStructure - $discountForStructure;

            if ($dueForStructure > 0) {
                $payingNow = min($remainingAmount, $dueForStructure);

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
                    'transaction_id' => $paymentId,
                    'payment_date' => now(),
                    'remarks' => 'Paid via Razorpay',
                    'receipt_no' => $receiptNo,
                ]);

                $remainingAmount -= $payingNow;
            }
        }

        // ─── Process Transport Fees ───
        $transportCollectionIds = json_decode($payment->transport_fee_collection_ids, true) ?? [];

        foreach ($transportCollectionIds as $tcId) {
            if ($remainingAmount <= 0) break;

            // Lock the transport collection row too
            $tc = TransportFeeCollection::where('id', $tcId)->lockForUpdate()->first();
            if (!$tc || $tc->status === 'paid') continue;

            $tcDue = ($tc->amount + $tc->fine - $tc->discount) - $tc->paid_amount;

            if ($tcDue > 0) {
                $payingNow = min($remainingAmount, $tcDue);
                $newPaid = $tc->paid_amount + $payingNow;
                $totalOwed = $tc->amount + $tc->fine - $tc->discount;

                $tc->update([
                    'paid_amount' => $newPaid,
                    'status' => $newPaid >= $totalOwed ? 'paid' : 'partial',
                    'payment_date' => now(),
                    'payment_mode' => 'online',
                    'receipt_number' => $receiptNo,
                ]);

                $remainingAmount -= $payingNow;
            }
        }

        DB::commit();
    }

    /**
     * Show payment receipt.
     */
    public function receipt(Payment $payment)
    {
        $student = $this->getCurrentStudent();

        if (!$student || $payment->student_id !== $student->id) {
            abort(403, 'Unauthorized access');
        }

        $payment->load('student.schoolClass', 'student.section');

        $feeStructureIds = json_decode($payment->fee_structure_ids, true) ?? [];
        $feeStructures = FeeStructure::whereIn('id', $feeStructureIds)
            ->with(['feeType', 'feeGroup'])
            ->get();

        $transportCollectionIds = json_decode($payment->transport_fee_collection_ids, true) ?? [];
        $transportCollections = TransportFeeCollection::whereIn('id', $transportCollectionIds)
            ->with(['transportFee.route'])
            ->get();

        return view('portal.fees.payment-receipt', compact('payment', 'student', 'feeStructures', 'transportCollections'));
    }
}
