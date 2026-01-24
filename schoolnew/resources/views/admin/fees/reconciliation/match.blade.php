@extends('layouts.app')

@section('title', 'Match Transactions')
@section('page-title', 'Match Transactions')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.fees.reconciliation.index') }}">Reconciliation</a></li>
    <li class="breadcrumb-item active">Match</li>
@endsection

@push('css')
<style>
    .match-card {
        cursor: pointer;
        transition: all 0.2s ease;
        border: 2px solid transparent;
    }
    .match-card:hover {
        border-color: #0d6efd;
        background-color: #f8f9fa;
    }
    .match-card.selected {
        border-color: #198754;
        background-color: #d1e7dd;
    }
    .amount-match {
        background-color: #d4edda;
        padding: 2px 6px;
        border-radius: 4px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Filters & Actions -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.fees.reconciliation.match') }}" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">From Date</label>
                    <input type="date" name="from_date" class="form-control" value="{{ $fromDate }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">To Date</label>
                    <input type="date" name="to_date" class="form-control" value="{{ $toDate }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary me-2">
                        <i data-feather="filter" class="me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.fees.reconciliation.match') }}" class="btn btn-light">Reset</a>
                </div>
                <div class="col-md-4 text-end">
                    <form action="{{ route('admin.fees.reconciliation.auto-match') }}" method="POST" class="d-inline" id="autoMatchForm">
                        @csrf
                        <input type="hidden" name="from_date" value="{{ $fromDate }}">
                        <input type="hidden" name="to_date" value="{{ $toDate }}">
                        <button type="submit" class="btn btn-success">
                            <i data-feather="zap" class="me-1"></i> Auto Match
                        </button>
                    </form>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- Bank Entries -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">Bank Statement Entries ({{ $bankEntries->total() }})</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light sticky-top">
                                <tr>
                                    <th style="width: 30px;"></th>
                                    <th>Date</th>
                                    <th>Reference</th>
                                    <th>Description</th>
                                    <th class="text-end">Credit</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bankEntries as $entry)
                                    <tr class="match-card bank-entry" data-id="{{ $entry->id }}" data-amount="{{ $entry->credit_amount }}">
                                        <td>
                                            <input type="radio" name="bank_entry" value="{{ $entry->id }}" class="form-check-input bank-radio">
                                        </td>
                                        <td>{{ $entry->transaction_date->format('d M') }}</td>
                                        <td><code class="small">{{ $entry->reference_no ?: '-' }}</code></td>
                                        <td>
                                            <span title="{{ $entry->description }}">{{ Str::limit($entry->description, 25) }}</span>
                                        </td>
                                        <td class="text-end fw-bold text-success">{{ number_format($entry->credit_amount, 2) }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i data-feather="more-vertical" style="width: 14px; height: 14px;"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <form action="{{ route('admin.fees.reconciliation.mark-unmatched') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="bank_statement_id" value="{{ $entry->id }}">
                                                            <button type="submit" class="dropdown-item text-warning">
                                                                <i data-feather="x-circle" class="me-2" style="width: 14px; height: 14px;"></i> Mark Unmatched
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('admin.fees.reconciliation.ignore') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="bank_statement_id" value="{{ $entry->id }}">
                                                            <button type="submit" class="dropdown-item text-secondary">
                                                                <i data-feather="eye-off" class="me-2" style="width: 14px; height: 14px;"></i> Ignore
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No pending bank entries</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($bankEntries->hasPages())
                    <div class="card-footer">
                        {{ $bankEntries->appends(request()->except('bank_page'))->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Collections -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">Fee Collections ({{ $collections->total() }})</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light sticky-top">
                                <tr>
                                    <th style="width: 30px;"></th>
                                    <th>Receipt</th>
                                    <th>Student</th>
                                    <th>Mode</th>
                                    <th class="text-end">Amount</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($collections as $collection)
                                    <tr class="match-card collection-entry" data-id="{{ $collection->id }}" data-amount="{{ $collection->paid_amount }}">
                                        <td>
                                            <input type="radio" name="collection" value="{{ $collection->id }}" class="form-check-input collection-radio">
                                        </td>
                                        <td><code class="small">{{ $collection->receipt_no }}</code></td>
                                        <td>{{ Str::limit($collection->student->full_name ?? 'N/A', 15) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $collection->payment_mode == 'online' ? 'info' : ($collection->payment_mode == 'bank_transfer' ? 'primary' : 'secondary') }}">
                                                {{ ucfirst(str_replace('_', ' ', $collection->payment_mode)) }}
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold">{{ number_format($collection->paid_amount, 2) }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i data-feather="more-vertical" style="width: 14px; height: 14px;"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a href="{{ route('admin.fees.receipt', $collection) }}" class="dropdown-item" target="_blank">
                                                            <i data-feather="file-text" class="me-2" style="width: 14px; height: 14px;"></i> View Receipt
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <button type="button" class="dropdown-item text-danger dispute-btn" data-id="{{ $collection->id }}">
                                                            <i data-feather="flag" class="me-2" style="width: 14px; height: 14px;"></i> Mark Disputed
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No pending collections</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($collections->hasPages())
                    <div class="card-footer">
                        {{ $collections->appends(request()->except('collection_page'))->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Match Button (Fixed at bottom) -->
    <div class="position-fixed bottom-0 start-50 translate-middle-x mb-4" style="z-index: 1050;">
        <form action="{{ route('admin.fees.reconciliation.manual-match') }}" method="POST" id="manualMatchForm">
            @csrf
            <input type="hidden" name="bank_statement_id" id="selectedBankId">
            <input type="hidden" name="fee_collection_id" id="selectedCollectionId">
            <button type="submit" class="btn btn-lg btn-primary shadow-lg" id="matchBtn" disabled>
                <i data-feather="git-merge" class="me-2"></i> Match Selected Transactions
            </button>
        </form>
    </div>
</div>

<!-- Dispute Modal -->
<div class="modal fade" id="disputeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.fees.reconciliation.dispute') }}" method="POST">
                @csrf
                <input type="hidden" name="fee_collection_id" id="disputeCollectionId">
                <div class="modal-header">
                    <h5 class="modal-title">Mark as Disputed</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Reason for Dispute <span class="text-danger">*</span></label>
                        <textarea name="notes" class="form-control" rows="3" required placeholder="Enter reason..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Mark Disputed</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
jQuery(document).ready(function() {
    let selectedBank = null;
    let selectedCollection = null;

    // Bank entry selection
    jQuery('.bank-entry').on('click', function() {
        jQuery('.bank-entry').removeClass('selected');
        jQuery(this).addClass('selected');
        jQuery(this).find('.bank-radio').prop('checked', true);
        selectedBank = jQuery(this).data('id');
        jQuery('#selectedBankId').val(selectedBank);
        checkMatch();
        highlightMatchingAmounts(jQuery(this).data('amount'));
    });

    // Collection selection
    jQuery('.collection-entry').on('click', function() {
        jQuery('.collection-entry').removeClass('selected');
        jQuery(this).addClass('selected');
        jQuery(this).find('.collection-radio').prop('checked', true);
        selectedCollection = jQuery(this).data('id');
        jQuery('#selectedCollectionId').val(selectedCollection);
        checkMatch();
    });

    function checkMatch() {
        if (selectedBank && selectedCollection) {
            jQuery('#matchBtn').prop('disabled', false);
        } else {
            jQuery('#matchBtn').prop('disabled', true);
        }
    }

    function highlightMatchingAmounts(amount) {
        jQuery('.collection-entry').each(function() {
            const collectionAmount = parseFloat(jQuery(this).data('amount'));
            if (Math.abs(collectionAmount - amount) < 0.01) {
                jQuery(this).find('td:nth-child(5)').addClass('amount-match');
            } else {
                jQuery(this).find('td:nth-child(5)').removeClass('amount-match');
            }
        });
    }

    // Dispute button
    jQuery('.dispute-btn').on('click', function() {
        const collectionId = jQuery(this).data('id');
        jQuery('#disputeCollectionId').val(collectionId);
        new bootstrap.Modal(document.getElementById('disputeModal')).show();
    });

    // Form validation
    jQuery('#manualMatchForm').on('submit', function(e) {
        if (!selectedBank || !selectedCollection) {
            e.preventDefault();
            alert('Please select both a bank entry and a collection.');
        }
    });
});
</script>
@endpush
