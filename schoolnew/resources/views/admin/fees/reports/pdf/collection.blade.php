<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fee Collection Report</title>
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
            border-bottom: 2px solid #4472C4;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 18px;
            color: #4472C4;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 11px;
        }
        .summary-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 15px;
        }
        .summary-box table {
            width: 100%;
        }
        .summary-box td {
            padding: 5px;
        }
        .summary-box .label {
            color: #666;
            font-size: 9px;
        }
        .summary-box .value {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.data-table th {
            background: #4472C4;
            color: white;
            padding: 8px 5px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
        }
        table.data-table td {
            padding: 6px 5px;
            border-bottom: 1px solid #dee2e6;
            font-size: 9px;
        }
        table.data-table tr:nth-child(even) {
            background: #f8f9fa;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        .badge-primary {
            background: #cce5ff;
            color: #004085;
        }
        .badge-secondary {
            background: #e2e3e5;
            color: #383d41;
        }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #dee2e6;
            font-size: 8px;
            color: #666;
            text-align: center;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Fee Collection Report</h1>
        <p>Period: {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}</p>
        <p>Generated on: {{ now()->format('d M Y, h:i A') }}</p>
    </div>

    <div class="summary-box">
        <table>
            <tr>
                <td style="width: 25%;">
                    <div class="label">Total Collected</div>
                    <div class="value" style="color: #28a745;">{{ number_format($summary['total_amount'], 2) }}</div>
                </td>
                <td style="width: 25%;">
                    <div class="label">Total Discount</div>
                    <div class="value" style="color: #ffc107;">{{ number_format($summary['total_discount'], 2) }}</div>
                </td>
                <td style="width: 25%;">
                    <div class="label">Total Fine</div>
                    <div class="value" style="color: #dc3545;">{{ number_format($summary['total_fine'], 2) }}</div>
                </td>
                <td style="width: 25%;">
                    <div class="label">Total Transactions</div>
                    <div class="value">{{ number_format($summary['total_transactions']) }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 12%;">Receipt No</th>
                <th style="width: 10%;">Date</th>
                <th style="width: 18%;">Student</th>
                <th style="width: 10%;">Class</th>
                <th style="width: 15%;">Fee Type</th>
                <th style="width: 12%;" class="text-right">Amount</th>
                <th style="width: 10%;" class="text-center">Mode</th>
            </tr>
        </thead>
        <tbody>
            @foreach($collections as $index => $collection)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $collection->receipt_no }}</td>
                    <td>{{ $collection->payment_date->format('d-m-Y') }}</td>
                    <td>{{ $collection->student->full_name ?? 'N/A' }}</td>
                    <td>{{ $collection->student->schoolClass->name ?? 'N/A' }}</td>
                    <td>{{ $collection->feeStructure->feeType->name ?? 'N/A' }}</td>
                    <td class="text-right">{{ number_format($collection->paid_amount, 2) }}</td>
                    <td class="text-center">
                        <span class="badge badge-{{ $collection->payment_mode == 'cash' ? 'success' : ($collection->payment_mode == 'online' ? 'primary' : 'secondary') }}">
                            {{ ucfirst($collection->payment_mode) }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        School Management System - Fee Collection Report
    </div>
</body>
</html>
