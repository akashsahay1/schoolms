@extends('layouts.app')

@section('title', 'Attendance Reports')

@section('page-title', 'Attendance Reports')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Attendance Reports</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Generate Attendance Report</h5>
                <p class="mb-0">Academic Year: {{ $activeYear ? $activeYear->name : 'No Active Academic Year' }}</p>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.attendance.reports') }}" class="row g-3 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label">Report Type</label>
                        <select name="report_type" class="form-select" id="report-type">
                            <option value="monthly" {{ $reportType == 'monthly' ? 'selected' : '' }}>Monthly Summary</option>
                            <option value="daily" {{ $reportType == 'daily' ? 'selected' : '' }}>Daily Report</option>
                        </select>
                    </div>
                    <div class="col-md-2" id="month-field" style="{{ $reportType == 'daily' ? 'display: none;' : '' }}">
                        <label class="form-label">Month</label>
                        <select name="month" class="form-select">
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $selectedMonth == $i ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2" id="year-field" style="{{ $reportType == 'daily' ? 'display: none;' : '' }}">
                        <label class="form-label">Year</label>
                        <select name="year" class="form-select">
                            @for($year = date('Y') - 2; $year <= date('Y') + 1; $year++)
                                <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2" id="date-field" style="{{ $reportType == 'monthly' ? 'display: none;' : '' }}">
                        <label class="form-label">Date</label>
                        <input type="date" name="date" class="form-control" value="{{ request('date', date('Y-m-d')) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Class <span class="text-danger">*</span></label>
                        <select name="class_id" class="form-select" id="class-select" required>
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Section</label>
                        <select name="section_id" class="form-select" id="section-select">
                            <option value="">All Sections</option>
                            @if($selectedClass)
                                @foreach($selectedClass->sections as $section)
                                    <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>
                                        {{ $section->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="bar-chart-2" class="me-1"></i> Generate
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if($reportData->count() > 0)
            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>
                        @if($reportType == 'daily')
                            Daily Attendance Report - {{ $selectedClass->name }}{{ $selectedSection ? ' - Section ' . $selectedSection->name : '' }}
                        @else
                            Monthly Attendance Summary - {{ $selectedClass->name }}{{ $selectedSection ? ' - Section ' . $selectedSection->name : '' }}
                        @endif
                    </h5>
                    <div>
                        <button type="button" class="btn btn-outline-success btn-sm me-1" onclick="exportToExcel()">
                            <i data-feather="download" class="icon-xs"></i> Export
                        </button>
                        <button type="button" class="btn btn-success btn-sm" onclick="window.print()">
                            <i data-feather="printer" class="icon-xs"></i> Print
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($reportType == 'daily')
                        <div class="mb-3">
                            <strong>Date:</strong> {{ Carbon\Carbon::parse(request('date'))->format('d M Y, l') }}
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Roll No.</th>
                                        <th>Student Name</th>
                                        <th>Status</th>
                                        <th>Check In Time</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reportData as $attendance)
                                        <tr>
                                            <td>{{ $attendance->student->roll_no }}</td>
                                            <td>{{ $attendance->student->full_name }}</td>
                                            <td>{!! $attendance->status_badge !!}</td>
                                            <td>{{ $attendance->check_in_time ?? 'N/A' }}</td>
                                            <td>{{ $attendance->remarks ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="mb-3">
                            <strong>Month:</strong> {{ date('F Y', mktime(0, 0, 0, $selectedMonth, 1, $selectedYear)) }}
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Roll No.</th>
                                        <th>Student Name</th>
                                        <th>Total Days</th>
                                        <th>Present</th>
                                        <th>Absent</th>
                                        <th>Late</th>
                                        <th>Half Day</th>
                                        <th>Attendance %</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reportData as $summary)
                                        <tr>
                                            <td>{{ $summary->student->roll_no }}</td>
                                            <td>{{ $summary->student->full_name }}</td>
                                            <td>{{ $summary->total_days }}</td>
                                            <td><span class="badge badge-light-success">{{ $summary->present_days }}</span></td>
                                            <td><span class="badge badge-light-danger">{{ $summary->absent_days }}</span></td>
                                            <td><span class="badge badge-light-warning">{{ $summary->late_days }}</span></td>
                                            <td><span class="badge badge-light-info">{{ $summary->half_days }}</span></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress me-2" style="width: 60px; height: 6px;">
                                                        <div class="progress-bar {{ $summary->attendance_percentage >= 75 ? 'bg-success' : ($summary->attendance_percentage >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                                             style="width: {{ $summary->attendance_percentage }}%"></div>
                                                    </div>
                                                    <strong>{{ number_format($summary->attendance_percentage, 1) }}%</strong>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6>Summary Statistics</h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <h4 class="text-success">{{ $reportData->avg('attendance_percentage') ? number_format($reportData->avg('attendance_percentage'), 1) : 0 }}%</h4>
                                                    <p class="mb-0">Class Average</p>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <h4 class="text-primary">{{ $reportData->count() }}</h4>
                                                    <p class="mb-0">Total Students</p>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <h4 class="text-success">{{ $reportData->where('attendance_percentage', '>=', 75)->count() }}</h4>
                                                    <p class="mb-0">Good Attendance (≥75%)</p>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <h4 class="text-danger">{{ $reportData->where('attendance_percentage', '<', 50)->count() }}</h4>
                                                    <p class="mb-0">Poor Attendance (<50%)</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @elseif(request('class_id'))
            <div class="card mt-3">
                <div class="card-body text-center py-5">
                    <div class="mb-3">
                        <i data-feather="calendar" class="icon-lg text-muted"></i>
                    </div>
                    <h5 class="text-muted">No Attendance Data Found</h5>
                    <p class="text-muted">
                        No attendance records found for the selected criteria.
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Classes data for section filtering
    const classesData = @json($classes);

    jQuery(document).ready(function() {
        // Toggle fields based on report type
        jQuery('#report-type').on('change', function() {
            var reportType = jQuery(this).val();

            if (reportType === 'daily') {
                jQuery('#month-field, #year-field').hide();
                jQuery('#date-field').show();
            } else {
                jQuery('#date-field').hide();
                jQuery('#month-field, #year-field').show();
            }
        });

        // Load sections when class changes
        jQuery('#class-select').on('change', function() {
            var classId = jQuery(this).val();
            var sectionSelect = jQuery('#section-select');

            sectionSelect.html('<option value="">All Sections</option>');

            if (classId) {
                var selectedClass = classesData.find(c => c.id == classId);
                if (selectedClass && selectedClass.sections) {
                    selectedClass.sections.forEach(function(section) {
                        sectionSelect.append('<option value="' + section.id + '">' + section.name + '</option>');
                    });
                }
            }
        });
    });

    // Export table to Excel
    function exportToExcel() {
        var table = document.querySelector('.card-body .table');
        if (!table) {
            alert('No data to export');
            return;
        }

        var html = table.outerHTML;
        var url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
        var downloadLink = document.createElement('a');
        downloadLink.href = url;
        downloadLink.download = 'attendance_report_{{ date("Y-m-d") }}.xls';
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
    }
</script>
@endpush

@push('styles')
<style>
    @media print {
        .btn, .breadcrumb, .card-header button {
            display: none !important;
        }
        
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        
        body {
            font-size: 12px;
        }
        
        .table {
            font-size: 11px;
        }
    }
</style>
@endpush