@extends('layouts.teacher-portal')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row">
	<!-- Welcome Card -->
	<div class="col-12 mb-4">
		<div class="card welcome-card">
			<div class="card-body">
				<div class="row align-items-center">
					<div class="col-md-8">
						<h4 class="text-white mb-2">Welcome back, {{ $user->name }}!</h4>
						<p class="text-white mb-0" style="opacity: 0.85;">
							@if($staff->designation)
								{{ $staff->designation->name }}
								@if($staff->department)
									| {{ $staff->department->name }}
								@endif
							@endif
						</p>
						<p class="text-white mb-0 mt-2" style="opacity: 0.85;">
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
	<div class="col-xl-3 col-md-6 mb-4">
		<div class="card stat-card h-100">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center">
					<div>
						<p class="text-muted mb-1">My Classes</p>
						<h3 class="mb-0">{{ $stats['total_classes'] }}</h3>
					</div>
					<div class="quick-action-icon bg-primary bg-opacity-10">
						<i data-feather="book-open" class="text-primary"></i>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-xl-3 col-md-6 mb-4">
		<div class="card stat-card h-100">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center">
					<div>
						<p class="text-muted mb-1">Total Students</p>
						<h3 class="mb-0">{{ $stats['total_students'] }}</h3>
					</div>
					<div class="quick-action-icon bg-success bg-opacity-10">
						<i data-feather="users" class="text-success"></i>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-xl-3 col-md-6 mb-4">
		<div class="card stat-card h-100">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center">
					<div>
						<p class="text-muted mb-1">Classes Today</p>
						<h3 class="mb-0">{{ $stats['classes_today'] }}</h3>
					</div>
					<div class="quick-action-icon bg-info bg-opacity-10">
						<i data-feather="calendar" class="text-info"></i>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-xl-3 col-md-6 mb-4">
		<div class="card stat-card h-100">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center">
					<div>
						<p class="text-muted mb-1">Pending Reviews</p>
						<h3 class="mb-0">{{ $stats['pending_reviews'] }}</h3>
					</div>
					<div class="quick-action-icon bg-warning bg-opacity-10">
						<i data-feather="file-text" class="text-warning"></i>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Today's Timetable -->
	<div class="col-xl-8 mb-4">
		<div class="card h-100">
			<div class="card-header pb-0">
				<div class="d-flex justify-content-between align-items-center">
					<h5 class="mb-0">Today's Schedule ({{ now()->format('l') }})</h5>
					<a href="{{ route('teacher.timetable') }}" class="btn btn-outline-primary btn-sm">View Full Timetable</a>
				</div>
			</div>
			<div class="card-body">
				@if($todaysTimetable->count() > 0)
					<div class="table-responsive">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>Period</th>
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
											<span class="badge bg-primary">{{ $slot->period->name ?? 'N/A' }}</span>
										</td>
										<td>
											{{ $slot->period ? \Carbon\Carbon::parse($slot->period->start_time)->format('h:i A') : '' }}
											-
											{{ $slot->period ? \Carbon\Carbon::parse($slot->period->end_time)->format('h:i A') : '' }}
										</td>
										<td>
											<strong>{{ $slot->schoolClass->name ?? '' }}</strong>
											@if($slot->section)
												<span class="text-muted">({{ $slot->section->name }})</span>
											@endif
										</td>
										<td>{{ $slot->subject->name ?? 'N/A' }}</td>
										<td>{{ $slot->room ?? '-' }}</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				@else
					<div class="text-center py-4">
						<i data-feather="coffee" style="width: 48px; height: 48px;" class="text-muted mb-3"></i>
						<p class="text-muted mb-0">No classes scheduled for today</p>
					</div>
				@endif
			</div>
		</div>
	</div>

	<!-- Quick Actions -->
	<div class="col-xl-4 mb-4">
		<div class="card h-100">
			<div class="card-header pb-0">
				<h5 class="mb-0">Quick Actions</h5>
			</div>
			<div class="card-body">
				<div class="row g-3">
					<div class="col-6">
						<a href="{{ route('teacher.my-classes') }}" class="card quick-action-card h-100 text-center p-3 text-decoration-none">
							<div class="quick-action-icon bg-primary bg-opacity-10 mx-auto">
								<i data-feather="users" class="text-primary"></i>
							</div>
							<span class="text-dark">My Classes</span>
						</a>
					</div>
					<div class="col-6">
						<a href="{{ route('teacher.timetable') }}" class="card quick-action-card h-100 text-center p-3 text-decoration-none">
							<div class="quick-action-icon bg-success bg-opacity-10 mx-auto">
								<i data-feather="calendar" class="text-success"></i>
							</div>
							<span class="text-dark">Timetable</span>
						</a>
					</div>
					<div class="col-6">
						<a href="{{ route('teacher.leaves.create') }}" class="card quick-action-card h-100 text-center p-3 text-decoration-none">
							<div class="quick-action-icon bg-info bg-opacity-10 mx-auto">
								<i data-feather="file-text" class="text-info"></i>
							</div>
							<span class="text-dark">Apply Leave</span>
						</a>
					</div>
					<div class="col-6">
						<a href="{{ route('teacher.profile') }}" class="card quick-action-card h-100 text-center p-3 text-decoration-none">
							<div class="quick-action-icon bg-warning bg-opacity-10 mx-auto">
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
			<div class="card-header pb-0">
				<div class="d-flex justify-content-between align-items-center">
					<h5 class="mb-0">My Classes</h5>
					<a href="{{ route('teacher.my-classes') }}" class="btn btn-link btn-sm p-0">View All</a>
				</div>
			</div>
			<div class="card-body">
				@if($myClasses->count() > 0)
					<div class="list-group list-group-flush">
						@foreach($myClasses->take(5) as $class)
							<a href="{{ route('teacher.class-students', [$class->class_id, $class->section_id]) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
								<div>
									<strong>{{ $class->schoolClass->name ?? 'N/A' }}</strong>
									@if($class->section)
										<span class="text-muted">- {{ $class->section->name }}</span>
									@endif
								</div>
								<span class="badge bg-primary rounded-pill">
									<i data-feather="chevron-right" style="width: 14px; height: 14px;"></i>
								</span>
							</a>
						@endforeach
					</div>
				@else
					<div class="text-center py-4">
						<p class="text-muted mb-0">No classes assigned yet</p>
					</div>
				@endif
			</div>
		</div>
	</div>

	<!-- Recent Notices & Events -->
	<div class="col-xl-6 mb-4">
		<div class="card h-100">
			<div class="card-header pb-0">
				<h5 class="mb-0">Recent Notices & Events</h5>
			</div>
			<div class="card-body">
				@if($notices->count() > 0 || $upcomingEvents->count() > 0)
					<div class="list-group list-group-flush">
						@foreach($notices->take(3) as $notice)
							<div class="list-group-item">
								<div class="d-flex align-items-start">
									<div class="flex-shrink-0 me-3">
										<span class="badge bg-info rounded-circle p-2">
											<i data-feather="bell" style="width: 14px; height: 14px;"></i>
										</span>
									</div>
									<div class="flex-grow-1">
										<h6 class="mb-1">{{ Str::limit($notice->title, 40) }}</h6>
										<small class="text-muted">{{ $notice->publish_date->format('M d, Y') }}</small>
									</div>
								</div>
							</div>
						@endforeach
						@foreach($upcomingEvents->take(2) as $event)
							<div class="list-group-item">
								<div class="d-flex align-items-start">
									<div class="flex-shrink-0 me-3">
										<span class="badge bg-success rounded-circle p-2">
											<i data-feather="calendar" style="width: 14px; height: 14px;"></i>
										</span>
									</div>
									<div class="flex-grow-1">
										<h6 class="mb-1">{{ Str::limit($event->title, 40) }}</h6>
										<small class="text-muted">{{ $event->start_date->format('M d, Y') }}</small>
									</div>
								</div>
							</div>
						@endforeach
					</div>
				@else
					<div class="text-center py-4">
						<p class="text-muted mb-0">No recent notices or events</p>
					</div>
				@endif
			</div>
		</div>
	</div>

	<!-- My Leave Applications -->
	<div class="col-12 mb-4">
		<div class="card">
			<div class="card-header pb-0">
				<div class="d-flex justify-content-between align-items-center">
					<h5 class="mb-0">My Recent Leave Applications</h5>
					<a href="{{ route('teacher.leaves.index') }}" class="btn btn-outline-primary btn-sm">View All</a>
				</div>
			</div>
			<div class="card-body">
				@if($myLeaves->count() > 0)
					<div class="table-responsive">
						<table class="table table-hover">
							<thead>
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
										<td>{{ $leave->leaveType->name ?? 'N/A' }}</td>
										<td>{{ $leave->from_date->format('M d, Y') }}</td>
										<td>{{ $leave->to_date->format('M d, Y') }}</td>
										<td>{{ $leave->from_date->diffInDays($leave->to_date) + 1 }}</td>
										<td>
											@switch($leave->status)
												@case('pending')
													<span class="badge bg-warning">Pending</span>
													@break
												@case('approved')
													<span class="badge bg-success">Approved</span>
													@break
												@case('rejected')
													<span class="badge bg-danger">Rejected</span>
													@break
												@default
													<span class="badge bg-secondary">{{ ucfirst($leave->status) }}</span>
											@endswitch
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				@else
					<div class="text-center py-4">
						<p class="text-muted mb-0">No leave applications found</p>
						<a href="{{ route('teacher.leaves.create') }}" class="btn btn-primary btn-sm mt-2">Apply for Leave</a>
					</div>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection
