<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Marksheet - {{ $student->full_name }}</title>
    <style>
        @page { margin: 25px 35px; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #2c3e50;
        }
        .marksheet {
            border: 3px double #2c3e50;
            padding: 25px 35px;
            min-height: 90vh;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 12px;
            border-bottom: 2px solid #2c3e50;
        }
        .header h1 {
            font-size: 20px;
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 3px;
        }
        .header .contact {
            font-size: 10px;
            color: #666;
        }
        .header .title {
            font-size: 16px;
            font-weight: bold;
            color: #2980b9;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 10px;
        }
        .exam-info {
            text-align: center;
            margin-bottom: 15px;
            font-size: 12px;
            color: #555;
        }
        .student-info {
            margin-bottom: 15px;
        }
        .student-info table {
            width: 100%;
        }
        .student-info td {
            padding: 4px 5px;
            font-size: 11px;
        }
        .student-info .label {
            font-weight: bold;
            color: #2c3e50;
            width: 20%;
        }
        .student-info .value {
            width: 30%;
        }
        .marks-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .marks-table th {
            background: #2c3e50;
            color: #fff;
            padding: 10px 8px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            text-align: center;
        }
        .marks-table th:nth-child(2) { text-align: left; }
        .marks-table td {
            padding: 8px;
            border-bottom: 1px solid #dee2e6;
            font-size: 11px;
            text-align: center;
        }
        .marks-table td:nth-child(2) { text-align: left; font-weight: 500; }
        .marks-table tr:nth-child(even) { background: #f8f9fc; }
        .marks-table .total-row td {
            background: #edf2fb;
            font-weight: bold;
            font-size: 12px;
            border-top: 2px solid #2c3e50;
        }
        .result-box {
            margin: 15px 0;
            padding: 14px;
            text-align: center;
            border-radius: 4px;
        }
        .result-pass {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .result-fail {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .result-text {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .result-details {
            margin-top: 5px;
            font-size: 12px;
        }
        .grade-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            font-size: 9px;
        }
        .grade-table th, .grade-table td {
            border: 1px solid #dee2e6;
            padding: 3px 6px;
            text-align: center;
        }
        .grade-table th {
            background: #f4f6f9;
            font-weight: 600;
            color: #555;
        }
        .signatures {
            margin-top: 50px;
            display: table;
            width: 100%;
        }
        .sig-block {
            display: table-cell;
            text-align: center;
            vertical-align: bottom;
            padding-top: 45px;
        }
        .sig-line {
            border-top: 1px solid #2c3e50;
            display: inline-block;
            width: 130px;
            margin-bottom: 4px;
        }
        .sig-label {
            font-size: 10px;
            font-weight: bold;
            color: #2c3e50;
        }
        .sig-name {
            font-size: 9px;
            color: #7f8c8d;
        }
        .footer-note {
            margin-top: 20px;
            font-size: 9px;
            color: #95a5a6;
            text-align: center;
            border-top: 1px solid #e9ecef;
            padding-top: 8px;
        }
    </style>
</head>
<body>
    <div class="marksheet">
        <!-- Header -->
        <div class="header">
            <h1>{{ $school['name'] }}</h1>
            @if($school['address'])
                <div class="contact">{{ $school['address'] }}</div>
            @endif
            <div class="title">Statement of Marks</div>
        </div>

        <!-- Exam Info -->
        <div class="exam-info">
            <strong>{{ $exam->name }}</strong>
            @if($exam->examType)
                ({{ $exam->examType->name }})
            @endif
            @if($exam->academicYear)
                &mdash; Session: {{ $exam->academicYear->name }}
            @elseif($student->academicYear)
                &mdash; Session: {{ $student->academicYear->name }}
            @endif
        </div>

        <!-- Student Info — only show fields that have data -->
        <div class="student-info">
            <table>
                <tr>
                    <td class="label">Student Name:</td>
                    <td class="value">{{ $student->full_name }}</td>
                    <td class="label">Admission No:</td>
                    <td class="value">{{ $student->admission_no }}</td>
                </tr>
                <tr>
                    <td class="label">Class:</td>
                    <td class="value">{{ $student->schoolClass->name ?? '' }}{{ $student->section ? ' (' . $student->section->name . ')' : '' }}</td>
                    @if($student->roll_no)
                        <td class="label">Roll No:</td>
                        <td class="value">{{ $student->roll_no }}</td>
                    @else
                        <td></td><td></td>
                    @endif
                </tr>
                <tr>
                    @if($student->date_of_birth)
                        <td class="label">Date of Birth:</td>
                        <td class="value">{{ $student->date_of_birth->format('d M Y') }}</td>
                    @else
                        <td></td><td></td>
                    @endif
                    @if($student->gender)
                        <td class="label">Gender:</td>
                        <td class="value">{{ ucfirst($student->gender) }}</td>
                    @else
                        <td></td><td></td>
                    @endif
                </tr>
            </table>
        </div>

        <!-- Marks Table -->
        <table class="marks-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 35%;">Subject</th>
                    <th style="width: 15%;">Full Marks</th>
                    <th style="width: 15%;">Marks Obtained</th>
                    <th style="width: 15%;">Percentage</th>
                    <th style="width: 15%;">Grade</th>
                </tr>
            </thead>
            <tbody>
                @foreach($marks as $index => $mark)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $mark->subject->name ?? 'Subject' }}</td>
                        <td>{{ number_format($mark->full_marks, 0) }}</td>
                        <td>{{ number_format($mark->marks_obtained, 0) }}</td>
                        <td>{{ $mark->full_marks > 0 ? number_format(($mark->marks_obtained / $mark->full_marks) * 100, 1) : 0 }}%</td>
                        <td>{{ $mark->grade ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td></td>
                    <td style="text-align: left;">Grand Total</td>
                    <td>{{ number_format($totalFullMarks, 0) }}</td>
                    <td>{{ number_format($totalMarks, 0) }}</td>
                    <td>{{ $percentage }}%</td>
                    <td>{{ $grade }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Result Box -->
        <div class="result-box {{ $result === 'PASS' ? 'result-pass' : 'result-fail' }}">
            <div class="result-text">{{ $result }}</div>
            <div class="result-details">
                Total: {{ number_format($totalMarks, 0) }} / {{ number_format($totalFullMarks, 0) }}
                &nbsp;&bull;&nbsp; Percentage: {{ $percentage }}%
                &nbsp;&bull;&nbsp; Grade: {{ $grade }}
            </div>
        </div>

        <!-- Grade Scale Reference -->
        <table class="grade-table">
            <tr>
                <th>Range</th>
                <th>90%+</th>
                <th>80-89%</th>
                <th>70-79%</th>
                <th>60-69%</th>
                <th>50-59%</th>
                <th>40-49%</th>
                <th>33-39%</th>
                <th>&lt;33%</th>
            </tr>
            <tr>
                <td><strong>Grade</strong></td>
                <td>A+</td>
                <td>A</td>
                <td>B+</td>
                <td>B</td>
                <td>C+</td>
                <td>C</td>
                <td>D</td>
                <td>F</td>
            </tr>
        </table>

        <!-- Signatures -->
        <div class="signatures">
            <div class="sig-block" style="text-align: left;">
                <div class="sig-line"></div>
                <div class="sig-label">Class Teacher</div>
            </div>
            <div class="sig-block">
                @if($school['stamp_url'] && file_exists($school['stamp_url']))
                    <img src="{{ $school['stamp_url'] }}" alt="Seal" style="max-height: 70px; max-width: 70px; margin-bottom: 5px;">
                    <br>
                @else
                    <div class="sig-line"></div>
                @endif
                <div class="sig-label">Exam Controller</div>
            </div>
            <div class="sig-block" style="text-align: right;">
                @if($school['signature_url'] && file_exists($school['signature_url']))
                    <img src="{{ $school['signature_url'] }}" alt="Signature" style="max-height: 50px; margin-bottom: 5px;">
                    <br>
                @else
                    <div class="sig-line"></div>
                @endif
                <div class="sig-label">Principal</div>
                @if($school['principal'])
                    <div class="sig-name">{{ $school['principal'] }}</div>
                @endif
            </div>
        </div>

        <!-- Footer -->
        <div class="footer-note">
            Issued by {{ $school['name'] }} on {{ now()->format('d M Y') }} &bull; This is a computer-generated marksheet.
        </div>
    </div>
</body>
</html>
