@extends('layouts.app')

@section('title', 'Reconciliation Report')
@section('page-title', 'Reconciliation Report')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.fees.reconciliation.index') }}">Reconciliation</a></li>
    <li class="breadcrumb-item active">Report</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.fees.reconciliation.report') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">From Date</label>
                    <input type="date" name="from_date" class="form-control" value="{{ $fromDate }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">To Date</label>
                    <input type="date" name="to_date" class="form-control" value="{{ $toDate }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary me-2">
                        <i data-feather="filter" class="me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.fees.reconciliation.report') }}" class="btn btn-light">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Collections</h6>
                    <h3 class="mb-1">{{ number_format($summary['total_collections'], 2) }}</h3>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-primary" style="width: 100%;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Reconciled Amount</h6>
                    <h3 class="mb-1 text-success">{{ number_format($summary['reconciled_amount'], 2) }}</h3>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-success" style="width: {{ $summary['reconciliation_rate'] }}%;"></div>
                    </div>
                    <small class="text-muted">{{ $summary['reconciliation_rate'] }}% reconciled</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Pending Amount</h6>
                    <h3 class="mb-1 text-warning">{{ number_format($summary['pending_amount'], 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Disputed Amount</h6>
                    <h3 class="mb-1 text-danger">{{ number_format($summary['disputed_amount'], 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Bank vs Collection Comparison -->
    <div class="row mb-4">
        <div class="col-xl-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Bank Credits (Total)</h6>
                    <h3 class="mb-1">{{ number_format($summary['bank_credits'], 2) }}</h3>
                    <div class="row mt-3">
                        <div class="col-6">
                            <small class="text-muted">Matched</small>
                            <div class="fw-bold text-success">{{ number_format($summary['matched_credits'], 2) }}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Unmatched</small>
                            <div class="fw-bold text-warning">{{ number_format($summary['unmatched_credits'], 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 mb-3">
            <div class="card border-0 shadow-sm h-100 {{ $summary['difference'] != 0 ? 'border-warning' : 'border-success' }}">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Difference (Collections - Bank)</h6>
                    <h3 class="mb-1 {{ $summary['difference'] > 0 ? 'text-success' : ($summary['difference'] < 0 ? 'text-danger' : 'text-muted') }}">
                        {{ $summary['difference'] >= 0 ? '+' : '' }}{{ number_format($summary['difference'], 2) }}
                    </h3>
                    @if($summary['difference'] != 0)
                        <small class="text-muted">
                            @if($summary['difference'] > 0)
                                Collections exceed bank credits
                            @else
                                Bank credits exceed collections
                            @endif
                        </small>
                    @else
                        <small class="text-success">Fully reconciled</small>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Reconciled Transactions -->
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h6 class="mb-0">Recently Reconciled Transactions</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Receipt No</th>
                            <th>Student</th>
                            <th>Payment Date</th>
                            <th class="text-end">Amount</th>
                            <th>Bank Reference</th>
                            <th>Reconciled By</th>
                            <th>Reconciled At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reconciledCollections as $collection)
                            <tr>
                                <td><code>{{ $collection->receipt_no }}</code></td>
                                <td>{{ $collection->student->full_name ?? 'N/A' }}</td>
                                <td>{{ $collection->payment_date->format('d M Y') }}</td>
                                <td class="text-end fw-bold">{{ number_format($collection->paid_amount, 2) }}</td>
                                <td>
                                    @if($collection->bankStatement)
                                        <code>{{ $collection->bankStatement->reference_no ?: 'N/A' }}</code>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $collection->reconciledBy->name ?? 'N/A' }}</td>
                                <td>{{ $collection->reconciled_at ? $collection->reconciled_at->format('d M Y, H:i') : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No reconciled transactions</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($disputedCollections->count() > 0)
        <!-- Disputed Transactions -->
        <div class="card shadow-sm mb-4 border-danger">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0">Disputed Transactions</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Receipt No</th>
                                <th>Student</th>
                                <th>Payment Date</th>
                                <th class="text-end">Amount</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($disputedCollections as $collection)
                                <tr>
                                    <td><code>{{ $collection->receipt_no }}</code></td>
                                    <td>{{ $collection->student->full_name ?? 'N/A' }}</td>
                                    <td>{{ $collection->payment_date->format('d M Y') }}</td>
                                    <td class="text-end fw-bold text-danger">{{ number_format($collection->paid_amount, 2) }}</td>
                                    <td>{{ $collection->reconciliation_notes ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    @if($unmatchedBankEntries->count() > 0)
        <!-- Unmatched Bank Entries -->
        <div class="card shadow-sm mb-4 border-warning">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0">Unmatched Bank Entries</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Date</th>
                                <th>Reference</th>
                                <th>Description</th>
                                <th class="text-end">Credit</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($unmatchedBankEntries as $entry)
                                <tr>
                                    <td>{{ $entry->transaction_date->format('d M Y') }}</td>
                                    <td><code>{{ $entry->reference_no ?: '-' }}</code></td>
                                    <td>{{ Str::limit($entry->description, 40) }}</td>
                                    <td class="text-end fw-bold text-warning">{{ number_format($entry->credit_amount, 2) }}</td>
                                    <td>{{ $entry->notes ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
