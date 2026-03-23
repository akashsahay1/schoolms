<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Portal\Traits\PortalStudentTrait;
use App\Models\Student;
use App\Models\FeeCollection;
use App\Models\FeeStructure;
use App\Models\AcademicYear;
use App\Models\RouteAssignment;
use App\Models\TransportFee;
use App\Models\TransportFeeCollection;
use App\Services\StudentFeeService;
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
        $activeYear = AcademicYear::getActive();

        // Academic fee structures for student's class
        $feeStructures = FeeStructure::where('class_id', $student->class_id)
            ->where('is_active', true)
            ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
            ->with(['feeType', 'feeGroup'])
            ->get();

        $feeStructureIds = $feeStructures->pluck('id')->toArray();

        // Academic fee collections
        $feeCollections = FeeCollection::where('student_id', $student->id)
            ->whereIn('fee_structure_id', $feeStructureIds)
            ->with(['feeStructure.feeType', 'feeStructure.feeGroup'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Transport fee data
        $transportData = null;
        if ($activeYear) {
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

                $transportCollections = $transportFee
                    ? TransportFeeCollection::where('student_id', $student->id)
                        ->where('transport_fee_id', $transportFee->id)
                        ->get()
                    : collect();

                $transportTotal = $transportCollections->sum(fn($c) => $c->amount + $c->fine - $c->discount);
                $transportPaid = $transportCollections->sum('paid_amount');

                $transportData = [
                    'route_name' => $assignment->route->route_name ?? '-',
                    'monthly_fare' => $assignment->route->fare_amount ?? 0,
                    'total_generated' => $transportTotal,
                    'total_paid' => $transportPaid,
                    'total_due' => max(0, $transportTotal - $transportPaid),
                    'collections' => $transportCollections,
                ];
            }
        }

        // Combined stats using service
        $stats = StudentFeeService::getStudentFeeStats($student->id, $activeYear?->id);

        return view('portal.fees.overview', compact(
            'student',
            'feeStructures',
            'feeCollections',
            'stats',
            'transportData'
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
