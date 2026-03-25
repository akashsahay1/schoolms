<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transfer Certificate - {{ $student->full_name }}</title>
    <style>
        @page { margin: 30px 40px; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #2c3e50;
        }
        .certificate {
            border: 3px double #2c3e50;
            padding: 30px 40px;
            min-height: 90vh;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #2c3e50;
        }
        .header h1 {
            font-size: 22px;
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }
        .header .tagline {
            font-size: 10px;
            color: #7f8c8d;
            font-style: italic;
            margin-bottom: 4px;
        }
        .header .contact {
            font-size: 10px;
            color: #666;
        }
        .header .tc-title {
            font-size: 18px;
            font-weight: bold;
            color: #c0392b;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 14px;
        }
        .meta-row {
            margin-bottom: 20px;
            font-size: 11px;
            color: #555;
        }
        .meta-row .left { float: left; }
        .meta-row .right { float: right; }
        .meta-row::after { content: ''; display: table; clear: both; }
        .details-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .details-table td {
            padding: 8px 5px;
            vertical-align: top;
            font-size: 12px;
        }
        .details-table .sno {
            width: 5%;
            color: #7f8c8d;
        }
        .details-table .label {
            width: 35%;
            font-weight: bold;
            color: #2c3e50;
        }
        .details-table .value {
            width: 60%;
            border-bottom: 1px dotted #bdc3c7;
            padding-left: 10px;
        }
        .conduct-box {
            margin: 20px 0;
            padding: 12px 15px;
            background: #f8f9fa;
            border-left: 3px solid #2c3e50;
            font-size: 12px;
        }
        .result-box {
            margin: 15px 0;
            padding: 12px 15px;
            background: #edf7ed;
            border-left: 3px solid #27ae60;
            font-size: 12px;
        }
        .signatures {
            margin-top: 60px;
            display: table;
            width: 100%;
        }
        .sig-block {
            display: table-cell;
            text-align: center;
            vertical-align: bottom;
            padding-top: 50px;
        }
        .sig-line {
            border-top: 1px solid #2c3e50;
            display: inline-block;
            width: 140px;
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
            margin-top: 25px;
            font-size: 9px;
            color: #95a5a6;
            text-align: center;
            border-top: 1px solid #e9ecef;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <!-- Header -->
        <div class="header">
            <h1>{{ $school['name'] }}</h1>
            @if($school['address'])
                <div class="contact">{{ $school['address'] }}</div>
            @endif
            @if($school['phone'] || $school['email'])
                <div class="contact">
                    @if($school['phone'])Phone: {{ $school['phone'] }}@endif
                    @if($school['phone'] && $school['email']) &nbsp;|&nbsp; @endif
                    @if($school['email'])Email: {{ $school['email'] }}@endif
                </div>
            @endif
            <div class="tc-title">Transfer Certificate</div>
        </div>

        <!-- TC No & Date -->
        <div class="meta-row">
            <span class="left"><strong>TC No:</strong> {{ $tcNumber }}</span>
            <span class="right"><strong>Date:</strong> {{ now()->format('d M Y') }}</span>
        </div>

        <!-- Student Details — only show rows that have data -->
        @php
            $slNo = 0;
            $details = [
                ['Name of Student', $student->full_name],
                ['Admission Number', $student->admission_no],
                ['Date of Birth', $student->date_of_birth ? $student->date_of_birth->format('d M Y') . ' (' . \Carbon\Carbon::parse($student->date_of_birth)->locale('en')->isoFormat('Do MMMM YYYY') . ')' : null],
                ['Gender', $student->gender ? ucfirst($student->gender) : null],
            ];

            // Father/Guardian name from parent
            if ($student->parent) {
                $parentName = $student->parent->father_name ?? $student->parent->guardian_name ?? null;
                if ($parentName) $details[] = ["Father's / Guardian's Name", $parentName];

                $motherName = $student->parent->mother_name ?? null;
                if ($motherName) $details[] = ["Mother's Name", $motherName];
            }

            if ($student->nationality) $details[] = ['Nationality', $student->nationality];
            if ($student->religion) $details[] = ['Religion / Caste', $student->religion . ($student->caste ? ' / ' . $student->caste : '')];

            $details[] = ['Class at Time of Leaving', ($student->schoolClass->name ?? '') . ($student->section ? ' (' . $student->section->name . ')' : '')];

            if ($student->admission_date) $details[] = ['Date of Admission', $student->admission_date->format('d M Y')];

            $details[] = ['Date of Leaving', $student->leaving_date ? $student->leaving_date->format('d M Y') : now()->format('d M Y')];

            if ($student->leaving_reason) $details[] = ['Reason for Leaving', $student->leaving_reason];

            if ($student->academicYear) $details[] = ['Academic Session', $student->academicYear->name];
        @endphp

        <table class="details-table">
            @foreach($details as $detail)
                @if($detail[1])
                    @php $slNo++; @endphp
                    <tr>
                        <td class="sno">{{ $slNo }}.</td>
                        <td class="label">{{ $detail[0] }}</td>
                        <td class="value">{{ $detail[1] }}</td>
                    </tr>
                @endif
            @endforeach
        </table>

        <!-- Result -->
        <div class="result-box">
            <strong>Result:</strong> {{ $resultText }}
        </div>

        <!-- Conduct -->
        <div class="conduct-box">
            <strong>General Conduct:</strong> The general conduct of the student during the stay in the school was <strong>Good</strong>.
        </div>

        <!-- Signatures -->
        <div class="signatures">
            <div class="sig-block" style="text-align: left;">
                @if($school['class_teacher_signature_url'] && file_exists($school['class_teacher_signature_url']))
                    <img src="{{ $school['class_teacher_signature_url'] }}" alt="Signature" style="max-height: 50px; margin-bottom: 5px;">
                    <br>
                @else
                    <div class="sig-line"></div>
                @endif
                <div class="sig-label">Class Teacher</div>
                @if($school['class_teacher'])
                    <div class="sig-name">{{ $school['class_teacher'] }}</div>
                @endif
            </div>
            <div class="sig-block">
                @if($school['stamp_url'] && file_exists($school['stamp_url']))
                    <img src="{{ $school['stamp_url'] }}" alt="Seal" style="max-height: 70px; max-width: 70px; margin-bottom: 5px;">
                    <br>
                @else
                    <div class="sig-line"></div>
                @endif
                <div class="sig-label">School Seal</div>
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
            This is a computer-generated Transfer Certificate issued by {{ $school['name'] }} on {{ now()->format('d M Y') }}.
        </div>
    </div>
</body>
</html>
