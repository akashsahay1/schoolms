<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\FeeCollection;
use App\Models\FeeStructure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeeController extends Controller
{
    /**
     * Display fee overview.
     */
    public function overview()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)
            ->with(['schoolClass', 'section'])
            ->first();

        if (!$student) {
            return redirect()->route('portal.dashboard');
        }

        // Get fee structure for student's class
        $feeStructures = FeeStructure::where('class_id', $student->class_id)
            ->where('is_active', true)
            ->with(['feeType', 'feeGroup'])
            ->get();

        // Get fee collections
        $feeCollections = FeeCollection::where('student_id', $student->id)
            ->with(['feeStructure.feeType', 'feeStructure.feeGroup'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate totals
        $totalFees = $feeStructures->sum('amount');
        $totalPaid = $feeCollections->sum('amount_paid');
        $totalDiscount = $feeCollections->sum('discount');
        $totalDue = $totalFees - $totalPaid - $totalDiscount;

        $stats = [
            'total_fees' => $totalFees,
            'total_paid' => $totalPaid,
            'total_discount' => $totalDiscount,
            'total_due' => max(0, $totalDue),
        ];

        return view('portal.fees.overview', compact(
            'student',
            'feeStructures',
            'feeCollections',
            'stats'
        ));
    }

    /**
     * Display payment history.
     */
    public function history()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return redirect()->route('portal.dashboard');
        }

        $payments = FeeCollection::where('student_id', $student->id)
            ->with(['feeStructure.feeType', 'feeStructure.feeGroup'])
            ->orderBy('payment_date', 'desc')
            ->paginate(20);

        return view('portal.fees.history', compact('student', 'payments'));
    }

    /**
     * View a specific receipt.
     */
    public function receipt(FeeCollection $feeCollection)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        // Security check - ensure the receipt belongs to this student
        if (!$student || $feeCollection->student_id !== $student->id) {
            abort(403, 'Unauthorized access');
        }

        $feeCollection->load(['student.schoolClass', 'student.section', 'feeStructure.feeType']);

        return view('portal.fees.receipt', compact('feeCollection', 'student'));
    }
}
