@extends('layouts.portal')

@section('title', 'Attendance')
@section('page-title', 'My Attendance')

@section('breadcrumb')
	<li class="breadcrumb-item active">Attendance</li>
@endsection

@section('content')
<div class="container-fluid">
	<!-- Help Tip -->
	<div class="row mb-4">
		<div class="col-12">
			<div class="help-tip">
				<i data-feather="info" class="me-2 text-primary" style="width: 18px; height: 18px;"></i>
				<strong>Track Your Attendance:</strong> View monthly or yearly attendance records. Green indicates present days, red for absent, and yellow for late arrivals. Maintain above 75% attendance for good standing.
			</div>
		</div>
	</div>

	<!-- Filter Card -->
	<div class="row mb-4">
		<div class="col-12">
			<div class="card">
				<div class="card-body py-3">
					<form method="GET" action="{{ route('portal.attendance') }}" class="row g-3 align-items-end">
						<div class="col-md-3 col-6">
							<label class="form-label">
								<i data-feather="eye" style="width: 14px; height: 14px;"></i> View Type
							</label>
							<select name="view" class="form-select" id="view-type">
								<option value="monthly" {{ ($viewType ?? 'monthly') == 'monthly' ? 'selected' : '' }}>Monthly View</option>
								<option value="yearly" {{ ($viewType ?? 'monthly') == 'yearly' ? 'selected' : '' }}>Yearly Summary</option>
							</select>
						</div>
						<div class="col-md-3 col-6" id="month-field" style="{{ ($viewType ?? 'monthly') == 'yearly' ? 'display: none;' : '' }}">
							<label class="form-label">
								<i data-feather="calendar" style="width: 14px; height: 14px;"></i> Month
							</label>
							<select name="month" class="form-select">
								@for($m = 1; $m <= 12; $m++)
									<option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
										{{ date('F', mktime(0, 0, 0, $m, 1)) }}
									</option>
								@endfor
							</select>
						</div>
						<div class="col-md-2 col-6">
							<label class="form-label">
								<i data-feather="hash" style="width: 14px; height: 14px;"></i> Year
							</label>
							<select name="year" class="form-select">
								@for($y = date('Y'); $y >= date('Y') - 5; $y--)
									<option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
								@endfor
							</select>
						</div>
						<div class="col-md-4 col-6">
							<button type="submit" class="btn btn-primary">
								<i data-feather="search" style="width: 14px; height: 14px;"></i> View Attendance
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<!-- Stats Cards -->
	<div class="row mb-4">
		<div class="col-xl-3 col-sm-6 mb-3">
			<div class="card stat-card h-100">
				<div class="card-body">
					<div class="d-flex align-items-center">
						<div class="quick-action-icon bg-light-success me-3">
							<i data-feather="check-circle" class="text-success" style="width: 28px; height: 28px;"></i>
						</div>
						<div>
							<h3 class="mb-0 text-success">{{ $stats['present'] }}</h3>
							<small class="text-muted">Days Present</small>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-sm-6 mb-3">
			<div class="card stat-card h-100">
				<div class="card-body">
					<div class="d-flex align-items-center">
						<div class="quick-action-icon bg-light-danger me-3">
							<i data-feather="x-circle" class="text-danger" style="width: 28px; height: 28px;"></i>
						</div>
						<div>
							<h3 class="mb-0 text-danger">{{ $stats['absent'] }}</h3>
							<small class="text-muted">Days Absent</small>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-sm-6 mb-3">
			<div class="card stat-card h-100">
				<div class="card-body">
					<div class="d-flex align-items-center">
						<div class="quick-action-icon bg-light-warning me-3">
							<i data-feather="clock" class="text-warning" style="width: 28px; height: 28px;"></i>
						</div>
						<div>
							<h3 class="mb-0 text-warning">{{ $stats['late'] }}</h3>
							<small class="text-muted">Days Late</small>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-sm-6 mb-3">
			<div class="card stat-card h-100 {{ $stats['percentage'] >= 75 ? 'border-success' : ($stats['percentage'] >= 50 ? 'border-warning' : 'border-danger') }}" style="border-width: 2px !important;">
				<div class="card-body">
					<div class="d-flex align-items-center">
						<div class="quick-action-icon {{ $stats['percentage'] >= 75 ? 'bg-light-success' : ($stats['percentage'] >= 50 ? 'bg-light-warning' : 'bg-light-danger') }} me-3">
							<i data-feather="percent" class="{{ $stats['percentage'] >= 75 ? 'text-success' : ($stats['percentage'] >= 50 ? 'text-warning' : 'text-danger') }}" style="width: 28px; height: 28px;"></i>
						</div>
						<div>
							<h3 class="mb-0 {{ $stats['percentage'] >= 75 ? 'text-success' : ($stats['percentage'] >= 50 ? 'text-warning' : 'text-danger') }}">{{ $stats['percentage'] }}%</h3>
							<small class="text-muted">{{ ($viewType ?? 'monthly') == 'yearly' ? 'Yearly' : 'Monthly' }} Rate</small>
						</div>
					</div>
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
	<div class="row mt-3">
		<div class="col-12">
			<div class="card">
				<div class="card-body py-3">
					<div class="d-flex flex-wrap gap-4 align-items-center">
						<strong class="me-2">
							<i data-feather="info" style="width: 14px; height: 14px;"></i> Legend:
						</strong>
						<span class="d-flex align-items-center">
							<span class="badge badge-light-success me-2">
								<i data-feather="check" style="width: 12px; height: 12px;"></i> Present
							</span>
							Attended full day
						</span>
						<span class="d-flex align-items-center">
							<span class="badge badge-light-danger me-2">
								<i data-feather="x" style="width: 12px; height: 12px;"></i> Absent
							</span>
							Did not attend
						</span>
						<span class="d-flex align-items-center">
							<span class="badge badge-light-warning me-2">
								<i data-feather="clock" style="width: 12px; height: 12px;"></i> Late
							</span>
							Arrived late
						</span>
						<span class="d-flex align-items-center">
							<span class="badge badge-light-info me-2">
								<i data-feather="sunrise" style="width: 12px; height: 12px;"></i> Half Day
							</span>
							Attended half day
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@push('styles')
<style>
	.quick-action-icon {
		width: 50px;
		height: 50px;
		display: flex;
		align-items: center;
		justify-content: center;
		border-radius: 10px;
	}
	@media print {
		.help-tip, .btn { display: none !important; }
		.card { box-shadow: none !important; border: 1px solid #ddd !important; }
	}
</style>
@endpush
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
