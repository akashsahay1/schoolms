@extends('layouts.teacher-portal')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<style>
	.welcome-card .card-body,
	.welcome-card .card-body *:not(.bg-white):not(.bg-white *):not(.bg-light):not(.bg-light *),
	.welcome-card .card-body p,
	.welcome-card .card-body h1,
	.welcome-card .card-body h2,
	.welcome-card .card-body h3,
	.welcome-card .card-body h4,
	.welcome-card .card-body h5,
	.welcome-card .card-body h6,
	.welcome-card .card-body strong,
	.welcome-card .card-body i {
		color: #ffffff !important;
	}
	.welcome-card .card-body .bg-white,
	.welcome-card .card-body .bg-white *,
	.welcome-card .card-body .bg-light,
	.welcome-card .card-body .bg-light * {
		color: #000 !important;
	}
	.welcome-card .card-body svg,
	.welcome-card .card-body [data-feather] {
		stroke: #ffffff !important;
		color: #ffffff !important;
	}
	.stat-card .quick-action-icon {
		width: 50px;
		height: 50px;
		border-radius: 12px;
	}
	.stat-card .quick-action-icon svg,
	.stat-card .quick-action-icon [data-feather] {
		width: 24px;
		height: 24px;
	}
	.stat-card p.text-muted {
		font-size: 13px;
		font-weight: 500;
		text-transform: uppercase;
		letter-spacing: 0.5px;
	}
	.stat-card h3 {
		font-size: 28px;
		font-weight: 700;
		color: #2c323f !important;
	}
	.quick-action-card {
		border-radius: 12px !important;
	}
	.quick-action-card .quick-action-icon {
		width: 50px;
		height: 50px;
		margin-bottom: 10px;
	}
	.quick-action-card span {
		font-size: 13px;
		font-weight: 500;
	}
	.timetable-period-badge {
		min-width: 60px;
		text-align: center;
		font-weight: 600;
		padding: 6px 12px;
		border-radius: 6px;
	}
	.timetable-time {
		font-size: 12px;
		color: #8b8fa3;
	}
	.notice-icon {
		width: 38px;
		height: 38px;
		display: flex;
		align-items: center;
		justify-content: center;
		border-radius: 10px;
		flex-shrink: 0;
	}
	.leave-status-badge {
		min-width: 80px;
		text-align: center;
		padding: 5px 12px;
		border-radius: 20px;
		font-size: 12px;
		font-weight: 600;
	}
	.class-item {
		border-radius: 8px;
		transition: background 0.2s;
		padding: 12px 16px;
	}
	.class-item:hover {
		background: #f8f9fa;
	}
</style>

<div class="row">
	<!-- Welcome Card -->
	<div class="col-12 mb-4">
		<div class="card welcome-card">
			<div class="card-body py-4 px-4">
				<div class="row align-items-center">
					<div class="col-md-8">
						<p class="mb-1" style="opacity: 0.8; font-size: 14px;">
							<i data-feather="sun" style="width: 16px; height: 16px;"></i> Good {{ now()->format('H') < 12 ? 'Morning' : (now()->format('H') < 17 ? 'Afternoon' : 'Evening') }}
						</p>
						<h4 class="mb-2 fw-bold">Welcome back, {{ $user->name }}!</h4>
						<p class="mb-0" style="opacity: 0.85;">
							@if($staff->designation)
								{{ $staff->designation->name }}
								@if($staff->department)
									| {{ $staff->department->name }}
								@endif
							@endif
						</p>
						<p class="mb-0 mt-2" style="opacity: 0.85;">
							<i class="fa fa-calendar me-1"></i> {{ now()->format('l, F j, Y') }}
						</p>
					</div>
					<div class="col-md-4 text-end d-none d-md-block">
						<img src="{{ asset('assets/images/dashboard/cartoon.png') }}" alt="" style="max-height: 120px; opacity: 0.9;">
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Stats Cards -->
	<div class="col-xl-3 col-sm-6 mb-4">
		<div class="card stat-card h-100">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center">
					<div>
						<p class="text-muted mb-1">My Classes</p>
						<h3 class="mb-0">{{ $stats['total_classes'] }}</h3>
					</div>
					<div class="quick-action-icon bg-primary bg-opacity-10 d-flex align-items-center justify-content-center">
						<i data-feather="book-open" class="text-primary"></i>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-xl-3 col-sm-6 mb-4">
		<div class="card stat-card h-100">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center">
					<div>
						<p class="text-muted mb-1">Total Students</p>
						<h3 class="mb-0">{{ $stats['total_students'] }}</h3>
					</div>
					<div class="quick-action-icon bg-success bg-opacity-10 d-flex align-items-center justify-content-center">
						<i data-feather="users" class="text-success"></i>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-xl-3 col-sm-6 mb-4">
		<div class="card stat-card h-100">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center">
					<div>
						<p class="text-muted mb-1">Classes Today</p>
						<h3 class="mb-0">{{ $stats['classes_today'] }}</h3>
					</div>
					<div class="quick-action-icon bg-info bg-opacity-10 d-flex align-items-center justify-content-center">
						<i data-feather="calendar" class="text-info"></i>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-xl-3 col-sm-6 mb-4">
		<div class="card stat-card h-100">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center">
					<div>
						<p class="text-muted mb-1">Pending Reviews</p>
						<h3 class="mb-0">{{ $stats['pending_reviews'] }}</h3>
					</div>
					<div class="quick-action-icon bg-warning bg-opacity-10 d-flex align-items-center justify-content-center">
						<i data-feather="file-text" class="text-warning"></i>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Today's Timetable -->
	<div class="col-xl-8 mb-4">
		<div class="card h-100">
			<div class="card-header pb-0 border-0">
				<div class="d-flex justify-content-between align-items-center">
					<h5 class="mb-0">
						<i data-feather="clock" style="width: 18px; height: 18px;" class="me-2"></i>Today's Schedule
						<span class="badge bg-light text-primary ms-2">{{ now()->format('l') }}</span>
					</h5>
					<a href="{{ route('teacher.timetable') }}" class="btn btn-outline-primary btn-sm">
						View Full Timetable
					</a>
				</div>
			</div>
			<div class="card-body pt-3">
				@if($todaysTimetable->count() > 0)
					<div class="table-responsive">
						<table class="table table-hover mb-0">
							<thead class="bg-light">
								<tr>
									<th style="width: 100px;">Period</th>
									<th>Time</th>
									<th>Class</th>
									<th>Subject</th>
									<th>Room</th>
								</tr>
							</thead>
							<tbody>
								@foreach($todaysTimetable as $slot)
									<tr>
										<td>
											<span class="badge bg-primary timetable-period-badge">{{ $slot->period->name ?? 'N/A' }}</span>
										</td>
										<td>
											<span class="timetable-time">
												{{ $slot->period ? \Carbon\Carbon::parse($slot->period->start_time)->format('h:i A') : '' }}
												-
												{{ $slot->period ? \Carbon\Carbon::parse($slot->period->end_time)->format('h:i A') : '' }}
											</span>
										</td>
										<td>
											<strong>{{ $slot->schoolClass->name ?? '' }}</strong>
											@if($slot->section)
												<span class="text-muted">({{ $slot->section->name }})</span>
											@endif
										</td>
										<td>{{ $slot->subject->name ?? 'N/A' }}</td>
										<td>
											@if($slot->room)
												<span class="badge bg-light text-dark">{{ $slot->room }}</span>
											@else
												<span class="text-muted">-</span>
											@endif
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				@else
					<div class="text-center py-5">
						<div class="mb-3">
							<i data-feather="coffee" style="width: 48px; height: 48px;" class="text-muted"></i>
						</div>
						<h6 class="text-muted">No Classes Today</h6>
						<p class="text-muted mb-0 small">Enjoy your free day!</p>
					</div>
				@endif
			</div>
		</div>
	</div>

	<!-- Quick Actions -->
	<div class="col-xl-4 mb-4">
		<div class="card h-100">
			<div class="card-header pb-0 border-0">
				<h5 class="mb-0">
					<i data-feather="zap" style="width: 18px; height: 18px;" class="me-2"></i>Quick Actions
				</h5>
			</div>
			<div class="card-body pt-3">
				<div class="row g-3">
					<div class="col-6">
						<a href="{{ route('teacher.my-classes') }}" class="card quick-action-card h-100 text-center p-3 text-decoration-none mb-0">
							<div class="quick-action-icon bg-primary bg-opacity-10 mx-auto d-flex align-items-center justify-content-center">
								<i data-feather="users" class="text-primary"></i>
							</div>
							<span class="text-dark">My Classes</span>
						</a>
					</div>
					<div class="col-6">
						<a href="{{ route('teacher.timetable') }}" class="card quick-action-card h-100 text-center p-3 text-decoration-none mb-0">
							<div class="quick-action-icon bg-success bg-opacity-10 mx-auto d-flex align-items-center justify-content-center">
								<i data-feather="calendar" class="text-success"></i>
							</div>
							<span class="text-dark">Timetable</span>
						</a>
					</div>
					<div class="col-6">
						<a href="{{ route('teacher.leaves.create') }}" class="card quick-action-card h-100 text-center p-3 text-decoration-none mb-0">
							<div class="quick-action-icon bg-info bg-opacity-10 mx-auto d-flex align-items-center justify-content-center">
								<i data-feather="file-plus" class="text-info"></i>
							</div>
							<span class="text-dark">Apply Leave</span>
						</a>
					</div>
					<div class="col-6">
						<a href="{{ route('teacher.profile') }}" class="card quick-action-card h-100 text-center p-3 text-decoration-none mb-0">
							<div class="quick-action-icon bg-warning bg-opacity-10 mx-auto d-flex align-items-center justify-content-center">
								<i data-feather="user" class="text-warning"></i>
							</div>
							<span class="text-dark">My Profile</span>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- My Classes Overview -->
	<div class="col-xl-6 mb-4">
		<div class="card h-100">
			<div class="card-header pb-0 border-0">
				<div class="d-flex justify-content-between align-items-center">
					<h5 class="mb-0">
						<i data-feather="book-open" style="width: 18px; height: 18px;" class="me-2"></i>My Classes
					</h5>
					<a href="{{ route('teacher.my-classes') }}" class="btn btn-sm btn-outline-primary">View All</a>
				</div>
			</div>
			<div class="card-body pt-3">
				@if($myClasses->count() > 0)
					<div class="list-group list-group-flush">
						@foreach($myClasses->take(5) as $class)
							<a href="{{ route('teacher.class-students', [$class->class_id, $class->section_id]) }}" class="list-group-item list-group-item-action class-item d-flex justify-content-between align-items-center border-0">
								<div class="d-flex align-items-center gap-3">
									<div class="bg-primary bg-opacity-10 rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; flex-shrink: 0;">
										<i data-feather="book" class="text-primary" style="width: 18px; height: 18px;"></i>
									</div>
									<div>
										<h6 class="mb-0">{{ $class->schoolClass->name ?? 'N/A' }}</h6>
										@if($class->section)
											<small class="text-muted">Section {{ $class->section->name }}</small>
										@endif
									</div>
								</div>
								<i data-feather="chevron-right" class="text-muted" style="width: 16px; height: 16px;"></i>
							</a>
						@endforeach
					</div>
				@else
					<div class="text-center py-4">
						<i data-feather="inbox" style="width: 40px; height: 40px;" class="text-muted mb-2"></i>
						<p class="text-muted mb-0">No classes assigned yet</p>
					</div>
				@endif
			</div>
		</div>
	</div>

	<!-- Recent Notices & Events -->
	<div class="col-xl-6 mb-4">
		<div class="card h-100">
			<div class="card-header pb-0 border-0">
				<h5 class="mb-0">
					<i data-feather="bell" style="width: 18px; height: 18px;" class="me-2"></i>Notices & Events
				</h5>
			</div>
			<div class="card-body pt-3">
				@if($notices->count() > 0 || $upcomingEvents->count() > 0)
					<div class="list-group list-group-flush">
						@foreach($notices->take(3) as $notice)
							<div class="list-group-item border-0 px-0 py-3">
								<div class="d-flex align-items-start gap-3">
									<div class="notice-icon bg-info bg-opacity-10">
										<i data-feather="bell" class="text-info" style="width: 16px; height: 16px;"></i>
									</div>
									<div class="flex-grow-1">
										<h6 class="mb-1" style="font-size: 14px;">{{ Str::limit($notice->title, 40) }}</h6>
										<small class="text-muted">
											<i data-feather="calendar" style="width: 12px; height: 12px;"></i>
											{{ $notice->publish_date->format('M d, Y') }}
										</small>
									</div>
								</div>
							</div>
						@endforeach
						@foreach($upcomingEvents->take(2) as $event)
							<div class="list-group-item border-0 px-0 py-3">
								<div class="d-flex align-items-start gap-3">
									<div class="notice-icon bg-success bg-opacity-10">
										<i data-feather="calendar" class="text-success" style="width: 16px; height: 16px;"></i>
									</div>
									<div class="flex-grow-1">
										<h6 class="mb-1" style="font-size: 14px;">{{ Str::limit($event->title, 40) }}</h6>
										<small class="text-muted">
											<i data-feather="clock" style="width: 12px; height: 12px;"></i>
											{{ $event->start_date->format('M d, Y') }}
										</small>
									</div>
								</div>
							</div>
						@endforeach
					</div>
				@else
					<div class="text-center py-4">
						<i data-feather="bell-off" style="width: 40px; height: 40px;" class="text-muted mb-2"></i>
						<p class="text-muted mb-0">No recent notices or events</p>
					</div>
				@endif
			</div>
		</div>
	</div>

	<!-- My Leave Applications -->
	<div class="col-12 mb-4">
		<div class="card">
			<div class="card-header pb-0 border-0">
				<div class="d-flex justify-content-between align-items-center">
					<h5 class="mb-0">
						<i data-feather="file-text" style="width: 18px; height: 18px;" class="me-2"></i>My Recent Leave Applications
					</h5>
					<a href="{{ route('teacher.leaves.index') }}" class="btn btn-outline-primary btn-sm">View All</a>
				</div>
			</div>
			<div class="card-body pt-3">
				@if($myLeaves->count() > 0)
					<div class="table-responsive">
						<table class="table table-hover mb-0">
							<thead class="bg-light">
								<tr>
									<th>Type</th>
									<th>From</th>
									<th>To</th>
									<th>Days</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								@foreach($myLeaves as $leave)
									<tr>
										<td>
											<strong>{{ $leave->getLeaveTypeLabel() }}</strong>
										</td>
										<td>{{ $leave->from_date->format('M d, Y') }}</td>
										<td>{{ $leave->to_date->format('M d, Y') }}</td>
										<td>
											<span class="badge bg-light text-dark">{{ $leave->from_date->diffInDays($leave->to_date) + 1 }} days</span>
										</td>
										<td>
											@switch($leave->status)
												@case('pending')
													<span class="leave-status-badge bg-warning bg-opacity-15 text-warning">Pending</span>
													@break
												@case('approved')
													<span class="leave-status-badge bg-success bg-opacity-15 text-success">Approved</span>
													@break
												@case('rejected')
													<span class="leave-status-badge bg-danger bg-opacity-15 text-danger">Rejected</span>
													@break
												@default
													<span class="leave-status-badge bg-secondary bg-opacity-15 text-secondary">{{ ucfirst($leave->status) }}</span>
											@endswitch
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				@else
					<div class="text-center py-5">
						<div class="mb-3">
							<i data-feather="inbox" style="width: 48px; height: 48px;" class="text-muted"></i>
						</div>
						<h6 class="text-muted">No Leave Applications</h6>
						<p class="text-muted small mb-3">You haven't applied for any leave yet</p>
						<a href="{{ route('teacher.leaves.create') }}" class="btn btn-primary btn-sm">
							<i data-feather="plus" style="width: 14px; height: 14px;" class="me-1"></i> Apply for Leave
						</a>
					</div>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection
