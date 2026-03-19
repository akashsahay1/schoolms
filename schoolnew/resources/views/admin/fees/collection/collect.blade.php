@extends('layouts.app')

@section('title', 'Student Fees')

@section('page-title', 'Student Fees')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.fees.collection') }}">Payments</a></li>
	<li class="breadcrumb-item active">{{ $student->full_name }}</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12">
		@if(session('error'))
			<div class="alert alert-danger alert-dismissible fade show" role="alert">
				{{ session('error') }}
				<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
			</div>
		@endif
		@if(session('success'))
			<div class="alert alert-success alert-dismissible fade show" role="alert">
				{{ session('success') }}
				<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
			</div>
		@endif

		<!-- Student Info -->
		<div class="card mb-3">
			<div class="card-body">
				<div class="row align-items-center">
					<div class="col-md-6">
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

		<!-- Fee Details -->
		<div class="card">
			<div class="card-header">
				<h5>Fee Breakdown</h5>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-hover">
						<thead>
							<tr>
								<th>Fee Type</th>
								<th>Amount</th>
								<th>Due Date</th>
								<th>Late Fee</th>
								<th>Total</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
							@php $today = now()->startOfDay(); @endphp
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
								@endphp
								<tr class="{{ $isOverdue ? 'table-warning' : '' }}">
									<td>
										<strong>{{ $structure->feeType->name }}</strong>
										@if($structure->feeGroup)<br><small class="text-muted">{{ $structure->feeGroup->name }}</small>@endif
									</td>
									<td>₹{{ number_format($structure->amount, 2) }}</td>
									<td>
										{{ $structure->due_date ? $structure->due_date->format('d M Y') : '-' }}
										@if($isOverdue)
											<br><span class="badge badge-light-danger">Overdue</span>
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

				@if($totalPending > 0)
				<div class="alert alert-info mt-3 mb-0">
					<i class="icon-info-alt me-2"></i>
					Pending fees of <strong>₹{{ number_format($totalPending, 2) }}</strong> can be paid online by the parent/student through their portal. Please ask the parent to log in and use the <strong>"Pay Now"</strong> button.
				</div>
				@endif

				<div class="mt-3">
					<a href="{{ route('admin.fees.collection') }}" class="btn btn-outline-secondary">
						<i class="icon-arrow-left me-1"></i> Back to Payments
					</a>
					<a href="{{ route('admin.fees.outstanding') }}" class="btn btn-outline-warning ms-2">
						<i class="icon-info-alt me-1"></i> Outstanding Fees
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
