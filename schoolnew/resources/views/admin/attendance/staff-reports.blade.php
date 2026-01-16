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
                <form method="GET" action="{{ route('admin.staff-attendance.reports') }}" class="row g-3 align-items-end" id="report-form">
                    <input type="hidden" name="generate" value="1">
                    <div class="col-md-2">
                        <label class="form-label">Quick Filter</label>
                        <select class="form-select" id="quick-filter">
                            <option value="">Custom Range</option>
                            <option value="this_month" {{ request('period') == 'this_month' ? 'selected' : '' }}>This Month</option>
                            <option value="last_month" {{ request('period') == 'last_month' ? 'selected' : '' }}>Last Month</option>
                            <option value="this_quarter" {{ request('period') == 'this_quarter' ? 'selected' : '' }}>This Quarter</option>
                            <option value="last_quarter" {{ request('period') == 'last_quarter' ? 'selected' : '' }}>Last Quarter</option>
                            <option value="this_year" {{ request('period') == 'this_year' ? 'selected' : '' }}>This Year</option>
                            <option value="last_year" {{ request('period') == 'last_year' ? 'selected' : '' }}>Last Year</option>
                        </select>
                        <input type="hidden" name="period" id="period-input" value="{{ request('period') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">From Date <span class="text-danger">*</span></label>
                        <input type="date" name="from_date" id="from-date" class="form-control" value="{{ request('from_date', Carbon\Carbon::now()->startOfMonth()->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">To Date <span class="text-danger">*</span></label>
                        <input type="date" name="to_date" id="to-date" class="form-control" value="{{ request('to_date', Carbon\Carbon::now()->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-3">
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
                        Staff Attendance Report {{ $selectedDepartment ? '- ' . $selectedDepartment->name : '' }}
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
                    <div class="mb-3">
                        <strong>Period:</strong> {{ Carbon\Carbon::parse($fromDate)->format('d M Y') }} - {{ Carbon\Carbon::parse($toDate)->format('d M Y') }}
                        <span class="ms-3 text-muted">({{ Carbon\Carbon::parse($fromDate)->diffInDays(Carbon\Carbon::parse($toDate)) + 1 }} days)</span>
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
        // Quick filter functionality
        jQuery('#quick-filter').on('change', function() {
            var period = jQuery(this).val();
            var today = new Date();
            var fromDate, toDate;

            jQuery('#period-input').val(period);

            switch(period) {
                case 'this_month':
                    fromDate = new Date(today.getFullYear(), today.getMonth(), 1);
                    toDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                    break;
                case 'last_month':
                    fromDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                    toDate = new Date(today.getFullYear(), today.getMonth(), 0);
                    break;
                case 'this_quarter':
                    var quarter = Math.floor(today.getMonth() / 3);
                    fromDate = new Date(today.getFullYear(), quarter * 3, 1);
                    toDate = new Date(today.getFullYear(), quarter * 3 + 3, 0);
                    break;
                case 'last_quarter':
                    var quarter = Math.floor(today.getMonth() / 3) - 1;
                    var year = today.getFullYear();
                    if (quarter < 0) {
                        quarter = 3;
                        year--;
                    }
                    fromDate = new Date(year, quarter * 3, 1);
                    toDate = new Date(year, quarter * 3 + 3, 0);
                    break;
                case 'this_year':
                    fromDate = new Date(today.getFullYear(), 0, 1);
                    toDate = new Date(today.getFullYear(), 11, 31);
                    break;
                case 'last_year':
                    fromDate = new Date(today.getFullYear() - 1, 0, 1);
                    toDate = new Date(today.getFullYear() - 1, 11, 31);
                    break;
                default:
                    return;
            }

            jQuery('#from-date').val(formatDate(fromDate));
            jQuery('#to-date').val(formatDate(toDate));
        });

        function formatDate(date) {
            var year = date.getFullYear();
            var month = String(date.getMonth() + 1).padStart(2, '0');
            var day = String(date.getDate()).padStart(2, '0');
            return year + '-' + month + '-' + day;
        }
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
