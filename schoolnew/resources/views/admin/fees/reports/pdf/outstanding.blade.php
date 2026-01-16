<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Outstanding Fees Report</title>
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
            border-bottom: 2px solid #C65911;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 18px;
            color: #C65911;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 11px;
        }
        .summary-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            text-align: center;
        }
        .summary-box .total-label {
            font-size: 11px;
            color: #856404;
            margin-bottom: 5px;
        }
        .summary-box .total-value {
            font-size: 24px;
            font-weight: bold;
            color: #dc3545;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.data-table th {
            background: #C65911;
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
        .text-danger {
            color: #dc3545;
            font-weight: bold;
        }
        .text-success {
            color: #28a745;
        }
        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: #28a745;
        }
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
        <h1>Outstanding Fees Report</h1>
        <p>Academic Year: {{ $activeYear->name ?? 'All Years' }}</p>
        <p>Generated on: {{ now()->format('d M Y, h:i A') }}</p>
    </div>

    <div class="summary-box">
        <div class="total-label">Total Outstanding Amount</div>
        <div class="total-value">{{ number_format($totalOutstanding, 2) }}</div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 20%;">Student Name</th>
                <th style="width: 12%;">Admission No</th>
                <th style="width: 10%;">Class</th>
                <th style="width: 13%;" class="text-right">Total Fee</th>
                <th style="width: 13%;" class="text-right">Paid</th>
                <th style="width: 13%;" class="text-right">Outstanding</th>
                <th style="width: 14%;" class="text-center">Progress</th>
            </tr>
        </thead>
        <tbody>
            @foreach($outstandingData as $index => $data)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $data['student']->full_name }}</td>
                    <td>{{ $data['student']->admission_no }}</td>
                    <td>{{ $data['student']->schoolClass->name ?? 'N/A' }}</td>
                    <td class="text-right">{{ number_format($data['total_fee'], 2) }}</td>
                    <td class="text-right text-success">{{ number_format($data['paid_amount'], 2) }}</td>
                    <td class="text-right text-danger">{{ number_format($data['outstanding'], 2) }}</td>
                    <td class="text-center">
                        @php
                            $percentage = $data['total_fee'] > 0 ? round(($data['paid_amount'] / $data['total_fee']) * 100) : 0;
                        @endphp
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $percentage }}%; background: {{ $percentage >= 80 ? '#28a745' : ($percentage >= 50 ? '#ffc107' : '#dc3545') }};"></div>
                        </div>
                        <small>{{ $percentage }}%</small>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        School Management System - Outstanding Fees Report
    </div>
</body>
</html>
