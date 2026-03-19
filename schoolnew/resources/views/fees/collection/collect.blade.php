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
						<p class="text-muted mb-0">{{ $student->admission_no ?? $student->roll_no }} &bull; {{ $student->schoolClass->name ?? 'N/A' }} {{ $student->section ? '(' . $student->section->name . ')' : '' }}</p>
					</div>
					<div class="col-md-6 text-md-end mt-2 mt-md-0">
						@php
							$totalFees = $unpaidFees->sum('amount') + ($paymentHistory->sum('paid_amount') ?? 0);
							$totalPaid = $paymentHistory->where('paid_amount', '>', 0)->sum('paid_amount');
							$totalPending = $unpaidFees->sum('amount');
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

		<!-- Unpaid Fees -->
		@if($unpaidFees->count() > 0)
		<div class="card">
			<div class="card-header">
				<h5>Pending Fees</h5>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-hover">
						<thead>
							<tr>
								<th>Fee Type</th>
								<th>Amount</th>
								<th>Due Date</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
							@foreach($unpaidFees as $fee)
								<tr>
									<td>
										<strong>{{ $fee->feeType->name ?? 'N/A' }}</strong>
										@if($fee->feeGroup)<br><small class="text-muted">{{ $fee->feeGroup->name }}</small>@endif
									</td>
									<td>₹{{ number_format($fee->amount, 2) }}</td>
									<td>
										@if($fee->due_date)
											{{ $fee->due_date->format('d M Y') }}
											@if($fee->due_date->isPast())
												<br><span class="badge badge-light-danger">Overdue</span>
											@endif
										@else
											<span class="text-muted">No due date</span>
										@endif
									</td>
									<td><span class="badge badge-light-warning px-3 py-2">Pending</span></td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>

				<div class="alert alert-info mt-3 mb-0">
					<i class="icon-info-alt me-2"></i>
					Pending fees of <strong>₹{{ number_format($totalPending, 2) }}</strong> can be paid online by the parent/student through their portal using the <strong>"Pay Now"</strong> button.
				</div>
			</div>
		</div>
		@else
		<div class="card">
			<div class="card-body text-center py-4">
				<h5 class="text-success mb-2">All Fees Paid!</h5>
				<p class="text-muted mb-0">This student has no pending fees for the current academic year.</p>
			</div>
		</div>
		@endif

		<!-- Payment History -->
		@if($paymentHistory->count() > 0)
		<div class="card mt-3">
			<div class="card-header">
				<h5>Payment History</h5>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-hover">
						<thead>
							<tr>
								<th>Receipt</th>
								<th>Fee Type</th>
								<th>Amount</th>
								<th>Date</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
							@foreach($paymentHistory as $payment)
								<tr>
									<td><a href="{{ route('admin.fees.receipt', $payment) }}">{{ $payment->receipt_no }}</a></td>
									<td>{{ $payment->feeStructure->feeType->name ?? 'N/A' }}</td>
									<td class="fw-bold">₹{{ number_format($payment->paid_amount, 2) }}</td>
									<td>{{ $payment->payment_date->format('d M Y') }}</td>
									<td><span class="badge badge-light-success px-3 py-2">Paid</span></td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
		@endif

		<div class="mt-3">
			<a href="{{ route('admin.fees.collection') }}" class="btn btn-outline-secondary">
				<i class="icon-arrow-left me-1"></i> Back to Payments
			</a>
		</div>
	</div>
</div>
@endsection
