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
                        <div class="col-md-3">
                            <label class="form-label">Month</label>
                            <select name="month" class="form-select">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3">
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
                    <p class="mb-0 text-primary">Attendance Rate</p>
                </div>
            </div>
        </div>
    </div>

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
