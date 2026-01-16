@extends('layouts.admin')

@section('title', 'Transaction Reconciliation')
@section('page-title', 'Transaction Reconciliation')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.fees.reports.index') }}">Fee Reports</a></li>
    <li class="breadcrumb-item active">Reconciliation</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Action Buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.fees.reconciliation.import') }}" class="btn btn-primary">
                    <i data-feather="upload" class="me-1"></i> Import Bank Statement
                </a>
                <a href="{{ route('admin.fees.reconciliation.match') }}" class="btn btn-success">
                    <i data-feather="git-merge" class="me-1"></i> Match Transactions
                </a>
                <a href="{{ route('admin.fees.reconciliation.report') }}" class="btn btn-info">
                    <i data-feather="file-text" class="me-1"></i> Reconciliation Report
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Total Collections</p>
                            <h4 class="mb-0">{{ number_format($stats['total_collections']) }}</h4>
                        </div>
                        <div class="bg-primary-subtle rounded-circle p-3">
                            <i data-feather="dollar-sign" class="text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Pending Reconciliation</p>
                            <h4 class="mb-0 text-warning">{{ number_format($stats['pending_reconciliation']) }}</h4>
                            <small class="text-muted">{{ number_format($stats['pending_amount'], 2) }}</small>
                        </div>
                        <div class="bg-warning-subtle rounded-circle p-3">
                            <i data-feather="clock" class="text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Reconciled</p>
                            <h4 class="mb-0 text-success">{{ number_format($stats['reconciled']) }}</h4>
                            <small class="text-muted">{{ number_format($stats['reconciled_amount'], 2) }}</small>
                        </div>
                        <div class="bg-success-subtle rounded-circle p-3">
                            <i data-feather="check-circle" class="text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Disputed</p>
                            <h4 class="mb-0 text-danger">{{ number_format($stats['disputed']) }}</h4>
                        </div>
                        <div class="bg-danger-subtle rounded-circle p-3">
                            <i data-feather="alert-triangle" class="text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bank Statement Stats -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card bg-light border-0 h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Bank Entries</h6>
                    <h3 class="mb-0">{{ number_format($stats['total_bank_entries']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card bg-warning bg-opacity-10 border-0 h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Pending Match</h6>
                    <h3 class="mb-0 text-warning">{{ number_format($stats['pending_bank_entries']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card bg-success bg-opacity-10 border-0 h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Matched</h6>
                    <h3 class="mb-0 text-success">{{ number_format($stats['matched_bank_entries']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card bg-secondary bg-opacity-10 border-0 h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Unmatched</h6>
                    <h3 class="mb-0 text-secondary">{{ number_format($stats['unmatched_bank_entries']) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Pending Bank Entries -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Pending Bank Entries</h6>
                    <a href="{{ route('admin.fees.reconciliation.match') }}" class="btn btn-sm btn-outline-primary">View All</a>
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
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($unmatchedEntries as $entry)
                                    <tr>
                                        <td>{{ $entry->transaction_date->format('d M') }}</td>
                                        <td><code>{{ $entry->reference_no ?: '-' }}</code></td>
                                        <td>{{ Str::limit($entry->description, 30) }}</td>
                                        <td class="text-end fw-bold text-success">{{ number_format($entry->credit_amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">No pending bank entries</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Collections -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Pending Collections (Online/Transfer)</h6>
                    <a href="{{ route('admin.fees.reconciliation.match') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Receipt</th>
                                    <th>Student</th>
                                    <th>Mode</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingCollections as $collection)
                                    <tr>
                                        <td><code>{{ $collection->receipt_no }}</code></td>
                                        <td>{{ $collection->student->full_name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ ucfirst($collection->payment_mode) }}</span>
                                        </td>
                                        <td class="text-end fw-bold">{{ number_format($collection->paid_amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">No pending collections</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Import Batches -->
    @if($recentBatches->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h6 class="mb-0">Recent Import Batches</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Batch ID</th>
                                        <th class="text-center">Entries</th>
                                        <th>Imported At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentBatches as $batch)
                                        <tr>
                                            <td><code>{{ $batch->import_batch }}</code></td>
                                            <td class="text-center">{{ $batch->count }}</td>
                                            <td>{{ \Carbon\Carbon::parse($batch->imported_at)->format('d M Y, h:i A') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
