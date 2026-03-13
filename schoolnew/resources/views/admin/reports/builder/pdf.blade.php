<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Custom Report - {{ $dataSourceName }}</title>
	<style>
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}
		body {
			font-family: 'DejaVu Sans', sans-serif;
			font-size: 10px;
			line-height: 1.4;
			color: #333;
		}
		.header {
			text-align: center;
			margin-bottom: 20px;
			padding-bottom: 15px;
			border-bottom: 2px solid #333;
		}
		.header h1 {
			font-size: 18px;
			margin-bottom: 5px;
		}
		.header p {
			font-size: 11px;
			color: #666;
		}
		.meta-info {
			margin-bottom: 15px;
			padding: 10px;
			background: #f5f5f5;
		}
		.meta-info table {
			width: 100%;
		}
		.meta-info td {
			padding: 3px 10px;
		}
		.meta-info .label {
			font-weight: bold;
			width: 120px;
		}
		table.data-table {
			width: 100%;
			border-collapse: collapse;
			margin-top: 10px;
		}
		table.data-table th,
		table.data-table td {
			border: 1px solid #ddd;
			padding: 6px 8px;
			text-align: left;
		}
		table.data-table th {
			background-color: #4a90d9;
			color: white;
			font-weight: bold;
			text-transform: uppercase;
			font-size: 9px;
		}
		table.data-table tr:nth-child(even) {
			background-color: #f9f9f9;
		}
		table.data-table tr:hover {
			background-color: #f5f5f5;
		}
		.footer {
			position: fixed;
			bottom: 0;
			left: 0;
			right: 0;
			text-align: center;
			font-size: 9px;
			color: #666;
			padding: 10px;
			border-top: 1px solid #ddd;
		}
		.page-break {
			page-break-after: always;
		}
		.summary {
			margin-top: 20px;
			text-align: right;
			font-size: 11px;
		}
		.no-data {
			text-align: center;
			padding: 40px;
			color: #666;
		}
	</style>
</head>
<body>
	<div class="header">
		<h1>{{ $dataSourceName }} Report</h1>
		<p>Generated on {{ $generatedAt }}</p>
	</div>

	<div class="meta-info">
		<table>
			<tr>
				<td class="label">Report Type:</td>
				<td>{{ $dataSourceName }}</td>
				<td class="label">Total Records:</td>
				<td>{{ $data->count() }}</td>
			</tr>
			<tr>
				<td class="label">Columns:</td>
				<td colspan="3">{{ count($columns) }} columns selected</td>
			</tr>
		</table>
	</div>

	@if($data->isEmpty())
		<div class="no-data">
			<p>No records found matching the selected criteria.</p>
		</div>
	@else
		<table class="data-table">
			<thead>
				<tr>
					<th>#</th>
					@foreach($columns as $col)
						<th>{{ $columnLabels[$col] ?? $col }}</th>
					@endforeach
				</tr>
			</thead>
			<tbody>
				@foreach($data as $index => $row)
					<tr>
						<td>{{ $index + 1 }}</td>
						@foreach($columns as $col)
							<td>{{ $row[$col] ?? '-' }}</td>
						@endforeach
					</tr>
				@endforeach
			</tbody>
		</table>

		<div class="summary">
			<strong>Total Records: {{ $data->count() }}</strong>
		</div>
	@endif

	<div class="footer">
		School Management System - Custom Report | Page {PAGE_NUM} of {PAGE_COUNT}
	</div>
</body>
</html>
