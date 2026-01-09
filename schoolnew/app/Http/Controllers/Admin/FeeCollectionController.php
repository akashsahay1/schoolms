<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeCollection;
use App\Models\FeeStructure;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FeeCollectionController extends Controller
{
	public function index(Request $request)
	{
		$query = FeeCollection::with(['student', 'feeStructure.feeType', 'collectedBy']);

		// Academic year filter
		if ($request->filled('academic_year')) {
			$query->where('academic_year_id', $request->academic_year);
		} else {
			// Default to current academic year
			$currentYear = AcademicYear::where('is_active', true)->first();
			if ($currentYear) {
				$query->where('academic_year_id', $currentYear->id);
			}
		}

		// Class filter
		if ($request->filled('class')) {
			$query->whereHas('student', function ($q) use ($request) {
				$q->where('class_id', $request->class);
			});
		}

		// Student search
		if ($request->filled('search')) {
			$search = $request->search;
			$query->whereHas('student', function ($q) use ($search) {
				$q->where('admission_no', 'like', "%{$search}%")
					->orWhere('first_name', 'like', "%{$search}%")
					->orWhere('last_name', 'like', "%{$search}%");
			});
		}

		// Payment mode filter
		if ($request->filled('payment_mode')) {
			$query->where('payment_mode', $request->payment_mode);
		}

		// Date range filter
		if ($request->filled('from_date')) {
			$query->whereDate('payment_date', '>=', $request->from_date);
		}
		if ($request->filled('to_date')) {
			$query->whereDate('payment_date', '<=', $request->to_date);
		}

		$collections = $query->orderBy('payment_date', 'desc')->paginate(15);
		$academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
		$classes = SchoolClass::active()->ordered()->get();

		// Calculate totals
		$totalCollected = $query->sum('paid_amount');

		return view('admin.fees.collection.index', compact('collections', 'academicYears', 'classes', 'totalCollected'));
	}

	public function collectFee(Student $student)
	{
		$student->load(['schoolClass', 'section']);

		// Get current academic year
		$currentYear = AcademicYear::where('is_active', true)->first();

		if (!$currentYear) {
			return redirect()->route('admin.fees.collection')
				->with('error', 'No active academic year found. Please set an active academic year first.');
		}

		// Get fee structures for this student's class
		$feeStructures = FeeStructure::with(['feeType', 'feeGroup'])
			->where('academic_year_id', $currentYear->id)
			->where('class_id', $student->class_id)
			->active()
			->get();

		if ($feeStructures->isEmpty()) {
			return redirect()->route('admin.fees.collection')
				->with('error', 'No fee structure found for this student\'s class.');
		}

		// Get already paid fees
		$paidFees = FeeCollection::where('student_id', $student->id)
			->where('academic_year_id', $currentYear->id)
			->pluck('fee_structure_id')
			->toArray();

		return view('admin.fees.collection.collect', compact('student', 'feeStructures', 'paidFees', 'currentYear'));
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'student_id' => ['required', 'exists:students,id'],
			'fee_structure_id' => ['required', 'exists:fee_structures,id'],
			'academic_year_id' => ['required', 'exists:academic_years,id'],
			'amount' => ['required', 'numeric', 'min:0'],
			'discount_amount' => ['nullable', 'numeric', 'min:0'],
			'fine_amount' => ['nullable', 'numeric', 'min:0'],
			'payment_mode' => ['required', 'in:cash,cheque,card,online,bank_transfer'],
			'transaction_id' => ['nullable', 'string', 'max:100'],
			'payment_date' => ['required', 'date'],
			'remarks' => ['nullable', 'string', 'max:500'],
		]);

		try {
			DB::beginTransaction();

			// Check if already paid
			$exists = FeeCollection::where('student_id', $validated['student_id'])
				->where('fee_structure_id', $validated['fee_structure_id'])
				->where('academic_year_id', $validated['academic_year_id'])
				->exists();

			if ($exists) {
				return back()->with('error', 'Fee already collected for this student.')->withInput();
			}

			// Calculate paid amount
			$amount = $validated['amount'];
			$discount = $validated['discount_amount'] ?? 0;
			$fine = $validated['fine_amount'] ?? 0;
			$paidAmount = $amount - $discount + $fine;

			// Generate receipt number
			$receiptNo = 'REC-' . date('Ymd') . '-' . str_pad(FeeCollection::count() + 1, 5, '0', STR_PAD_LEFT);

			// Create fee collection
			$collection = FeeCollection::create([
				'student_id' => $validated['student_id'],
				'fee_structure_id' => $validated['fee_structure_id'],
				'academic_year_id' => $validated['academic_year_id'],
				'collected_by' => Auth::id(),
				'amount' => $amount,
				'discount_amount' => $discount,
				'fine_amount' => $fine,
				'paid_amount' => $paidAmount,
				'payment_mode' => $validated['payment_mode'],
				'transaction_id' => $validated['transaction_id'] ?? null,
				'payment_date' => $validated['payment_date'],
				'remarks' => $validated['remarks'] ?? null,
				'receipt_no' => $receiptNo,
			]);

			DB::commit();

			return redirect()->route('admin.fees.receipt', $collection->id)
				->with('success', 'Fee collected successfully.');

		} catch (\Exception $e) {
			DB::rollBack();
			return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
		}
	}

	public function receipt(FeeCollection $feeCollection)
	{
		$feeCollection->load([
			'student.schoolClass',
			'student.section',
			'feeStructure.feeType',
			'feeStructure.feeGroup',
			'academicYear',
			'collectedBy'
		]);

		return view('admin.fees.collection.receipt', compact('feeCollection'));
	}

	public function outstanding(Request $request)
	{
		$currentYear = AcademicYear::where('is_active', true)->first();

		if (!$currentYear) {
			return redirect()->route('admin.fees.collection')
				->with('error', 'No active academic year found.');
		}

		$query = Student::with(['schoolClass', 'section']);

		// Class filter
		if ($request->filled('class')) {
			$query->where('class_id', $request->class);
		}

		// Status filter
		$query->where('status', 'active');

		$students = $query->orderBy('first_name')->get();

		// Calculate outstanding fees for each student
		$outstandingData = [];
		foreach ($students as $student) {
			$totalFees = FeeStructure::where('academic_year_id', $currentYear->id)
				->where('class_id', $student->class_id)
				->active()
				->sum('amount');

			$paidFees = FeeCollection::where('student_id', $student->id)
				->where('academic_year_id', $currentYear->id)
				->sum('paid_amount');

			$outstanding = $totalFees - $paidFees;

			if ($outstanding > 0 || !$request->filled('show_only_outstanding')) {
				$outstandingData[] = [
					'student' => $student,
					'total_fees' => $totalFees,
					'paid_fees' => $paidFees,
					'outstanding' => $outstanding,
				];
			}
		}

		$classes = SchoolClass::active()->ordered()->get();

		return view('admin.fees.collection.outstanding', compact('outstandingData', 'classes', 'currentYear'));
	}
}
