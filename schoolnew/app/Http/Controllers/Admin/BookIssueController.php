<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookIssue;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookIssueController extends Controller
{
	public function index(Request $request)
	{
		$query = BookIssue::with(['book', 'student']);

		if ($request->filled('status')) {
			$query->where('status', $request->status);
		}

		$issues = $query->orderBy('issue_date', 'desc')->paginate(15);

		return view('admin.library.issue.index', compact('issues'));
	}

	public function create()
	{
		$books = Book::active()->available()->orderBy('title')->get();
		$students = Student::where('status', 'active')->orderBy('first_name')->get();

		return view('admin.library.issue.create', compact('books', 'students'));
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'book_id' => ['required', 'exists:books,id'],
			'student_id' => ['required', 'exists:students,id'],
			'issue_date' => ['required', 'date'],
			'due_date' => ['required', 'date', 'after:issue_date'],
		]);

		try {
			DB::beginTransaction();

			$book = Book::findOrFail($validated['book_id']);

			if ($book->available_copies <= 0) {
				return back()->with('error', 'Book not available.')->withInput();
			}

			BookIssue::create([
				'book_id' => $validated['book_id'],
				'student_id' => $validated['student_id'],
				'issued_by' => Auth::id(),
				'issue_date' => $validated['issue_date'],
				'due_date' => $validated['due_date'],
				'status' => BookIssue::STATUS_ISSUED,
			]);

			$book->decrement('available_copies');

			DB::commit();

			return redirect()->route('admin.library.issue.index')->with('success', 'Book issued successfully.');
		} catch (\Exception $e) {
			DB::rollBack();
			return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
		}
	}

	public function returnBook(Request $request, BookIssue $issue)
	{
		$validated = $request->validate([
			'return_date' => ['required', 'date'],
			'fine_amount' => ['nullable', 'numeric', 'min:0'],
		]);

		try {
			DB::beginTransaction();

			// Auto-calculate fine if not provided
			$fineAmount = $validated['fine_amount'] ?? null;
			if ($fineAmount === null) {
				// Temporarily set return date to calculate fine
				$issue->return_date = $validated['return_date'];
				$fineAmount = $issue->calculated_fine;
			}

			$issue->update([
				'return_date' => $validated['return_date'],
				'fine_amount' => $fineAmount,
				'status' => BookIssue::STATUS_RETURNED,
			]);

			$issue->book->increment('available_copies');

			DB::commit();

			$message = 'Book returned successfully.';
			if ($fineAmount > 0) {
				$message .= ' Fine amount: ₹' . number_format($fineAmount, 2);
			}

			return back()->with('success', $message);
		} catch (\Exception $e) {
			DB::rollBack();
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}

	/**
	 * Get calculated fine for AJAX request
	 */
	public function calculateFine(BookIssue $issue)
	{
		return response()->json([
			'overdue_days' => $issue->overdue_days,
			'calculated_fine' => $issue->calculated_fine,
			'is_overdue' => $issue->is_overdue,
		]);
	}
}
