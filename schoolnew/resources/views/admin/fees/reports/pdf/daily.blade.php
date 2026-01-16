<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daily Collection Report</title>
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
            border-bottom: 2px solid #548235;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 18px;
            color: #548235;
            margin-bottom: 5px;
        }
        .header h2 {
            font-size: 14px;
            color: #333;
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
        .summary-grid {
            display: table;
            width: 100%;
        }
        .summary-item {
            display: table-cell;
            text-align: center;
            padding: 5px;
            border-right: 1px solid #dee2e6;
        }
        .summary-item:last-child {
            border-right: none;
        }
        .summary-item .label {
            font-size: 9px;
            color: #666;
            margin-bottom: 3px;
        }
        .summary-item .value {
            font-size: 14px;
            font-weight: bold;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.data-table th {
            background: #548235;
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
        table.data-table tfoot td {
            background: #e2e3e5;
            font-weight: bold;
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
        .badge-success { background: #d4edda; color: #155724; }
        .badge-primary { background: #cce5ff; color: #004085; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-secondary { background: #e2e3e5; color: #383d41; }
        .badge-dark { background: #d6d8db; color: #1b1e21; }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #dee2e6;
            font-size: 8px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Daily Collection Report</h1>
        <h2>{{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}</h2>
        <p>Generated on: {{ now()->format('d M Y, h:i A') }}</p>
    </div>

    <div class="summary-box">
        <table style="width: 100%;">
            <tr>
                <td style="width: 16.66%; text-align: center; border-right: 1px solid #dee2e6;">
                    <div style="font-size: 9px; color: #666;">Total</div>
                    <div style="font-size: 14px; font-weight: bold; color: #4472C4;">{{ number_format($summary['total'], 2) }}</div>
                </td>
                <td style="width: 16.66%; text-align: center; border-right: 1px solid #dee2e6;">
                    <div style="font-size: 9px; color: #666;">Cash</div>
                    <div style="font-size: 14px; font-weight: bold; color: #28a745;">{{ number_format($summary['cash'], 2) }}</div>
                </td>
                <td style="width: 16.66%; text-align: center; border-right: 1px solid #dee2e6;">
                    <div style="font-size: 9px; color: #666;">Online</div>
                    <div style="font-size: 14px; font-weight: bold; color: #17a2b8;">{{ number_format($summary['online'], 2) }}</div>
                </td>
                <td style="width: 16.66%; text-align: center; border-right: 1px solid #dee2e6;">
                    <div style="font-size: 9px; color: #666;">Cheque</div>
                    <div style="font-size: 14px; font-weight: bold; color: #ffc107;">{{ number_format($summary['cheque'], 2) }}</div>
                </td>
                <td style="width: 16.66%; text-align: center; border-right: 1px solid #dee2e6;">
                    <div style="font-size: 9px; color: #666;">Card</div>
                    <div style="font-size: 14px; font-weight: bold; color: #6c757d;">{{ number_format($summary['card'], 2) }}</div>
                </td>
                <td style="width: 16.66%; text-align: center;">
                    <div style="font-size: 9px; color: #666;">Transactions</div>
                    <div style="font-size: 14px; font-weight: bold; color: #343a40;">{{ $summary['count'] }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 12%;">Receipt No</th>
                <th style="width: 8%;">Time</th>
                <th style="width: 20%;">Student</th>
                <th style="width: 10%;">Class</th>
                <th style="width: 15%;">Fee Type</th>
                <th style="width: 12%;" class="text-right">Amount</th>
                <th style="width: 10%;" class="text-center">Mode</th>
            </tr>
        </thead>
        <tbody>
            @forelse($collections as $index => $collection)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $collection->receipt_no }}</td>
                    <td>{{ $collection->created_at->format('h:i A') }}</td>
                    <td>{{ $collection->student->full_name ?? 'N/A' }}</td>
                    <td>{{ $collection->student->schoolClass->name ?? 'N/A' }}</td>
                    <td>{{ $collection->feeStructure->feeType->name ?? 'N/A' }}</td>
                    <td class="text-right">{{ number_format($collection->paid_amount, 2) }}</td>
                    <td class="text-center">
                        @php
                            $modeClass = [
                                'cash' => 'success',
                                'online' => 'primary',
                                'cheque' => 'warning',
                                'card' => 'secondary',
                                'bank_transfer' => 'dark'
                            ][$collection->payment_mode] ?? 'secondary';
                        @endphp
                        <span class="badge badge-{{ $modeClass }}">
                            {{ ucfirst($collection->payment_mode) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 30px;">No collections on this date</td>
                </tr>
            @endforelse
        </tbody>
        @if($collections->count() > 0)
            <tfoot>
                <tr>
                    <td colspan="6" class="text-right">Total:</td>
                    <td class="text-right">{{ number_format($summary['total'], 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="footer">
        School Management System - Daily Collection Report
    </div>
</body>
</html>
