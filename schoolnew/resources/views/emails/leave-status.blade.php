<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Application Status</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #7366ff 0%, #5a52d5 100%);
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header .icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
        }
        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
        }
        .details-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        .details-table td:first-child {
            font-weight: 600;
            color: #666;
            width: 40%;
        }
        .remarks-box {
            background-color: #f8f9fa;
            border-left: 4px solid #7366ff;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }
        .remarks-box h4 {
            margin: 0 0 10px 0;
            color: #7366ff;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            font-size: 13px;
            color: #666;
        }
        .footer a {
            color: #7366ff;
            text-decoration: none;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #7366ff 0%, #5a52d5 100%);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="icon">
                @if($status === 'approved')
                    ✓
                @else
                    ✗
                @endif
            </div>
            <h1>Leave Application {{ ucfirst($status) }}</h1>
        </div>

        <div class="content">
            <p class="greeting">Dear {{ $leave->student->first_name ?? 'Student' }},</p>

            <p>
                Your leave application has been
                <span class="status-badge status-{{ $status }}">{{ $status }}</span>
            </p>

            <table class="details-table">
                <tr>
                    <td>Leave Type</td>
                    <td>{{ $leave->getLeaveTypeLabel() }}</td>
                </tr>
                <tr>
                    <td>From Date</td>
                    <td>{{ $leave->from_date->format('l, F d, Y') }}</td>
                </tr>
                <tr>
                    <td>To Date</td>
                    <td>{{ $leave->to_date->format('l, F d, Y') }}</td>
                </tr>
                <tr>
                    <td>Total Days</td>
                    <td>{{ $leave->total_days }} day(s)</td>
                </tr>
                <tr>
                    <td>{{ $status === 'approved' ? 'Approved' : 'Reviewed' }} By</td>
                    <td>{{ $leave->approvedByUser->name ?? 'Administrator' }}</td>
                </tr>
                <tr>
                    <td>{{ $status === 'approved' ? 'Approved' : 'Reviewed' }} On</td>
                    <td>{{ $leave->approved_at ? $leave->approved_at->format('F d, Y h:i A') : now()->format('F d, Y h:i A') }}</td>
                </tr>
            </table>

            @if($leave->admin_remarks)
                <div class="remarks-box">
                    <h4>Admin Remarks</h4>
                    <p style="margin: 0;">{{ $leave->admin_remarks }}</p>
                </div>
            @endif

            @if($status === 'approved')
                <p>Your leave has been approved. Please ensure all necessary arrangements are made before your leave period begins.</p>
            @else
                <p>Unfortunately, your leave application could not be approved. Please contact the school administration if you have any questions or need further clarification.</p>
            @endif

            <center>
                <a href="{{ url('/portal/leaves/' . $leave->id) }}" class="btn">View Application</a>
            </center>
        </div>

        <div class="footer">
            <p>This is an automated email from {{ config('app.name') }}.</p>
            <p>If you have any questions, please contact the school administration.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
