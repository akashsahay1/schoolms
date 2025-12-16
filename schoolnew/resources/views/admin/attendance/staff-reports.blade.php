@extends('layouts.app')

@section('title', 'Staff Attendance Reports')

@section('page-title', 'Staff Attendance Reports')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Staff Attendance Reports</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Generate Staff Attendance Report</h5>
                <p class="mb-0">Academic Year: {{ $activeYear ? $activeYear->name : 'No Active Academic Year' }}</p>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.staff-attendance.reports') }}" class="row g-3 align-items-end">
                    <input type="hidden" name="generate" value="1">
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
                        <label class="form-label">Department</label>
                        <select name="department_id" class="form-select">
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
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
                            Daily Staff Attendance Report {{ $selectedDepartment ? '- ' . $selectedDepartment->name : '' }}
                        @else
                            Monthly Staff Attendance Summary {{ $selectedDepartment ? '- ' . $selectedDepartment->name : '' }}
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
                            <table class="table table-hover" id="report-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Staff Name</th>
                                        <th>Department</th>
                                        <th>Status</th>
                                        <th>Check In</th>
                                        <th>Check Out</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reportData as $index => $attendance)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $attendance->staff->full_name }}</td>
                                            <td>{{ $attendance->staff->department->name ?? 'N/A' }}</td>
                                            <td>{!! $attendance->status_badge !!}</td>
                                            <td>{{ $attendance->check_in_time ? $attendance->check_in_time->format('H:i') : 'N/A' }}</td>
                                            <td>{{ $attendance->check_out_time ? $attendance->check_out_time->format('H:i') : 'N/A' }}</td>
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
                            <table class="table table-hover" id="report-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Staff Name</th>
                                        <th>Department</th>
                                        <th>Total Days</th>
                                        <th>Present</th>
                                        <th>Absent</th>
                                        <th>Late</th>
                                        <th>Half Day</th>
                                        <th>On Leave</th>
                                        <th>Attendance %</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reportData as $index => $summary)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $summary->staff->full_name }}</td>
                                            <td>{{ $summary->staff->department->name ?? 'N/A' }}</td>
                                            <td>{{ $summary->total_days }}</td>
                                            <td><span class="badge badge-light-success">{{ $summary->present_days }}</span></td>
                                            <td><span class="badge badge-light-danger">{{ $summary->absent_days }}</span></td>
                                            <td><span class="badge badge-light-warning">{{ $summary->late_days }}</span></td>
                                            <td><span class="badge badge-light-info">{{ $summary->half_days }}</span></td>
                                            <td><span class="badge badge-light-secondary">{{ $summary->leave_days }}</span></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress me-2" style="width: 60px; height: 6px;">
                                                        <div class="progress-bar {{ $summary->attendance_percentage >= 75 ? 'bg-success' : ($summary->attendance_percentage >= 50 ? 'bg-warning' : 'bg-danger') }}" style="width: {{ $summary->attendance_percentage }}%"></div>
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
                                                    <p class="mb-0">Average Attendance</p>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <h4 class="text-primary">{{ $reportData->count() }}</h4>
                                                    <p class="mb-0">Total Staff</p>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <h4 class="text-success">{{ $reportData->where('attendance_percentage', '>=', 75)->count() }}</h4>
                                                    <p class="mb-0">Good Attendance (>=75%)</p>
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
        @elseif(request()->has('generate'))
            <div class="card mt-3">
                <div class="card-body text-center py-5">
                    <div class="mb-3">
                        <i data-feather="calendar" class="icon-lg text-muted"></i>
                    </div>
                    <h5 class="text-muted">No Attendance Data Found</h5>
                    <p class="text-muted">No attendance records found for the selected criteria.</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
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
    });

    // Export table to Excel
    function exportToExcel() {
        var table = document.getElementById('report-table');
        if (!table) {
            alert('No data to export');
            return;
        }

        var html = table.outerHTML;
        var url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
        var downloadLink = document.createElement('a');
        downloadLink.href = url;
        downloadLink.download = 'staff_attendance_report_{{ date("Y-m-d") }}.xls';
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
