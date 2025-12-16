@extends('layouts.app')

@section('title', 'Fee Receipt - ' . $feeCollection->receipt_no)

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
				<div class="text-end mb-3 no-print">
					<button type="button" class="btn btn-primary" onclick="window.print()">
						<i class="fa-solid fa-print me-1"></i> Print Receipt
					</button>
					<a href="{{ route('admin.fees.collection') }}" class="btn btn-light">Back to Collection</a>
				</div>

				<div class="receipt-container">
					<!-- School Header -->
					<div class="receipt-header">
						<div class="school-logo">
							@if($schoolSettings['school_logo'])
								<img src="{{ asset('storage/' . $schoolSettings['school_logo']) }}" alt="School Logo">
							@else
								<img src="{{ asset('assets/images/logo/logo.png') }}" alt="School Logo">
							@endif
						</div>
						<div class="school-info">
							<h2>{{ $schoolSettings['school_name'] ?? config('app.name') }}</h2>
							<p>{{ $schoolSettings['school_address'] ?? '' }}</p>
							<p>Phone: {{ $schoolSettings['school_phone'] ?? '' }} | Email: {{ $schoolSettings['school_email'] ?? '' }}</p>
						</div>
					</div>

					<div class="receipt-title">
						<h3>FEE RECEIPT</h3>
					</div>

					<!-- Receipt Info Row -->
					<div class="receipt-info-row">
						<table class="info-table">
							<tr>
								<td class="label">Receipt No:</td>
								<td class="value"><strong>{{ $feeCollection->receipt_no }}</strong></td>
							</tr>
							<tr>
								<td class="label">Date:</td>
								<td class="value">{{ $feeCollection->payment_date->format('d-m-Y') }}</td>
							</tr>
							<tr>
								<td class="label">Academic Year:</td>
								<td class="value">{{ $feeCollection->academicYear->name ?? 'N/A' }}</td>
							</tr>
						</table>
						<table class="info-table">
							<tr>
								<td class="label">Student Name:</td>
								<td class="value"><strong>{{ $feeCollection->student->full_name }}</strong></td>
							</tr>
							<tr>
								<td class="label">Admission No:</td>
								<td class="value">{{ $feeCollection->student->admission_no }}</td>
							</tr>
							<tr>
								<td class="label">Class & Section:</td>
								<td class="value">{{ $feeCollection->student->schoolClass->name ?? 'N/A' }} - {{ $feeCollection->student->section->name ?? 'N/A' }}</td>
							</tr>
						</table>
					</div>

					<!-- Fee Details Table -->
					<div class="fee-table-container">
						<table class="fee-table">
							<thead>
								<tr>
									<th style="width: 8%;">S.No</th>
									<th style="width: 25%;">Fee Type</th>
									<th style="width: 20%;">Fee Group</th>
									<th style="width: 12%;">Amount</th>
									<th style="width: 12%;">Fine</th>
									<th style="width: 12%;">Discount</th>
									<th style="width: 12%;">Paid</th>
								</tr>
							</thead>
							<tbody>
								@php $totalAmount = 0; $totalFine = 0; $totalDiscount = 0; $totalPaid = 0; @endphp
								@foreach($collections as $index => $collection)
									<tr>
										<td class="text-center">{{ $index + 1 }}</td>
										<td>{{ $collection->feeStructure->feeType->name ?? 'N/A' }}</td>
										<td>{{ $collection->feeStructure->feeGroup->name ?? 'N/A' }}</td>
										<td class="text-end">{{ number_format($collection->amount, 2) }}</td>
										<td class="text-end">{{ number_format($collection->fine_amount, 2) }}</td>
										<td class="text-end">{{ number_format($collection->discount_amount, 2) }}</td>
										<td class="text-end">{{ number_format($collection->paid_amount, 2) }}</td>
									</tr>
									@php
										$totalAmount += $collection->amount;
										$totalFine += $collection->fine_amount;
										$totalDiscount += $collection->discount_amount;
										$totalPaid += $collection->paid_amount;
									@endphp
								@endforeach
							</tbody>
							<tfoot>
								<tr>
									<th colspan="3" class="text-end">Total (₹)</th>
									<th class="text-end">{{ number_format($totalAmount, 2) }}</th>
									<th class="text-end">{{ number_format($totalFine, 2) }}</th>
									<th class="text-end">{{ number_format($totalDiscount, 2) }}</th>
									<th class="text-end">{{ number_format($totalPaid, 2) }}</th>
								</tr>
							</tfoot>
						</table>
					</div>

					<!-- Payment Summary -->
					<div class="payment-summary">
						<div class="payment-mode">
							<p><span class="label">Payment Mode:</span> <strong>{{ ucfirst($feeCollection->payment_mode) }}</strong></p>
							@if($feeCollection->transaction_id)
								<p><span class="label">Transaction ID:</span> {{ $feeCollection->transaction_id }}</p>
							@endif
						</div>
						<div class="total-box">
							<p class="total-label">Total Amount Paid</p>
							<p class="total-amount">₹ {{ number_format($totalPaid, 2) }}</p>
							<p class="amount-words">{{ ucwords(convertNumberToWords($totalPaid)) }} Rupees Only</p>
						</div>
					</div>

					@if($feeCollection->remarks)
						<div class="remarks">
							<strong>Remarks:</strong> {{ $feeCollection->remarks }}
						</div>
					@endif

					<!-- Signature Section -->
					<div class="signature-section">
						<div class="received-by">
							<p class="sign-label">Received By</p>
							<p class="sign-line">_____________________</p>
							<p class="sign-name">{{ $feeCollection->collectedBy->name ?? 'N/A' }}</p>
						</div>
						<div class="authorized-sign">
							<p class="sign-label">Authorized Signature</p>
							@if($schoolSettings['signature_image'] ?? false)
								<img src="{{ asset('storage/' . $schoolSettings['signature_image']) }}" alt="Signature" class="signature-img">
							@else
								<p class="sign-line">_____________________</p>
							@endif
							<p class="sign-name">{{ $schoolSettings['authorized_signature_text'] ?? 'Principal/Cashier' }}</p>
						</div>
					</div>

					<!-- Footer -->
					<div class="receipt-footer">
						<p>This is a computer generated receipt</p>
						<p>Printed on: {{ now()->format('d-m-Y h:i A') }}</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@push('styles')
<style>
	.receipt-container {
		max-width: 800px;
		margin: 0 auto;
		padding: 20px;
		background: #fff;
		font-family: Arial, sans-serif;
	}

	.receipt-header {
		display: flex;
		align-items: center;
		justify-content: center;
		gap: 20px;
		padding-bottom: 15px;
		border-bottom: 2px solid #333;
		margin-bottom: 10px;
	}

	.school-logo img {
		width: 70px;
		height: 70px;
		object-fit: contain;
	}

	.school-info {
		text-align: center;
	}

	.school-info h2 {
		font-size: 22px;
		font-weight: bold;
		margin: 0 0 5px 0;
		color: #333;
	}

	.school-info p {
		font-size: 12px;
		margin: 2px 0;
		color: #555;
	}

	.receipt-title {
		text-align: center;
		margin: 15px 0;
	}

	.receipt-title h3 {
		font-size: 18px;
		font-weight: bold;
		background: #7366ff;
		color: white;
		padding: 8px 30px;
		display: inline-block;
		border-radius: 4px;
		margin: 0;
	}

	.receipt-info-row {
		display: flex;
		justify-content: space-between;
		margin-bottom: 20px;
		gap: 20px;
	}

	.info-table {
		width: 48%;
		border-collapse: collapse;
	}

	.info-table td {
		padding: 4px 8px;
		font-size: 13px;
		vertical-align: top;
	}

	.info-table td.label {
		width: 120px;
		color: #666;
	}

	.info-table td.value {
		color: #333;
	}

	.fee-table-container {
		margin-bottom: 20px;
	}

	.fee-table {
		width: 100%;
		border-collapse: collapse;
		font-size: 12px;
	}

	.fee-table th, .fee-table td {
		border: 1px solid #ddd;
		padding: 8px 6px;
	}

	.fee-table thead th {
		background: #f5f5f5;
		font-weight: bold;
		text-align: center;
		color: #333;
	}

	.fee-table tbody td {
		color: #333;
	}

	.fee-table tfoot th {
		background: #f0f0f0;
		font-weight: bold;
	}

	.payment-summary {
		display: flex;
		justify-content: space-between;
		align-items: flex-start;
		margin-bottom: 15px;
		padding: 15px 0;
	}

	.payment-mode p {
		margin: 3px 0;
		font-size: 13px;
		color: #333;
	}

	.payment-mode .label {
		color: #666;
	}

	.total-box {
		text-align: right;
	}

	.total-label {
		font-size: 12px;
		color: #333;
		margin: 0;
	}

	.total-amount {
		font-size: 24px;
		font-weight: bold;
		color: #7366ff;
		margin: 5px 0;
	}

	.amount-words {
		font-size: 11px;
		color: #333;
		font-style: italic;
		margin: 0;
	}

	.remarks {
		padding: 10px;
		background: #fff8e1;
		border-left: 3px solid #ffc107;
		margin-bottom: 20px;
		font-size: 12px;
	}

	.signature-section {
		display: flex;
		justify-content: space-between;
		margin-top: 40px;
		padding-top: 20px;
	}

	.received-by, .authorized-sign {
		text-align: center;
		width: 200px;
	}

	.sign-label {
		font-size: 11px;
		color: #666;
		margin-bottom: 30px;
	}

	.sign-line {
		margin: 10px 0 5px 0;
		color: #333;
	}

	.sign-name {
		font-size: 12px;
		font-weight: bold;
		margin: 0;
	}

	.signature-img {
		max-height: 50px;
		margin-bottom: 5px;
	}

	.receipt-footer {
		text-align: center;
		margin-top: 30px;
		padding-top: 15px;
		border-top: 1px dashed #ccc;
	}

	.receipt-footer p {
		font-size: 10px;
		color: #999;
		margin: 2px 0;
	}

	/* Print Styles */
	@media print {
		* {
			-webkit-print-color-adjust: exact !important;
			print-color-adjust: exact !important;
		}

		body {
			margin: 0;
			padding: 0;
		}

		.no-print, .sidebar-wrapper, .page-header, .footer, .breadcrumb-wrapper {
			display: none !important;
		}

		.page-wrapper, .page-body-wrapper, .page-body {
			margin: 0 !important;
			padding: 0 !important;
		}

		.card {
			border: none !important;
			box-shadow: none !important;
			margin: 0 !important;
		}

		.card-body {
			padding: 0 !important;
		}

		.receipt-container {
			max-width: 100%;
			padding: 15px;
			page-break-inside: avoid;
		}

		.receipt-header {
			padding-bottom: 10px;
		}

		.school-logo img {
			width: 60px;
			height: 60px;
		}

		.school-info h2 {
			font-size: 18px;
		}

		.school-info p {
			font-size: 10px;
		}

		.receipt-title h3 {
			font-size: 14px;
			padding: 6px 20px;
			background: #7366ff !important;
			color: white !important;
		}

		.info-table td {
			font-size: 11px;
			padding: 3px 6px;
		}

		.fee-table {
			font-size: 10px;
		}

		.fee-table th, .fee-table td {
			padding: 5px 4px;
		}

		.fee-table thead th {
			background: #f5f5f5 !important;
		}

		.fee-table tfoot th {
			background: #f0f0f0 !important;
		}

		.payment-summary {
			padding: 10px 0;
		}

		.payment-mode p,
		.total-label,
		.amount-words {
			color: #333 !important;
		}

		.total-amount {
			font-size: 18px;
			color: #7366ff !important;
		}

		.signature-section {
			margin-top: 30px;
		}

		.receipt-footer {
			margin-top: 20px;
		}

		@page {
			size: A4;
			margin: 10mm;
		}
	}
</style>
@endpush

@php
function convertNumberToWords($number) {
	$words = '';
	$ones = array('', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen');
	$tens = array('', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety');

	if ($number == 0) return 'Zero';

	$number = (int) $number;

	if ($number >= 10000000) {
		$crore = intval($number / 10000000);
		if ($crore < 20) {
			$words .= $ones[$crore] . ' Crore ';
		} else {
			$words .= $tens[intval($crore / 10)] . ' ' . $ones[$crore % 10] . ' Crore ';
		}
		$number %= 10000000;
	}

	if ($number >= 100000) {
		$lakh = intval($number / 100000);
		if ($lakh < 20) {
			$words .= $ones[$lakh] . ' Lakh ';
		} else {
			$words .= $tens[intval($lakh / 10)] . ' ' . $ones[$lakh % 10] . ' Lakh ';
		}
		$number %= 100000;
	}

	if ($number >= 1000) {
		$thousands = intval($number / 1000);
		if ($thousands < 20) {
			$words .= $ones[$thousands] . ' Thousand ';
		} else {
			$words .= $tens[intval($thousands / 10)] . ' ' . $ones[$thousands % 10] . ' Thousand ';
		}
		$number %= 1000;
	}

	if ($number >= 100) {
		$words .= $ones[intval($number / 100)] . ' Hundred ';
		$number %= 100;
	}

	if ($number >= 20) {
		$words .= $tens[intval($number / 10)] . ' ' . $ones[$number % 10];
	} else if ($number > 0) {
		$words .= $ones[$number];
	}

	return trim($words);
}
@endphp
