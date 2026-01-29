<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Portal\Traits\PortalStudentTrait;
use App\Models\Student;
use App\Models\FeeCollection;
use App\Models\FeeStructure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeeController extends Controller
{
    use PortalStudentTrait;

    /**
     * Display fee overview.
     */
    public function overview()
    {
        $student = $this->getCurrentStudent();

        if (!$student) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'No student profile found.');
        }

        $student->load(['schoolClass', 'section']);

        // Get fee structure for student's class
        $feeStructures = FeeStructure::where('class_id', $student->class_id)
            ->where('is_active', true)
            ->with(['feeType', 'feeGroup'])
            ->get();

        // Get fee structure IDs for this class
        $feeStructureIds = $feeStructures->pluck('id')->toArray();

        // Get fee collections only for the fee structures of this class
        $feeCollections = FeeCollection::where('student_id', $student->id)
            ->whereIn('fee_structure_id', $feeStructureIds)
            ->with(['feeStructure.feeType', 'feeStructure.feeGroup'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate totals properly per fee structure
        $totalFees = 0;
        $totalPaid = 0;
        $totalDiscount = 0;

        foreach ($feeStructures as $structure) {
            $totalFees += $structure->amount;

            // Get payments for this specific structure
            $structurePayments = $feeCollections->where('fee_structure_id', $structure->id);
            $totalPaid += $structurePayments->sum('paid_amount');
            $totalDiscount += $structurePayments->sum('discount_amount');
        }

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
        $student = $this->getCurrentStudent();

        if (!$student) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'No student profile found.');
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
        $student = $this->getCurrentStudent();

        // Security check - ensure the receipt belongs to this student
        if (!$student || $feeCollection->student_id !== $student->id) {
            abort(403, 'Unauthorized access');
        }

        $feeCollection->load(['student.schoolClass', 'student.section', 'feeStructure.feeType']);

        return view('portal.fees.receipt', compact('feeCollection', 'student'));
    }
}
