<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankStatement;
use App\Models\FeeCollection;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ReconciliationController extends Controller
{
    /**
     * Display reconciliation dashboard.
     */
    public function index()
    {
        $activeYear = AcademicYear::getActive();

        // Statistics
        $stats = [
            'total_collections' => FeeCollection::when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))->count(),
            'pending_reconciliation' => FeeCollection::pendingReconciliation()->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))->count(),
            'reconciled' => FeeCollection::reconciled()->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))->count(),
            'disputed' => FeeCollection::disputed()->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))->count(),
            'total_bank_entries' => BankStatement::count(),
            'pending_bank_entries' => BankStatement::pending()->count(),
            'matched_bank_entries' => BankStatement::matched()->count(),
            'unmatched_bank_entries' => BankStatement::unmatched()->count(),
        ];

        // Calculate amounts
        $stats['pending_amount'] = FeeCollection::pendingReconciliation()
            ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
            ->sum('paid_amount');
        $stats['reconciled_amount'] = FeeCollection::reconciled()
            ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
            ->sum('paid_amount');

        // Recent unmatched bank entries
        $unmatchedEntries = BankStatement::pending()
            ->credits()
            ->orderBy('transaction_date', 'desc')
            ->limit(10)
            ->get();

        // Recent pending collections
        $pendingCollections = FeeCollection::with(['student', 'feeStructure.feeType'])
            ->pendingReconciliation()
            ->whereIn('payment_mode', ['online', 'bank_transfer', 'cheque'])
            ->orderBy('payment_date', 'desc')
            ->limit(10)
            ->get();

        // Import batches
        $recentBatches = BankStatement::select('import_batch', DB::raw('COUNT(*) as count'), DB::raw('MIN(created_at) as imported_at'))
            ->whereNotNull('import_batch')
            ->groupBy('import_batch')
            ->orderBy('imported_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.fees.reconciliation.index', compact('stats', 'unmatchedEntries', 'pendingCollections', 'recentBatches', 'activeYear'));
    }

    /**
     * Show bank statement import form.
     */
    public function import()
    {
        return view('admin.fees.reconciliation.import');
    }

    /**
     * Process bank statement import.
     */
    public function processImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
            'bank_name' => 'required|string|max:100',
            'account_number' => 'nullable|string|max:50',
            'date_format' => 'required|string',
        ]);

        $file = $request->file('file');
        $batchId = 'BATCH-' . now()->format('YmdHis') . '-' . Str::random(4);

        $handle = fopen($file->getPathname(), 'r');
        $header = fgetcsv($handle); // Skip header row

        $imported = 0;
        $skipped = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 4) {
                $skipped++;
                continue;
            }

            try {
                $dateValue = trim($row[0]);
                $transactionDate = Carbon::createFromFormat($request->date_format, $dateValue);
            } catch (\Exception $e) {
                $skipped++;
                continue;
            }

            $creditAmount = $this->parseAmount($row[3] ?? 0);
            $debitAmount = $this->parseAmount($row[4] ?? 0);

            // Skip if no credit amount (we're looking for incoming payments)
            if ($creditAmount <= 0 && $debitAmount <= 0) {
                $skipped++;
                continue;
            }

            BankStatement::create([
                'transaction_date' => $transactionDate,
                'reference_no' => trim($row[1] ?? ''),
                'description' => trim($row[2] ?? ''),
                'credit_amount' => $creditAmount,
                'debit_amount' => $debitAmount,
                'balance' => $this->parseAmount($row[5] ?? 0),
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'status' => 'pending',
                'import_batch' => $batchId,
                'imported_by' => auth()->id(),
            ]);

            $imported++;
        }

        fclose($handle);

        return redirect()->route('admin.fees.reconciliation.index')
            ->with('success', "Imported {$imported} transactions. Skipped {$skipped} invalid rows. Batch: {$batchId}");
    }

    /**
     * Parse amount from string.
     */
    private function parseAmount($value)
    {
        if (empty($value)) return 0;
        $value = str_replace([',', ' '], '', $value);
        return abs(floatval($value));
    }

    /**
     * Show matching interface.
     */
    public function match(Request $request)
    {
        $fromDate = $request->get('from_date', now()->subMonth()->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->format('Y-m-d'));

        // Bank entries awaiting match
        $bankEntries = BankStatement::pending()
            ->credits()
            ->whereBetween('transaction_date', [$fromDate, $toDate])
            ->orderBy('transaction_date', 'desc')
            ->paginate(20, ['*'], 'bank_page');

        // Collections awaiting reconciliation
        $collections = FeeCollection::with(['student', 'feeStructure.feeType'])
            ->pendingReconciliation()
            ->whereIn('payment_mode', ['online', 'bank_transfer', 'cheque'])
            ->whereBetween('payment_date', [$fromDate, $toDate])
            ->orderBy('payment_date', 'desc')
            ->paginate(20, ['*'], 'collection_page');

        return view('admin.fees.reconciliation.match', compact('bankEntries', 'collections', 'fromDate', 'toDate'));
    }

    /**
     * Auto-match transactions.
     */
    public function autoMatch(Request $request)
    {
        $fromDate = $request->get('from_date', now()->subMonth()->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->format('Y-m-d'));

        $matched = 0;

        // Get pending bank entries with credits
        $bankEntries = BankStatement::pending()
            ->credits()
            ->whereBetween('transaction_date', [$fromDate, $toDate])
            ->get();

        foreach ($bankEntries as $entry) {
            // Try to match by transaction ID/reference in description
            $collection = FeeCollection::pendingReconciliation()
                ->whereIn('payment_mode', ['online', 'bank_transfer'])
                ->where('paid_amount', $entry->credit_amount)
                ->where(function ($q) use ($entry) {
                    // Match by transaction ID
                    if ($entry->reference_no) {
                        $q->where('transaction_id', $entry->reference_no);
                    }
                    // Or match by receipt number in description
                    $q->orWhere(function ($q2) use ($entry) {
                        if ($entry->description) {
                            // Extract potential receipt numbers from description
                            preg_match('/RCP\d+/', $entry->description, $matches);
                            if (!empty($matches)) {
                                $q2->where('receipt_no', $matches[0]);
                            }
                        }
                    });
                })
                ->whereBetween('payment_date', [
                    Carbon::parse($entry->transaction_date)->subDays(3),
                    Carbon::parse($entry->transaction_date)->addDays(3)
                ])
                ->first();

            if ($collection) {
                $this->linkMatch($entry, $collection);
                $matched++;
            }
        }

        // Second pass: match by amount and date proximity
        $bankEntries = BankStatement::pending()
            ->credits()
            ->whereBetween('transaction_date', [$fromDate, $toDate])
            ->get();

        foreach ($bankEntries as $entry) {
            $collection = FeeCollection::pendingReconciliation()
                ->whereIn('payment_mode', ['online', 'bank_transfer', 'cheque'])
                ->where('paid_amount', $entry->credit_amount)
                ->whereBetween('payment_date', [
                    Carbon::parse($entry->transaction_date)->subDays(2),
                    Carbon::parse($entry->transaction_date)->addDays(2)
                ])
                ->first();

            if ($collection) {
                $this->linkMatch($entry, $collection);
                $matched++;
            }
        }

        return back()->with('success', "Auto-matched {$matched} transactions.");
    }

    /**
     * Manually match a bank entry with a collection.
     */
    public function manualMatch(Request $request)
    {
        $request->validate([
            'bank_statement_id' => 'required|exists:bank_statements,id',
            'fee_collection_id' => 'required|exists:fee_collections,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $bankEntry = BankStatement::findOrFail($request->bank_statement_id);
        $collection = FeeCollection::findOrFail($request->fee_collection_id);

        if ($bankEntry->status !== 'pending') {
            return back()->with('error', 'Bank entry is already matched.');
        }

        if ($collection->reconciliation_status !== 'pending') {
            return back()->with('error', 'Collection is already reconciled.');
        }

        $this->linkMatch($bankEntry, $collection, $request->notes);

        return back()->with('success', 'Transaction matched successfully.');
    }

    /**
     * Link a bank entry with a collection.
     */
    private function linkMatch(BankStatement $bankEntry, FeeCollection $collection, $notes = null)
    {
        DB::transaction(function () use ($bankEntry, $collection, $notes) {
            $bankEntry->update([
                'status' => 'matched',
                'fee_collection_id' => $collection->id,
                'matched_by' => auth()->id(),
                'matched_at' => now(),
                'notes' => $notes,
            ]);

            $collection->update([
                'reconciliation_status' => 'reconciled',
                'bank_statement_id' => $bankEntry->id,
                'reconciled_by' => auth()->id(),
                'reconciled_at' => now(),
                'reconciliation_notes' => $notes,
            ]);
        });
    }

    /**
     * Unmatch a reconciled transaction.
     */
    public function unmatch(Request $request)
    {
        $request->validate([
            'bank_statement_id' => 'required|exists:bank_statements,id',
        ]);

        $bankEntry = BankStatement::findOrFail($request->bank_statement_id);

        if ($bankEntry->status !== 'matched') {
            return back()->with('error', 'Transaction is not matched.');
        }

        DB::transaction(function () use ($bankEntry) {
            if ($bankEntry->fee_collection_id) {
                FeeCollection::where('id', $bankEntry->fee_collection_id)->update([
                    'reconciliation_status' => 'pending',
                    'bank_statement_id' => null,
                    'reconciled_by' => null,
                    'reconciled_at' => null,
                    'reconciliation_notes' => null,
                ]);
            }

            $bankEntry->update([
                'status' => 'pending',
                'fee_collection_id' => null,
                'matched_by' => null,
                'matched_at' => null,
                'notes' => null,
            ]);
        });

        return back()->with('success', 'Transaction unmatched successfully.');
    }

    /**
     * Mark bank entry as unmatched (no corresponding collection).
     */
    public function markUnmatched(Request $request)
    {
        $request->validate([
            'bank_statement_id' => 'required|exists:bank_statements,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $bankEntry = BankStatement::findOrFail($request->bank_statement_id);

        $bankEntry->update([
            'status' => 'unmatched',
            'notes' => $request->notes,
            'matched_by' => auth()->id(),
            'matched_at' => now(),
        ]);

        return back()->with('success', 'Bank entry marked as unmatched.');
    }

    /**
     * Mark bank entry as ignored.
     */
    public function ignore(Request $request)
    {
        $request->validate([
            'bank_statement_id' => 'required|exists:bank_statements,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $bankEntry = BankStatement::findOrFail($request->bank_statement_id);

        $bankEntry->update([
            'status' => 'ignored',
            'notes' => $request->notes ?? 'Ignored by user',
            'matched_by' => auth()->id(),
            'matched_at' => now(),
        ]);

        return back()->with('success', 'Bank entry ignored.');
    }

    /**
     * Mark collection as disputed.
     */
    public function dispute(Request $request)
    {
        $request->validate([
            'fee_collection_id' => 'required|exists:fee_collections,id',
            'notes' => 'required|string|max:500',
        ]);

        $collection = FeeCollection::findOrFail($request->fee_collection_id);

        $collection->update([
            'reconciliation_status' => 'disputed',
            'reconciliation_notes' => $request->notes,
        ]);

        return back()->with('success', 'Collection marked as disputed.');
    }

    /**
     * Reconciliation report.
     */
    public function report(Request $request)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->format('Y-m-d'));

        // Summary
        $summary = [
            'total_collections' => FeeCollection::whereBetween('payment_date', [$fromDate, $toDate])->sum('paid_amount'),
            'reconciled_amount' => FeeCollection::reconciled()->whereBetween('payment_date', [$fromDate, $toDate])->sum('paid_amount'),
            'pending_amount' => FeeCollection::pendingReconciliation()->whereBetween('payment_date', [$fromDate, $toDate])->sum('paid_amount'),
            'disputed_amount' => FeeCollection::disputed()->whereBetween('payment_date', [$fromDate, $toDate])->sum('paid_amount'),
            'bank_credits' => BankStatement::credits()->whereBetween('transaction_date', [$fromDate, $toDate])->sum('credit_amount'),
            'matched_credits' => BankStatement::matched()->credits()->whereBetween('transaction_date', [$fromDate, $toDate])->sum('credit_amount'),
            'unmatched_credits' => BankStatement::where('status', '!=', 'matched')->credits()->whereBetween('transaction_date', [$fromDate, $toDate])->sum('credit_amount'),
        ];

        $summary['difference'] = $summary['total_collections'] - $summary['bank_credits'];
        $summary['reconciliation_rate'] = $summary['total_collections'] > 0
            ? round(($summary['reconciled_amount'] / $summary['total_collections']) * 100, 1)
            : 0;

        // Detailed lists
        $reconciledCollections = FeeCollection::with(['student', 'bankStatement', 'reconciledBy'])
            ->reconciled()
            ->whereBetween('payment_date', [$fromDate, $toDate])
            ->orderBy('reconciled_at', 'desc')
            ->limit(50)
            ->get();

        $disputedCollections = FeeCollection::with(['student'])
            ->disputed()
            ->whereBetween('payment_date', [$fromDate, $toDate])
            ->get();

        $unmatchedBankEntries = BankStatement::where('status', 'unmatched')
            ->whereBetween('transaction_date', [$fromDate, $toDate])
            ->get();

        return view('admin.fees.reconciliation.report', compact(
            'summary', 'reconciledCollections', 'disputedCollections', 'unmatchedBankEntries', 'fromDate', 'toDate'
        ));
    }

    /**
     * Search collections for matching.
     */
    public function searchCollections(Request $request)
    {
        $query = FeeCollection::with(['student', 'feeStructure.feeType'])
            ->pendingReconciliation()
            ->whereIn('payment_mode', ['online', 'bank_transfer', 'cheque']);

        if ($request->filled('amount')) {
            $amount = floatval($request->amount);
            $query->whereBetween('paid_amount', [$amount - 0.01, $amount + 0.01]);
        }

        if ($request->filled('receipt_no')) {
            $query->where('receipt_no', 'like', '%' . $request->receipt_no . '%');
        }

        if ($request->filled('transaction_id')) {
            $query->where('transaction_id', 'like', '%' . $request->transaction_id . '%');
        }

        if ($request->filled('date')) {
            $query->whereDate('payment_date', $request->date);
        }

        $collections = $query->orderBy('payment_date', 'desc')->limit(20)->get();

        return response()->json($collections->map(function ($c) {
            return [
                'id' => $c->id,
                'receipt_no' => $c->receipt_no,
                'student_name' => $c->student->full_name ?? 'N/A',
                'amount' => number_format($c->paid_amount, 2),
                'payment_date' => $c->payment_date->format('d M Y'),
                'payment_mode' => ucfirst($c->payment_mode),
                'transaction_id' => $c->transaction_id,
            ];
        }));
    }
}
