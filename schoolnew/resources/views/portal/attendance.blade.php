@extends('layouts.portal')

@section('title', 'Attendance')
@section('page-title', 'My Attendance')

@section('breadcrumb')
    <li class="breadcrumb-item active">Attendance</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Filter -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('portal.attendance') }}" class="row g-3 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label">View Type</label>
                            <select name="view" class="form-select" id="view-type">
                                <option value="monthly" {{ ($viewType ?? 'monthly') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="yearly" {{ ($viewType ?? 'monthly') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                            </select>
                        </div>
                        <div class="col-md-2" id="month-field" style="{{ ($viewType ?? 'monthly') == 'yearly' ? 'display: none;' : '' }}">
                            <label class="form-label">Month</label>
                            <select name="month" class="form-select">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Year</label>
                            <select name="year" class="form-select">
                                @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">View Attendance</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="row">
        <div class="col-xl-3 col-sm-6">
            <div class="card bg-light-success">
                <div class="card-body text-center">
                    <h3 class="text-success">{{ $stats['present'] }}</h3>
                    <p class="mb-0 text-success">Days Present</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card bg-light-danger">
                <div class="card-body text-center">
                    <h3 class="text-danger">{{ $stats['absent'] }}</h3>
                    <p class="mb-0 text-danger">Days Absent</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card bg-light-warning">
                <div class="card-body text-center">
                    <h3 class="text-warning">{{ $stats['late'] }}</h3>
                    <p class="mb-0 text-warning">Days Late</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card bg-light-primary">
                <div class="card-body text-center">
                    <h3 class="text-primary">{{ $stats['percentage'] }}%</h3>
                    <p class="mb-0 text-primary">{{ ($viewType ?? 'monthly') == 'yearly' ? 'Yearly' : 'Monthly' }} Attendance Rate</p>
                </div>
            </div>
        </div>
    </div>

    @if(($viewType ?? 'monthly') == 'yearly')
        <!-- Yearly Month-wise Breakdown -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h5>Yearly Attendance Report - {{ $year }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Month</th>
                                        <th class="text-center">Total Days</th>
                                        <th class="text-center">Present</th>
                                        <th class="text-center">Absent</th>
                                        <th class="text-center">Late</th>
                                        <th class="text-center">Half Day</th>
                                        <th class="text-center">Attendance %</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @for($m = 1; $m <= 12; $m++)
                                        @php $monthData = $monthlyBreakdown[$m] ?? null; @endphp
                                        <tr>
                                            <td><strong>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</strong></td>
                                            <td class="text-center">{{ $monthData['total'] ?? 0 }}</td>
                                            <td class="text-center">
                                                @if(($monthData['present'] ?? 0) > 0)
                                                    <span class="badge badge-light-success">{{ $monthData['present'] }}</span>
                                                @else
                                                    <span class="text-muted">0</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if(($monthData['absent'] ?? 0) > 0)
                                                    <span class="badge badge-light-danger">{{ $monthData['absent'] }}</span>
                                                @else
                                                    <span class="text-muted">0</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if(($monthData['late'] ?? 0) > 0)
                                                    <span class="badge badge-light-warning">{{ $monthData['late'] }}</span>
                                                @else
                                                    <span class="text-muted">0</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if(($monthData['half_day'] ?? 0) > 0)
                                                    <span class="badge badge-light-info">{{ $monthData['half_day'] }}</span>
                                                @else
                                                    <span class="text-muted">0</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if(($monthData['total'] ?? 0) > 0)
                                                    <div class="d-flex align-items-center justify-content-center">
                                                        <div class="progress me-2" style="width: 60px; height: 6px;">
                                                            <div class="progress-bar {{ $monthData['percentage'] >= 75 ? 'bg-success' : ($monthData['percentage'] >= 50 ? 'bg-warning' : 'bg-danger') }}" style="width: {{ $monthData['percentage'] }}%"></div>
                                                        </div>
                                                        <strong>{{ number_format($monthData['percentage'], 1) }}%</strong>
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endfor
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th>Total</th>
                                        <th class="text-center">{{ $stats['total'] }}</th>
                                        <th class="text-center"><span class="badge badge-light-success">{{ $stats['present'] }}</span></th>
                                        <th class="text-center"><span class="badge badge-light-danger">{{ $stats['absent'] }}</span></th>
                                        <th class="text-center"><span class="badge badge-light-warning">{{ $stats['late'] }}</span></th>
                                        <th class="text-center"><span class="badge badge-light-info">{{ $stats['half_day'] }}</span></th>
                                        <th class="text-center">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <div class="progress me-2" style="width: 60px; height: 6px;">
                                                    <div class="progress-bar {{ $stats['percentage'] >= 75 ? 'bg-success' : ($stats['percentage'] >= 50 ? 'bg-warning' : 'bg-danger') }}" style="width: {{ $stats['percentage'] }}%"></div>
                                                </div>
                                                <strong>{{ number_format($stats['percentage'], 1) }}%</strong>
                                            </div>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Calendar -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h5>{{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered text-center">
                                <thead class="bg-light text-dark">
                                    <tr>
                                        <th style="width: 14.28%;">Sun</th>
                                        <th style="width: 14.28%;">Mon</th>
                                        <th style="width: 14.28%;">Tue</th>
                                        <th style="width: 14.28%;">Wed</th>
                                        <th style="width: 14.28%;">Thu</th>
                                        <th style="width: 14.28%;">Fri</th>
                                        <th style="width: 14.28%;">Sat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($calendarData as $week)
                                        <tr>
                                            @foreach($week as $day)
                                                <td class="{{ !$day['inMonth'] ? 'text-muted bg-light' : '' }} {{ $day['isToday'] ? 'border-primary border-2' : '' }}" style="height: 60px; vertical-align: middle;">
                                                    @if($day['inMonth'])
                                                        <div class="mb-1">{{ $day['day'] }}</div>
                                                        @if($day['attendance'])
                                                            @php
                                                                $statusColors = [
                                                                    'present' => 'success',
                                                                    'absent' => 'danger',
                                                                    'late' => 'warning',
                                                                    'half_day' => 'info',
                                                                ];
                                                                $color = $statusColors[$day['attendance']->status] ?? 'secondary';
                                                            @endphp
                                                            <span class="badge badge-light-{{ $color }}">
                                                                {{ ucfirst(str_replace('_', ' ', $day['attendance']->status)) }}
                                                            </span>
                                                        @elseif($day['isSunday'])
                                                            <span class="text-muted small">Holiday</span>
                                                        @endif
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Legend -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-3">
                        <span><span class="badge badge-light-success">Present</span> - Attended full day</span>
                        <span><span class="badge badge-light-danger">Absent</span> - Did not attend</span>
                        <span><span class="badge badge-light-warning">Late</span> - Arrived late</span>
                        <span><span class="badge badge-light-info">Half Day</span> - Attended half day</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    jQuery(document).ready(function() {
        jQuery('#view-type').on('change', function() {
            var viewType = jQuery(this).val();
            if (viewType === 'yearly') {
                jQuery('#month-field').hide();
            } else {
                jQuery('#month-field').show();
            }
        });
    });
</script>
@endpush
