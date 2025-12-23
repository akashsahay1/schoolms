@extends('layouts.app')

@section('title', 'Fee Receipt')

@section('page-title', 'Fee Receipt')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.fees.collection') }}">Fee Collection</a></li>
	<li class="breadcrumb-item active">Receipt</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<div class="d-flex justify-content-end mb-3">
					<button onclick="window.print()" class="btn btn-primary">
						<i data-feather="printer" class="me-1"></i> Print Receipt
					</button>
				</div>

				<div id="receipt-content" class="p-4 border">
					<div class="text-center mb-4">
						<h3 class="mb-0">School Management System</h3>
						<p class="text-muted mb-0">Fee Payment Receipt</p>
					</div>

					<div class="row mb-4">
						<div class="col-6">
							<p class="mb-1"><strong>Receipt No:</strong> {{ $feeCollection->receipt_no }}</p>
							<p class="mb-1"><strong>Date:</strong> {{ $feeCollection->payment_date->format('d M Y') }}</p>
							<p class="mb-1"><strong>Payment Mode:</strong> {{ str_replace('_', ' ', ucfirst($feeCollection->payment_mode)) }}</p>
							@if($feeCollection->transaction_id)
								<p class="mb-1"><strong>Transaction ID:</strong> {{ $feeCollection->transaction_id }}</p>
							@endif
						</div>
						<div class="col-6 text-end">
							<p class="mb-1"><strong>Academic Year:</strong> {{ $feeCollection->academicYear->name }}</p>
						</div>
					</div>

					<div class="mb-4">
						<h5 class="border-bottom pb-2">Student Information</h5>
						<div class="row">
							<div class="col-6">
								<p class="mb-1"><strong>Name:</strong> {{ $feeCollection->student->full_name }}</p>
								<p class="mb-1"><strong>Admission No:</strong> {{ $feeCollection->student->admission_no }}</p>
								<p class="mb-1"><strong>Class:</strong> {{ $feeCollection->student->schoolClass->name }} {{ $feeCollection->student->section ? '(' . $feeCollection->student->section->name . ')' : '' }}</p>
							</div>
							<div class="col-6">
								@if($feeCollection->student->roll_no)
									<p class="mb-1"><strong>Roll No:</strong> {{ $feeCollection->student->roll_no }}</p>
								@endif
								@if($feeCollection->student->phone)
									<p class="mb-1"><strong>Phone:</strong> {{ $feeCollection->student->phone }}</p>
								@endif
							</div>
						</div>
					</div>

					<div class="mb-4">
						<h5 class="border-bottom pb-2">Fee Details</h5>
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>Description</th>
									<th class="text-end">Amount</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>{{ $feeCollection->feeStructure->feeType->name }} ({{ $feeCollection->feeStructure->feeGroup->name }})</td>
									<td class="text-end">₹{{ number_format($feeCollection->amount, 2) }}</td>
								</tr>
								@if($feeCollection->discount_amount > 0)
									<tr>
										<td>Discount</td>
										<td class="text-end text-success">- ₹{{ number_format($feeCollection->discount_amount, 2) }}</td>
									</tr>
								@endif
								@if($feeCollection->fine_amount > 0)
									<tr>
										<td>Fine</td>
										<td class="text-end text-danger">+ ₹{{ number_format($feeCollection->fine_amount, 2) }}</td>
									</tr>
								@endif
								<tr class="table-active">
									<td><strong>Total Paid</strong></td>
									<td class="text-end"><strong>₹{{ number_format($feeCollection->paid_amount, 2) }}</strong></td>
								</tr>
							</tbody>
						</table>
					</div>

					@if($feeCollection->remarks)
						<div class="mb-4">
							<h5 class="border-bottom pb-2">Remarks</h5>
							<p>{{ $feeCollection->remarks }}</p>
						</div>
					@endif

					<div class="row mt-5">
						<div class="col-6">
							<p class="mb-0"><strong>Collected By:</strong> {{ $feeCollection->collectedBy->name }}</p>
						</div>
						<div class="col-6 text-end">
							<p class="mb-0">Authorized Signature</p>
							<div class="mt-4 border-top d-inline-block px-5"></div>
						</div>
					</div>

					<div class="text-center mt-4 text-muted">
						<small>This is a computer-generated receipt and does not require a signature.</small>
					</div>
				</div>

				<div class="mt-3">
					<a href="{{ route('admin.fees.collection') }}" class="btn btn-light">Back to Collection</a>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
@media print {
	.card-header, .btn, nav, .breadcrumb, aside, footer {
		display: none !important;
	}
	.card {
		border: none !important;
		box-shadow: none !important;
	}
	#receipt-content {
		border: 2px solid #000 !important;
	}
}
</style>
@endsection
