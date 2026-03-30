@extends('layouts.app')

@section('title', 'Collect Fee - ' . $student->full_name)

@section('page-title', 'Collect Fee')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.fees.collection') }}">Payments</a></li>
	<li class="breadcrumb-item active">{{ $student->full_name }}</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12">
		@if(session('error'))
			<div class="alert alert-danger alert-dismissible fade show py-2 px-3" role="alert" style="font-size: 13px; border-radius: 8px;">
				<i class="icon-alert me-1"></i> {{ session('error') }}
				<button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" style="padding: 10px;"></button>
			</div>
		@endif
		@if(session('success'))
			<div class="alert alert-success alert-dismissible fade show py-2 px-3" role="alert" style="font-size: 13px; border-radius: 8px;">
				<i class="icon-check me-1"></i> {{ session('success') }}
				<button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" style="padding: 10px;"></button>
			</div>
		@endif

		<!-- Student Info -->
		<div class="card mb-3">
			<div class="card-body">
				<div class="row align-items-center">
					<div class="col-md-1 text-center">
						@if($student->photo)
							<img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->full_name }}" class="rounded-circle" style="width: 60px; height: 60px; object-fit: cover;">
						@else
							<div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 1.4rem;">
								{{ strtoupper(substr($student->first_name, 0, 1)) }}
							</div>
						@endif
					</div>
					<div class="col-md-5">
						<h5 class="mb-1">{{ $student->full_name }}</h5>
						<p class="text-muted mb-0">{{ $student->admission_no }} &bull; {{ $student->schoolClass->name }} {{ $student->section ? '(' . $student->section->name . ')' : '' }} &bull; {{ $currentYear->name }}</p>
					</div>
					<div class="col-md-6 text-md-end mt-2 mt-md-0">
						@php
							$totalFees = $feeStructures->sum('amount');
							$totalPaid = $feeStructures->whereIn('id', $paidFees)->sum('amount');
							$totalPending = $totalFees - $totalPaid;
						@endphp
						<div class="d-inline-flex gap-4">
							<div>
								<small class="text-muted d-block">Total Fees</small>
								<strong class="fs-5">₹{{ number_format($totalFees, 2) }}</strong>
							</div>
							<div>
								<small class="text-success d-block">Paid</small>
								<strong class="fs-5 text-success">₹{{ number_format($totalPaid, 2) }}</strong>
							</div>
							<div>
								<small class="text-danger d-block">Pending</small>
								<strong class="fs-5 text-danger">₹{{ number_format($totalPending, 2) }}</strong>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<!-- Fee Selection (left) -->
			<div class="col-md-8">
				<div class="card">
					<div class="card-header">
						<h5>Select Fees to Collect</h5>
					</div>
					<div class="card-body">
						<div class="table-responsive">
							<table class="table table-hover mb-0">
								<thead class="bg-light">
									<tr>
										<th style="width: 40px;">
											<input type="checkbox" class="form-check-input" id="selectAllFees" title="Select All Pending" autocomplete="off">
										</th>
										<th>Fee Type</th>
										<th>Amount</th>
										<th>Due Date</th>
										<th>Late Fee</th>
										<th>Total</th>
										<th>Status</th>
									</tr>
								</thead>
								<tbody>
									@php $today = now()->startOfDay(); $pendingCount = 0; @endphp
									@foreach($feeStructures as $structure)
										@php
											$isPaid = in_array($structure->id, $paidFees);
											$isOverdue = $structure->due_date && $structure->due_date < $today && !$isPaid;
											$fineAmount = 0;
											if ($isOverdue) {
												$fineAmount = $structure->fine_type === 'percentage'
													? ($structure->amount * $structure->fine_amount) / 100
													: ($structure->fine_amount ?? 0);
											}
											$totalAmount = $structure->amount + $fineAmount;
											if (!$isPaid) $pendingCount++;
										@endphp
										<tr class="{{ $isOverdue ? 'table-warning' : '' }} {{ $isPaid ? 'opacity-50' : '' }}">
											<td>
												@if(!$isPaid)
													<input type="checkbox" class="form-check-input fee-checkbox" value="{{ $structure->id }}" data-amount="{{ $structure->amount }}" data-fine="{{ $fineAmount }}" data-name="{{ $structure->feeType->name }}" autocomplete="off">
												@else
													<i class="icon-check text-success"></i>
												@endif
											</td>
											<td>
												<strong>{{ $structure->feeType->name }}</strong>
												@if($structure->feeGroup)<br><small class="text-muted">{{ $structure->feeGroup->name }}</small>@endif
											</td>
											<td>₹{{ number_format($structure->amount, 2) }}</td>
											<td>
												{{ $structure->due_date ? $structure->due_date->format('d M Y') : '-' }}
												@if($isOverdue)
													<br><span class="badge badge-light-danger" style="font-size: 10px;">Overdue</span>
												@endif
											</td>
											<td class="{{ $fineAmount > 0 ? 'text-danger fw-bold' : '' }}">
												₹{{ number_format($fineAmount, 2) }}
											</td>
											<td class="fw-bold">₹{{ number_format($totalAmount, 2) }}</td>
											<td>
												@if($isPaid)
													<span class="badge badge-light-success px-3 py-2">Paid</span>
												@else
													<span class="badge badge-light-warning px-3 py-2">Pending</span>
												@endif
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>

						@if($pendingCount === 0)
							<div class="alert alert-success mt-3 mb-0">
								<i class="icon-check me-2"></i> All fees have been collected for this student. No pending fees.
							</div>
						@endif
					</div>
				</div>

				<!-- Payment History -->
				@php
					$history = \App\Models\FeeCollection::with(['feeStructure.feeType', 'collectedBy'])
						->where('student_id', $student->id)
						->where('academic_year_id', $currentYear->id)
						->latest('payment_date')
						->get();
				@endphp
				@if($history->count() > 0)
				<div class="card">
					<div class="card-header">
						<h5>Payment History ({{ $currentYear->name }})</h5>
					</div>
					<div class="card-body p-0">
						<div class="table-responsive">
							<table class="table table-sm mb-0">
								<thead class="bg-light">
									<tr>
										<th>Receipt #</th>
										<th>Fee Type</th>
										<th>Paid Amount</th>
										<th>Mode</th>
										<th>Date</th>
										<th>Collected By</th>
									</tr>
								</thead>
								<tbody>
									@foreach($history as $record)
									<tr>
										<td>
											<a href="{{ route('admin.fees.receipt', $record->id) }}" class="text-primary">{{ $record->receipt_no }}</a>
										</td>
										<td>{{ $record->feeStructure->feeType->name ?? '-' }}</td>
										<td class="fw-bold">₹{{ number_format($record->paid_amount, 2) }}</td>
										<td><span class="badge badge-light-info">{{ ucfirst(str_replace('_', ' ', $record->payment_mode)) }}</span></td>
										<td>{{ \Carbon\Carbon::parse($record->payment_date)->format('d M Y') }}</td>
										<td>{{ $record->collectedBy->name ?? 'System' }}</td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				</div>
				@endif
			</div>

			<!-- Collection Form (right) -->
			<div class="col-md-4">
				<div class="card sticky-top" style="top: 80px;">
					<div class="card-header bg-primary">
						<h5 class="text-white mb-0"><i class="icon-money me-2"></i>Collect Payment</h5>
					</div>
					<div class="card-body" id="collectFormContainer">
						<div id="noSelectionMsg" class="text-center py-4">
							<div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 56px; height: 56px;">
								<i class="icon-hand-pointing-right" style="font-size: 24px; color: #95a5a6;"></i>
							</div>
							<p class="text-muted mb-0">Select fee(s) from the table to collect payment manually.</p>
						</div>

						<form action="{{ route('admin.fees.collection.store') }}" method="POST" id="collectForm" class="d-none">
							@csrf
							<input type="hidden" name="student_id" value="{{ $student->id }}">
							<input type="hidden" name="academic_year_id" value="{{ $currentYear->id }}">
							<input type="hidden" name="fee_structure_id" id="selectedFeeId" value="">

							<!-- Selected fees summary -->
							<div class="mb-3">
								<label class="form-label fw-bold">Selected Fee(s)</label>
								<div id="selectedFeesList" class="border rounded p-2" style="max-height: 120px; overflow-y: auto; font-size: 13px;"></div>
							</div>

							<div class="row">
								<div class="col-6 mb-3">
									<label class="form-label">Amount <span class="text-danger">*</span></label>
									<input type="number" name="amount" id="feeAmount" class="form-control" step="0.01" min="0" readonly>
								</div>
								<div class="col-6 mb-3">
									<label class="form-label">Late Fee</label>
									<input type="number" name="fine_amount" id="feeFinAmount" class="form-control" step="0.01" min="0" value="0">
								</div>
							</div>

							<div class="row">
								<div class="col-6 mb-3">
									<label class="form-label">Discount</label>
									<input type="number" name="discount_amount" id="feeDiscount" class="form-control" step="0.01" min="0" value="0">
								</div>
								<div class="col-6 mb-3">
									<label class="form-label fw-bold">Total Payable</label>
									<input type="text" id="feeTotalPayable" class="form-control bg-light fw-bold" readonly>
								</div>
							</div>

							<div class="mb-3">
								<label class="form-label">Payment Mode <span class="text-danger">*</span></label>
								<select name="payment_mode" id="paymentMode" class="form-select" required>
									<option value="cash">Cash</option>
									<option value="cheque">Cheque</option>
									<option value="card">Card</option>
									<option value="bank_transfer">Bank Transfer</option>
									<option value="online">Online / UPI</option>
								</select>
							</div>

							<div class="mb-3 d-none" id="transactionIdGroup">
								<label class="form-label">Transaction / Reference ID</label>
								<input type="text" name="transaction_id" class="form-control" placeholder="Cheque no. / Transaction ID">
							</div>

							<div class="mb-3">
								<label class="form-label">Payment Date <span class="text-danger">*</span></label>
								<input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
							</div>

							<div class="mb-3">
								<label class="form-label">Remarks</label>
								<textarea name="remarks" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
							</div>

							<button type="submit" class="btn btn-success w-100" id="collectBtn">
								<i class="icon-check me-1"></i> Collect Fee — <span id="collectBtnAmount">₹0</span>
							</button>
						</form>
					</div>
				</div>

				<div class="mt-3 d-flex gap-2">
					<a href="{{ route('admin.fees.collection') }}" class="btn btn-outline-secondary flex-fill">
						<i class="icon-arrow-left me-1"></i> Back
					</a>
					<a href="{{ route('admin.fees.outstanding') }}" class="btn btn-outline-warning flex-fill">
						<i class="icon-info-alt me-1"></i> Outstanding
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
	var selectedFees = {};

	function updateCollectForm() {
		var ids = Object.keys(selectedFees);
		var totalAmount = 0;
		var totalFine = 0;
		var listHtml = '';

		ids.forEach(function(id) {
			var fee = selectedFees[id];
			totalAmount += fee.amount;
			totalFine += fee.fine;
			listHtml += '<div class="d-flex justify-content-between mb-1"><span>' + fee.name + '</span><span class="fw-bold">₹' + fee.amount.toFixed(2) + '</span></div>';
		});

		if (ids.length === 0) {
			jQuery('#collectForm').addClass('d-none');
			jQuery('#noSelectionMsg').removeClass('d-none');
			return;
		}

		jQuery('#collectForm').removeClass('d-none');
		jQuery('#noSelectionMsg').addClass('d-none');
		jQuery('#selectedFeesList').html(listHtml);
		jQuery('#feeAmount').val(totalAmount.toFixed(2));
		jQuery('#feeFinAmount').val(totalFine.toFixed(2));
		jQuery('#feeDiscount').val('0');

		// For single fee, set the fee_structure_id directly
		if (ids.length === 1) {
			jQuery('#selectedFeeId').val(ids[0]);
		} else {
			jQuery('#selectedFeeId').val(ids[0]);
		}

		calculateTotal();
	}

	function calculateTotal() {
		var amount = parseFloat(jQuery('#feeAmount').val()) || 0;
		var fine = parseFloat(jQuery('#feeFinAmount').val()) || 0;
		var discount = parseFloat(jQuery('#feeDiscount').val()) || 0;
		var total = amount + fine - discount;
		jQuery('#feeTotalPayable').val('₹' + total.toFixed(2));
		jQuery('#collectBtnAmount').text('₹' + total.toFixed(2));
	}

	// Select All pending fees
	jQuery('#selectAllFees').on('change', function() {
		var checked = jQuery(this).is(':checked');
		jQuery('.fee-checkbox').prop('checked', checked);
		selectedFees = {};
		if (checked) {
			jQuery('.fee-checkbox').each(function() {
				var id = jQuery(this).val();
				selectedFees[id] = {
					amount: parseFloat(jQuery(this).data('amount')),
					fine: parseFloat(jQuery(this).data('fine')),
					name: jQuery(this).data('name')
				};
			});
		}
		updateCollectForm();
	});

	// Individual fee checkbox
	jQuery(document).on('change', '.fee-checkbox', function() {
		var id = jQuery(this).val();
		if (jQuery(this).is(':checked')) {
			selectedFees[id] = {
				amount: parseFloat(jQuery(this).data('amount')),
				fine: parseFloat(jQuery(this).data('fine')),
				name: jQuery(this).data('name')
			};
		} else {
			delete selectedFees[id];
		}

		// Update select all state
		var total = jQuery('.fee-checkbox').length;
		var checked = jQuery('.fee-checkbox:checked').length;
		jQuery('#selectAllFees').prop('checked', total > 0 && checked === total);
		jQuery('#selectAllFees').prop('indeterminate', checked > 0 && checked < total);

		updateCollectForm();
	});

	// Recalculate on discount/fine change
	jQuery('#feeDiscount, #feeFinAmount').on('input', calculateTotal);

	// Show transaction ID field for non-cash payments
	jQuery('#paymentMode').on('change', function() {
		if (jQuery(this).val() === 'cash') {
			jQuery('#transactionIdGroup').addClass('d-none');
		} else {
			jQuery('#transactionIdGroup').removeClass('d-none');
		}
	});

	// Submit handler: if multiple fees selected, submit them one by one via AJAX
	jQuery('#collectForm').on('submit', function(e) {
		var ids = Object.keys(selectedFees);
		if (ids.length <= 1) return true; // Single fee — normal form submit

		e.preventDefault();

		var formData = jQuery(this).serializeArray();
		var baseDiscount = parseFloat(jQuery('#feeDiscount').val()) || 0;
		var perFeeDiscount = Math.round((baseDiscount / ids.length) * 100) / 100;
		var processed = 0;
		var errors = [];

		Swal.fire({
			title: 'Collecting fees...',
			html: 'Processing <strong>0</strong> of <strong>' + ids.length + '</strong> fees',
			allowOutsideClick: false,
			allowEscapeKey: false,
			didOpen: function() { Swal.showLoading(); }
		});

		function processNext(index) {
			if (index >= ids.length) {
				if (errors.length > 0) {
					Swal.fire({
						icon: 'warning',
						title: 'Partially Collected',
						html: 'Collected <strong>' + processed + '</strong> of <strong>' + ids.length + '</strong> fees.<br>' + errors.join('<br>'),
						confirmButtonColor: '#3085d6'
					}).then(function() { window.location.reload(); });
				} else {
					Swal.fire({
						icon: 'success',
						title: 'All Fees Collected!',
						text: processed + ' fee(s) collected successfully.',
						confirmButtonColor: '#3085d6'
					}).then(function() { window.location.reload(); });
				}
				return;
			}

			var fee = selectedFees[ids[index]];
			var data = {
				_token: '{{ csrf_token() }}',
				student_id: '{{ $student->id }}',
				academic_year_id: '{{ $currentYear->id }}',
				fee_structure_id: ids[index],
				amount: fee.amount,
				fine_amount: fee.fine,
				discount_amount: perFeeDiscount,
				payment_mode: jQuery('#paymentMode').val(),
				transaction_id: jQuery('input[name="transaction_id"]').val(),
				payment_date: jQuery('input[name="payment_date"]').val(),
				remarks: jQuery('textarea[name="remarks"]').val()
			};

			jQuery.ajax({
				url: '{{ route("admin.fees.collection.store") }}',
				type: 'POST',
				data: data,
				success: function() {
					processed++;
					Swal.getHtmlContainer().querySelector('strong').textContent = processed;
					processNext(index + 1);
				},
				error: function(xhr) {
					errors.push(fee.name + ': ' + (xhr.responseJSON?.message || 'Failed'));
					processNext(index + 1);
				}
			});
		}

		processNext(0);
	});
});
</script>
@endpush
