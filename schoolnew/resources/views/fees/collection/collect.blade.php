@extends('layouts.app')

@section('title', 'Collect Fee - ' . $student->full_name)

@section('page-title', 'Collect Fee - ' . $student->full_name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.fees.collection') }}">Fee Collection</a></li>
    <li class="breadcrumb-item active">Collect Fee</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card mb-3">
            <div class="card-header">
                <h5>Student Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Name:</strong> {{ $student->full_name }}
                    </div>
                    <div class="col-md-3">
                        <strong>Roll No:</strong> {{ $student->roll_no }}
                    </div>
                    <div class="col-md-3">
                        <strong>Class:</strong> {{ $student->schoolClass->name }}
                    </div>
                    <div class="col-md-3">
                        <strong>Section:</strong> {{ $student->section->name }}
                    </div>
                </div>
            </div>
        </div>

        @if($unpaidFees->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h5>Collect Fees</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.fees.collection.store') }}" method="POST" id="fee-collection-form">
                        @csrf
                        <input type="hidden" name="student_id" value="{{ $student->id }}">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="payment_date" class="form-label">Payment Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="payment_date" name="payment_date" 
                                       value="{{ old('payment_date', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="payment_mode" class="form-label">Payment Mode <span class="text-danger">*</span></label>
                                <select class="form-select" id="payment_mode" name="payment_mode" required>
                                    <option value="">Select Payment Mode</option>
                                    <option value="cash" {{ old('payment_mode') == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="cheque" {{ old('payment_mode') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                    <option value="dd" {{ old('payment_mode') == 'dd' ? 'selected' : '' }}>Demand Draft</option>
                                    <option value="online" {{ old('payment_mode') == 'online' ? 'selected' : '' }}>Online Transfer</option>
                                    <option value="bank_transfer" {{ old('payment_mode') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="transaction_id" class="form-label">Transaction ID / Cheque No</label>
                                <input type="text" class="form-control" id="transaction_id" name="transaction_id" 
                                       value="{{ old('transaction_id') }}" placeholder="Optional">
                            </div>
                            <div class="col-md-6">
                                <label for="discount_amount" class="form-label">Discount Amount (₹)</label>
                                <input type="number" class="form-control" id="discount_amount" name="discount_amount" 
                                       value="{{ old('discount_amount', 0) }}" min="0" step="0.01">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="2" 
                                      placeholder="Optional remarks">{{ old('remarks') }}</textarea>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h6>Select Fees to Collect</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <input type="checkbox" id="select-all-fees" class="form-check-input">
                                                </th>
                                                <th>Fee Type</th>
                                                <th>Fee Group</th>
                                                <th>Amount (₹)</th>
                                                <th>Due Date</th>
                                                <th>Fine</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($unpaidFees as $fee)
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" name="fee_structure_ids[]" 
                                                               value="{{ $fee->id }}" class="form-check-input fee-checkbox" 
                                                               data-amount="{{ $fee->amount }}">
                                                    </td>
                                                    <td>{{ $fee->feeType->name }}</td>
                                                    <td>{{ $fee->feeGroup->name }}</td>
                                                    <td>₹{{ number_format($fee->amount, 2) }}</td>
                                                    <td>
                                                        @if($fee->due_date)
                                                            {{ $fee->due_date->format('d M Y') }}
                                                            @if($fee->due_date->isPast())
                                                                <span class="badge badge-light-danger">Overdue</span>
                                                            @endif
                                                        @else
                                                            <span class="text-muted">No due date</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($fee->fine_type !== 'none')
                                                            @if($fee->fine_type === 'percentage')
                                                                {{ $fee->fine_amount }}%
                                                            @else
                                                                ₹{{ number_format($fee->fine_amount, 2) }}
                                                            @endif
                                                        @else
                                                            <span class="text-muted">No fine</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-3 p-3 bg-light rounded">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Selected Fees: <span id="selected-count">0</span></strong>
                                        </div>
                                        <div class="col-md-6 text-end">
                                            <strong>Total Amount: ₹<span id="total-amount">0.00</span></strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-3">
                            <a href="{{ route('admin.fees.collection') }}" class="btn btn-light">Back</a>
                            <button type="submit" class="btn btn-success" id="collect-btn" disabled>
                                <i data-feather="credit-card" class="icon-xs"></i> Collect Fee
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="text-success">All fees are paid!</h5>
                    <p>This student has no pending fees for the current academic year.</p>
                    <a href="{{ route('admin.fees.collection') }}" class="btn btn-primary">Back to Collection</a>
                </div>
            </div>
        @endif

        @if($paymentHistory->count() > 0)
            <div class="card mt-3">
                <div class="card-header">
                    <h5>Payment History</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Receipt No</th>
                                    <th>Fee Type</th>
                                    <th>Amount</th>
                                    <th>Payment Date</th>
                                    <th>Payment Mode</th>
                                    <th>Collected By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paymentHistory as $payment)
                                    <tr>
                                        <td>{{ $payment->receipt_no }}</td>
                                        <td>{{ $payment->feeStructure->feeType->name ?? 'N/A' }}</td>
                                        <td>
                                            @if($payment->paid_amount < 0)
                                                <span class="text-danger">-₹{{ number_format(abs($payment->paid_amount), 2) }}</span>
                                                <span class="badge badge-light-danger">Refund</span>
                                            @else
                                                ₹{{ number_format($payment->paid_amount, 2) }}
                                            @endif
                                        </td>
                                        <td>{{ $payment->payment_date->format('d M Y') }}</td>
                                        <td>{{ ucfirst($payment->payment_mode) }}</td>
                                        <td>{{ $payment->collectedBy->name ?? 'N/A' }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('admin.fees.receipt', $payment) }}" class="btn btn-outline-primary btn-sm" title="Receipt">
                                                    <i data-feather="printer" class="icon-xs"></i>
                                                </a>
                                                @if($payment->paid_amount > 0)
                                                    <button type="button" class="btn btn-outline-warning btn-sm refund-btn" title="Refund" data-id="{{ $payment->id }}" data-amount="{{ $payment->paid_amount }}" data-receipt="{{ $payment->receipt_no }}" data-fee="{{ $payment->feeStructure->feeType->name ?? 'N/A' }}">
                                                        <i data-feather="corner-down-left" class="icon-xs"></i>
                                                    </button>
                                                @endif
                                                <form action="{{ route('admin.fees.collection.destroy', $payment) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-outline-danger btn-sm collection-delete-btn" title="Delete" data-receipt="{{ $payment->receipt_no }}" data-amount="{{ number_format($payment->paid_amount, 2) }}">
                                                        <i data-feather="trash-2" class="icon-xs"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

<!-- Refund Modal -->
<div class="modal fade" id="refundModal" tabindex="-1" aria-labelledby="refundModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="refundForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="refundModalLabel">Process Refund</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <p><strong>Receipt:</strong> <span id="refund-receipt"></span></p>
                        <p><strong>Fee Type:</strong> <span id="refund-fee"></span></p>
                        <p><strong>Paid Amount:</strong> ₹<span id="refund-paid"></span></p>
                    </div>
                    <div class="mb-3">
                        <label for="refund_amount" class="form-label">Refund Amount (₹) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="refund_amount" name="refund_amount" step="0.01" min="0.01" required>
                        <small class="text-muted">Maximum: ₹<span id="refund-max"></span></small>
                    </div>
                    <div class="mb-3">
                        <label for="refund_mode" class="form-label">Refund Mode <span class="text-danger">*</span></label>
                        <select class="form-select" id="refund_mode" name="refund_mode" required>
                            <option value="">Select Mode</option>
                            <option value="cash">Cash</option>
                            <option value="cheque">Cheque</option>
                            <option value="online">Online Transfer</option>
                            <option value="bank_transfer">Bank Transfer</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="refund_reason" class="form-label">Reason for Refund <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="refund_reason" name="refund_reason" rows="3" required placeholder="Enter reason for refund..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Process Refund</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
	jQuery(document).ready(function() {
		// Calculate total when checkboxes change
		jQuery('.fee-checkbox').on('change', function() {
			calculateTotal();
		});

		// Select all checkbox
		jQuery('#select-all-fees').on('change', function() {
			jQuery('.fee-checkbox').prop('checked', this.checked);
			calculateTotal();
		});

		function calculateTotal() {
			var total = 0;
			var count = 0;

			jQuery('.fee-checkbox:checked').each(function() {
				total += parseFloat(jQuery(this).data('amount'));
				count++;
			});

			jQuery('#selected-count').text(count);
			jQuery('#total-amount').text(total.toFixed(2));
			jQuery('#collect-btn').prop('disabled', count === 0);
		}

		// Form validation
		jQuery('#fee-collection-form').on('submit', function(e) {
			if (jQuery('.fee-checkbox:checked').length === 0) {
				e.preventDefault();
				Swal.fire({
					title: 'Error',
					text: 'Please select at least one fee to collect.',
					icon: 'error'
				});
				return false;
			}
		});

		// Initial calculation
		calculateTotal();

		// Delete collection handler
		jQuery('.collection-delete-btn').on('click', function() {
			var button = jQuery(this);
			var form = button.closest('form');
			var receipt = button.data('receipt');
			var amount = button.data('amount');

			Swal.fire({
				title: 'Delete Fee Collection?',
				html: 'You are about to delete payment <strong>' + receipt + '</strong> of ₹' + amount + '.<br><br>This will mark the fee as unpaid again.',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#d33',
				cancelButtonColor: '#6c757d',
				confirmButtonText: 'Yes, delete it!',
				cancelButtonText: 'Cancel'
			}).then(function(result) {
				if (result.isConfirmed) {
					form.submit();
				}
			});
		});

		// Refund button handler
		jQuery('.refund-btn').on('click', function() {
			var id = jQuery(this).data('id');
			var amount = jQuery(this).data('amount');
			var receipt = jQuery(this).data('receipt');
			var fee = jQuery(this).data('fee');

			jQuery('#refund-receipt').text(receipt);
			jQuery('#refund-fee').text(fee);
			jQuery('#refund-paid').text(parseFloat(amount).toFixed(2));
			jQuery('#refund-max').text(parseFloat(amount).toFixed(2));
			jQuery('#refund_amount').attr('max', amount).val(amount);
			jQuery('#refundForm').attr('action', '{{ url("admin/fees/collection") }}/' + id + '/refund');

			var modal = new bootstrap.Modal(document.getElementById('refundModal'));
			modal.show();
		});
	});
</script>
@endpush