<?php

namespace App\Http\Controllers;

use App\Models\FeeCollection;
use App\Models\FeeStructure;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\AcademicYear;
use App\Http\Controllers\Admin\SettingsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FeeCollectionController extends Controller
{
    public function index(Request $request)
    {
        $activeYear = AcademicYear::getActive();
        $classes = SchoolClass::with('sections')->ordered()->get();
        
        $students = collect();
        $selectedClass = null;
        $selectedSection = null;

        if ($request->filled('class_id')) {
            $selectedClass = SchoolClass::find($request->class_id);
            
            $query = Student::with(['schoolClass', 'section'])
                ->where('class_id', $request->class_id);

            if ($request->filled('section_id')) {
                $query->where('section_id', $request->section_id);
                $selectedSection = Section::find($request->section_id);
            }

            $students = $query->orderBy('roll_no')->get();

            // Get fee structures for the selected class
            if ($activeYear && $students->count() > 0) {
                $feeStructures = FeeStructure::with(['feeType', 'feeGroup'])
                    ->where('academic_year_id', $activeYear->id)
                    ->where('class_id', $request->class_id)
                    ->where('is_active', true)
                    ->get();

                // Calculate pending fees for each student
                foreach ($students as $student) {
                    $student->pendingFees = $this->calculatePendingFees($student, $feeStructures, $activeYear);
                }
            }
        }

        return view('fees.collection.index', compact(
            'classes',
            'students',
            'activeYear',
            'selectedClass',
            'selectedSection'
        ));
    }

    public function collectFee(Student $student)
    {
        $activeYear = AcademicYear::getActive();
        
        if (!$activeYear) {
            return redirect()->route('admin.fees.collection')
                ->with('error', 'No active academic year found.');
        }

        // Get all fee structures for student's class
        $feeStructures = FeeStructure::with(['feeType', 'feeGroup'])
            ->where('academic_year_id', $activeYear->id)
            ->where('class_id', $student->class_id)
            ->where('is_active', true)
            ->get();

        // Get existing payments
        $paidFees = FeeCollection::where('student_id', $student->id)
            ->where('academic_year_id', $activeYear->id)
            ->pluck('fee_structure_id')
            ->toArray();

        // Filter unpaid fees
        $unpaidFees = $feeStructures->whereNotIn('id', $paidFees);

        // Get payment history
        $paymentHistory = FeeCollection::with(['feeStructure.feeType', 'collectedBy'])
            ->where('student_id', $student->id)
            ->where('academic_year_id', $activeYear->id)
            ->latest()
            ->get();

        return view('fees.collection.collect', compact(
            'student',
            'unpaidFees',
            'paymentHistory',
            'activeYear'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'fee_structure_ids' => 'required|array',
            'fee_structure_ids.*' => 'exists:fee_structures,id',
            'payment_mode' => 'required|in:cash,cheque,dd,online,bank_transfer',
            'transaction_id' => 'nullable|string|max:100',
            'payment_date' => 'required|date',
            'discount_amount' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        $activeYear = AcademicYear::getActive();
        
        if (!$activeYear) {
            return redirect()->back()
                ->with('error', 'No active academic year found.');
        }

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            $collections = [];

            foreach ($validated['fee_structure_ids'] as $feeStructureId) {
                $feeStructure = FeeStructure::find($feeStructureId);
                
                // Check if already paid
                $existing = FeeCollection::where('student_id', $validated['student_id'])
                    ->where('fee_structure_id', $feeStructureId)
                    ->where('academic_year_id', $activeYear->id)
                    ->exists();

                if ($existing) {
                    continue;
                }

                // Calculate fine if applicable
                $fineAmount = 0;
                if ($feeStructure->due_date && $feeStructure->fine_type !== 'none') {
                    $dueDate = Carbon::parse($feeStructure->due_date);
                    $paymentDate = Carbon::parse($validated['payment_date']);
                    
                    if ($paymentDate->isAfter($dueDate)) {
                        if ($feeStructure->fine_type === 'percentage') {
                            $fineAmount = ($feeStructure->amount * $feeStructure->fine_amount) / 100;
                        } else {
                            $fineAmount = $feeStructure->fine_amount;
                        }
                    }
                }

                $amount = $feeStructure->amount;
                $paidAmount = $amount + $fineAmount - ($validated['discount_amount'] ?? 0);
                $totalAmount += $paidAmount;

                $collection = FeeCollection::create([
                    'student_id' => $validated['student_id'],
                    'fee_structure_id' => $feeStructureId,
                    'academic_year_id' => $activeYear->id,
                    'collected_by' => auth()->id(),
                    'amount' => $amount,
                    'discount_amount' => $validated['discount_amount'] ?? 0,
                    'fine_amount' => $fineAmount,
                    'paid_amount' => $paidAmount,
                    'payment_mode' => $validated['payment_mode'],
                    'transaction_id' => $validated['transaction_id'],
                    'payment_date' => $validated['payment_date'],
                    'remarks' => $validated['remarks'],
                ]);

                $collections[] = $collection;
            }

            DB::commit();

            if (count($collections) > 0) {
                return redirect()->route('admin.fees.receipt', $collections[0])
                    ->with('success', 'Fee collected successfully! Total Amount: ₹' . number_format($totalAmount, 2));
            }

            return redirect()->route('admin.fees.collection')
                ->with('info', 'Selected fees were already paid.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to collect fee. Please try again.');
        }
    }

    public function outstanding(Request $request)
    {
        $activeYear = AcademicYear::getActive();
        $classes = SchoolClass::ordered()->get();
        
        $outstandingData = collect();
        $selectedClass = null;
        $totalOutstanding = 0;

        if ($activeYear) {
            $query = Student::with(['schoolClass', 'section']);

            if ($request->filled('class_id')) {
                $query->where('class_id', $request->class_id);
                $selectedClass = SchoolClass::find($request->class_id);
            }

            $students = $query->get();

            foreach ($students as $student) {
                // Get fee structures for student's class
                $feeStructures = FeeStructure::with(['feeType', 'feeGroup'])
                    ->where('academic_year_id', $activeYear->id)
                    ->where('class_id', $student->class_id)
                    ->where('is_active', true)
                    ->get();

                // Get paid fees
                $paidFeeIds = FeeCollection::where('student_id', $student->id)
                    ->where('academic_year_id', $activeYear->id)
                    ->pluck('fee_structure_id')
                    ->toArray();

                // Calculate outstanding
                $outstandingFees = $feeStructures->whereNotIn('id', $paidFeeIds);
                $outstandingAmount = $outstandingFees->sum('amount');

                if ($outstandingAmount > 0) {
                    $outstandingData->push([
                        'student' => $student,
                        'outstanding_fees' => $outstandingFees,
                        'total_amount' => $outstandingAmount,
                    ]);
                    $totalOutstanding += $outstandingAmount;
                }
            }
        }

        return view('fees.outstanding.index', compact(
            'outstandingData',
            'classes',
            'selectedClass',
            'activeYear',
            'totalOutstanding'
        ));
    }

    public function receipt(FeeCollection $feeCollection)
    {
        // Get all collections from the same payment session
        $collections = FeeCollection::with([
                'feeStructure.feeType',
                'feeStructure.feeGroup',
                'student.schoolClass',
                'student.section',
                'academicYear',
                'collectedBy'
            ])
            ->where('student_id', $feeCollection->student_id)
            ->where('payment_date', $feeCollection->payment_date)
            ->where('created_at', '>=', $feeCollection->created_at->subMinutes(1))
            ->where('created_at', '<=', $feeCollection->created_at->addMinutes(1))
            ->get();

        // Get school settings
        $schoolSettings = [
            'school_name' => SettingsController::getSchoolSetting('school_name', 'Shree Education Academy'),
            'school_address' => SettingsController::getSchoolSetting('school_address', '123 School Street, Education City - 123456'),
            'school_phone' => SettingsController::getSchoolSetting('school_phone', '+91 98765 43210'),
            'school_email' => SettingsController::getSchoolSetting('school_email', 'info@shreeeducation.com'),
            'school_logo' => SettingsController::getSchoolSetting('school_logo'),
            'signature_image' => SettingsController::getSchoolSetting('signature_image'),
            'authorized_signature_text' => SettingsController::getSchoolSetting('authorized_signature_text'),
        ];

        return view('fees.receipt.show', compact('collections', 'feeCollection', 'schoolSettings'));
    }

    private function calculatePendingFees($student, $feeStructures, $activeYear)
    {
        $paidFeeIds = FeeCollection::where('student_id', $student->id)
            ->where('academic_year_id', $activeYear->id)
            ->pluck('fee_structure_id')
            ->toArray();

        $pendingFees = $feeStructures->whereNotIn('id', $paidFeeIds);
        
        return [
            'count' => $pendingFees->count(),
            'amount' => $pendingFees->sum('amount'),
            'fees' => $pendingFees
        ];
    }
}