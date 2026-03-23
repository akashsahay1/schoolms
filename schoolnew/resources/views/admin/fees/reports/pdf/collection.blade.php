<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Fee Collection Report</title>
	<style>
		@page {
			margin: 25px 30px;
		}
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}
		body {
			font-family: 'DejaVu Sans', sans-serif;
			font-size: 10px;
			line-height: 1.5;
			color: #2d3436;
			padding: 10px 0;
		}

		/* Header */
		.header {
			text-align: center;
			margin-bottom: 20px;
			padding-bottom: 12px;
			border-bottom: 2px solid #4472C4;
		}
		.header h1 {
			font-size: 20px;
			color: #4472C4;
			margin-bottom: 4px;
			letter-spacing: 0.5px;
		}
		.header .subtitle {
			color: #636e72;
			font-size: 11px;
			margin-bottom: 2px;
		}
		.header .meta {
			color: #b2bec3;
			font-size: 9px;
		}

		/* Student Info Banner */
		.student-banner {
			background: #edf2fb;
			border: 1px solid #c5d5ea;
			border-radius: 4px;
			padding: 10px 14px;
			margin-bottom: 16px;
			font-size: 10px;
		}
		.student-banner strong {
			color: #2d3436;
		}

		/* Summary Cards */
		.summary-box {
			background: #f8f9fa;
			border: 1px solid #dee2e6;
			border-radius: 4px;
			padding: 12px 14px;
			margin-bottom: 18px;
		}
		.summary-box table {
			width: 100%;
			border-collapse: collapse;
		}
		.summary-box td {
			padding: 6px 8px;
			vertical-align: top;
		}
		.summary-box .label {
			color: #636e72;
			font-size: 9px;
			text-transform: uppercase;
			letter-spacing: 0.3px;
			margin-bottom: 2px;
		}
		.summary-box .value {
			font-size: 15px;
			font-weight: bold;
			color: #2d3436;
		}
		.summary-box .value.green { color: #00b894; }
		.summary-box .value.orange { color: #e17055; }
		.summary-box .value.red { color: #d63031; }
		.summary-box .value.blue { color: #4472C4; }

		/* Section title */
		.section-title {
			font-size: 12px;
			font-weight: bold;
			color: #2d3436;
			margin-bottom: 8px;
			padding-bottom: 4px;
			border-bottom: 1px solid #dfe6e9;
		}

		/* Data Table */
		table.data-table {
			width: 100%;
			border-collapse: collapse;
			margin-bottom: 16px;
		}
		table.data-table th {
			background: #4472C4;
			color: #ffffff;
			padding: 10px 8px;
			text-align: left;
			font-size: 9px;
			font-weight: bold;
			text-transform: uppercase;
			letter-spacing: 0.3px;
		}
		table.data-table th:first-child {
			border-radius: 3px 0 0 0;
		}
		table.data-table th:last-child {
			border-radius: 0 3px 0 0;
		}
		table.data-table td {
			padding: 8px 8px;
			border-bottom: 1px solid #e9ecef;
			font-size: 9px;
			color: #2d3436;
		}
		table.data-table tr:nth-child(even) {
			background: #f8f9fc;
		}
		table.data-table tr:last-child td {
			border-bottom: 2px solid #dee2e6;
		}
		table.data-table .total-row td {
			background: #edf2fb;
			font-weight: bold;
			font-size: 10px;
			border-top: 2px solid #4472C4;
			border-bottom: none;
			padding: 10px 8px;
		}

		/* Alignment */
		.text-right { text-align: right; }
		.text-center { text-align: center; }
		.fw-bold { font-weight: bold; }

		/* Footer */
		.footer {
			margin-top: 20px;
			padding-top: 8px;
			border-top: 1px solid #dee2e6;
			font-size: 8px;
			color: #b2bec3;
			text-align: center;
		}
		.footer .left { float: left; }
		.footer .right { float: right; }
		.footer .center { text-align: center; }

		/* Page break */
		.page-break {
			page-break-after: always;
		}

		/* No data */
		.no-data {
			text-align: center;
			padding: 30px 10px;
			color: #b2bec3;
			font-size: 11px;
		}
	</style>
</head>
<body>
	<!-- Header -->
	<div class="header">
		<h1>Fee Collection Report</h1>
		<p class="subtitle">Period: {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} &mdash; {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}</p>
		<p class="meta">Generated on {{ now()->format('d M Y, h:i A') }}</p>
	</div>

	<!-- Student Banner (if filtered by student) -->
	@if(isset($selectedStudent) && $selectedStudent)
	<div class="student-banner">
		Student: <strong>{{ $selectedStudent->full_name }}</strong> ({{ $selectedStudent->admission_no }})
		@if($selectedStudent->schoolClass)
			&nbsp;&bull;&nbsp; Class: <strong>{{ $selectedStudent->schoolClass->name }}</strong>
		@endif
	</div>
	@endif

	<!-- Summary Cards -->
	<div class="summary-box">
		<table>
			<tr>
				@if(isset($studentStats) && $studentStats)
					<td style="width: 33%;">
						<div class="label">Total Fees</div>
						<div class="value blue">&#8377; {{ number_format($studentStats['total_fees'], 2) }}</div>
					</td>
					<td style="width: 33%;">
						<div class="label">Total Paid</div>
						<div class="value green">&#8377; {{ number_format($studentStats['total_paid'], 2) }}</div>
					</td>
					<td style="width: 34%;">
						<div class="label">Due Amount</div>
						@if($studentStats['total_due'] > 0)
							<div class="value red">&#8377; {{ number_format($studentStats['total_due'], 2) }}</div>
						@elseif($studentStats['total_due'] < 0)
							<div class="value blue">Advance &#8377; {{ number_format(abs($studentStats['total_due']), 2) }}</div>
						@else
							<div class="value green">All Paid</div>
						@endif
					</td>
				@else
					<td style="width: 25%;">
						<div class="label">Total Collected</div>
						<div class="value green">&#8377; {{ number_format($summary['total_amount'], 2) }}</div>
					</td>
					<td style="width: 25%;">
						<div class="label">Total Discount</div>
						<div class="value orange">&#8377; {{ number_format($summary['total_discount'], 2) }}</div>
					</td>
					<td style="width: 25%;">
						<div class="label">Total Fine</div>
						<div class="value red">&#8377; {{ number_format($summary['total_fine'], 2) }}</div>
					</td>
					<td style="width: 25%;">
						<div class="label">Transactions</div>
						<div class="value blue">{{ number_format($summary['total_transactions']) }}</div>
					</td>
				@endif
			</tr>
		</table>
	</div>

	<!-- Collection Details Table -->
	<div class="section-title">Collection Details</div>

	@if($collections->count() > 0)
	<table class="data-table">
		<thead>
			<tr>
				<th style="width: 5%;">#</th>
				<th style="width: 14%;">Receipt No</th>
				<th style="width: 11%;">Date</th>
				@if(!isset($selectedStudent) || !$selectedStudent)
					<th style="width: 22%;">Student</th>
					<th style="width: 12%;">Class</th>
				@endif
				<th style="width: {{ (isset($selectedStudent) && $selectedStudent) ? '35%' : '18%' }};">Fee Type</th>
				<th style="width: {{ (isset($selectedStudent) && $selectedStudent) ? '20%' : '13%' }};" class="text-right">Amount (&#8377;)</th>
				@if(isset($selectedStudent) && $selectedStudent)
					<th style="width: 15%;" class="text-center">Status</th>
				@endif
			</tr>
		</thead>
		<tbody>
			@php $totalAmount = 0; @endphp
			@foreach($collections as $index => $collection)
				@php $totalAmount += $collection->paid_amount; @endphp
				<tr>
					<td>{{ $index + 1 }}</td>
					<td class="fw-bold">{{ $collection->receipt_no }}</td>
					<td>{{ $collection->payment_date->format('d M Y') }}</td>
					@if(!isset($selectedStudent) || !$selectedStudent)
						<td>{{ $collection->student->full_name ?? 'N/A' }}</td>
						<td>{{ $collection->student->schoolClass->name ?? '-' }}</td>
					@endif
					<td>{{ $collection->feeStructure->feeType->name ?? '-' }}</td>
					<td class="text-right fw-bold">{{ number_format($collection->paid_amount, 2) }}</td>
					@if(isset($selectedStudent) && $selectedStudent)
						<td class="text-center">Paid</td>
					@endif
				</tr>
			@endforeach
		</tbody>
		<tfoot>
			<tr class="total-row">
				<td colspan="{{ (isset($selectedStudent) && $selectedStudent) ? 3 : 5 }}">Total</td>
				<td class="text-right">{{ number_format($totalAmount, 2) }}</td>
				@if(isset($selectedStudent) && $selectedStudent)
					<td></td>
				@endif
			</tr>
		</tfoot>
	</table>
	@else
	<div class="no-data">No collections found for the selected period.</div>
	@endif

	<!-- Footer -->
	<div class="footer">
		<span class="left">Fee Collection Report &bull; {{ \Carbon\Carbon::parse($fromDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($toDate)->format('d/m/Y') }}</span>
		<span class="right">Page 1</span>
		<div style="clear: both;"></div>
	</div>
</body>
</html>
