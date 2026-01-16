<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timetable - {{ $selectedClass->name ?? '' }} {{ $selectedSection->name ?? '' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            padding: 20px;
            background: white;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
            color: #333;
        }
        .header h2 {
            font-size: 18px;
            font-weight: normal;
            color: #666;
        }
        .header p {
            font-size: 14px;
            color: #888;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #333;
            padding: 8px 5px;
            text-align: center;
            vertical-align: middle;
        }
        th {
            background: #4169e1;
            color: white;
            font-weight: bold;
            font-size: 11px;
        }
        th:first-child {
            width: 80px;
        }
        th:nth-child(2) {
            width: 80px;
        }
        .subject-name {
            font-weight: bold;
            color: #333;
        }
        .teacher-name {
            font-size: 10px;
            color: #666;
            margin-top: 2px;
        }
        .room-info {
            font-size: 9px;
            color: #888;
        }
        .break-row td {
            background: #f5f5f5;
            color: #666;
            font-style: italic;
        }
        .time-cell {
            font-size: 10px;
            color: #666;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #888;
        }
        @media print {
            body {
                padding: 10px;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; background: #4169e1; color: white; border: none; border-radius: 5px;">
            Print Timetable
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer; background: #666; color: white; border: none; border-radius: 5px; margin-left: 10px;">
            Close
        </button>
    </div>

    <div class="header">
        <h1>{{ config('app.name', 'School Management System') }}</h1>
        <h2>Class Timetable</h2>
        <p><strong>Class:</strong> {{ $selectedClass->name ?? '-' }} - {{ $selectedSection->name ?? '-' }} | <strong>Academic Year:</strong> {{ $activeYear->name ?? '-' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Period</th>
                <th>Time</th>
                @foreach($days as $day)
                    <th>{{ ucfirst($day) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($periods as $period)
                <tr class="{{ $period->type !== 'class' ? 'break-row' : '' }}">
                    <td><strong>{{ $period->name }}</strong></td>
                    <td class="time-cell">
                        {{ \Carbon\Carbon::parse($period->start_time)->format('h:i A') }}<br>
                        {{ \Carbon\Carbon::parse($period->end_time)->format('h:i A') }}
                    </td>
                    @foreach($days as $day)
                        <td>
                            @if($period->type === 'break' || $period->type === 'lunch')
                                {{ ucfirst($period->type) }}
                            @else
                                @php
                                    $entry = $timetableData->get($day)?->firstWhere('period_id', $period->id);
                                @endphp
                                @if($entry)
                                    <div class="subject-name">{{ $entry->subject->name ?? '-' }}</div>
                                    <div class="teacher-name">{{ $entry->teacher->first_name ?? '' }} {{ $entry->teacher->last_name ?? '' }}</div>
                                    @if($entry->room)
                                        <div class="room-info">Room: {{ $entry->room }}</div>
                                    @endif
                                @else
                                    -
                                @endif
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated on: {{ now()->format('d M, Y h:i A') }}
    </div>
</body>
</html>
