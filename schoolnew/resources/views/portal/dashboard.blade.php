@extends('layouts.portal')

@php
	$user = Auth::user();
	$isParentUser = false;
	$parentRecord = null;

	// Check if user is a parent
	if (!\App\Models\Student::where('user_id', $user->id)->exists()) {
		$parentRecord = \App\Models\ParentGuardian::where('user_id', $user->id)
			->orWhere('father_email', $user->email)
			->orWhere('mother_email', $user->email)
			->orWhere('guardian_email', $user->email)
			->first();
		$isParentUser = $parentRecord !== null;
	}
@endphp

@section('title', 'Dashboard')
@section('page-title', $isParentUser ? 'Parent Dashboard' : 'My Dashboard')

@section('breadcrumb')
	<li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="container-fluid">
	<!-- Welcome Card -->
	<div class="row mb-4">
		<div class="col-12">
			<div class="card welcome-card {{ $isParentUser ? 'parent-theme' : 'student-theme' }}">
				<div class="card-body py-4">
					<div class="row align-items-center">
						<div class="col-md-8">
							@if($isParentUser)
								<p class="text-white mb-1" style="opacity: 0.85;"><i class="fa fa-user-friends me-2"></i>Parent Portal</p>
								<h3 class="text-white mb-2">Welcome, {{ $user->name }}!</h3>
								<p class="text-white mb-0" style="opacity: 0.85;">Viewing: <strong>{{ $student->full_name }}</strong></p>
								<p class="text-white mb-0" style="opacity: 0.85;">
									<i class="fa fa-graduation-cap me-1"></i>
									{{ $student->schoolClass->name ?? 'N/A' }} - Section {{ $student->section->name ?? 'N/A' }}
								</p>
							@else
								<p class="text-white mb-1" style="opacity: 0.85;"><i class="fa fa-user-graduate me-2"></i>Student Portal</p>
								<h3 class="text-white mb-2">Welcome back, {{ $student->first_name }}!</h3>
								<p class="text-white mb-0" style="opacity: 0.85;">
									<i class="fa fa-graduation-cap me-1"></i>
									{{ $student->schoolClass->name ?? 'N/A' }} - Section {{ $student->section->name ?? 'N/A' }}
								</p>
							@endif
							<p class="text-white mb-0 mt-2" style="opacity: 0.85;">
								<i class="fa fa-calendar me-1"></i> {{ now()->format('l, F j, Y') }}
							</p>
						</div>
						<div class="col-md-4 text-end d-none d-md-block">
							@if($student->photo)
								<img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->full_name }}" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover; border: 4px solid rgba(255,255,255,0.3);">
							@else
								<div class="rounded-circle bg-white bg-opacity-25 d-inline-flex align-items-center justify-content-center text-white" style="width: 100px; height: 100px; font-size: 36px;">
									{{ strtoupper(substr($student->first_name, 0, 1)) }}
								</div>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Quick Stats Cards -->
	<div class="row mb-4">
		<div class="col-xl-3 col-md-6 mb-3">
			<div class="card stat-card h-100">
				<div class="card-body">
					<div class="d-flex justify-content-between align-items-center">
						<div>
							<p class="text-muted mb-1 small">This Month's Attendance</p>
							<h3 class="mb-0 {{ $attendanceStats['percentage'] >= 75 ? 'text-success' : ($attendanceStats['percentage'] >= 50 ? 'text-warning' : 'text-danger') }}">
								{{ $attendanceStats['percentage'] }}%
							</h3>
							<div class="mt-2">
								<span class="badge bg-success bg-opacity-10 text-success">{{ $attendanceStats['present'] }}P</span>
								<span class="badge bg-danger bg-opacity-10 text-danger">{{ $attendanceStats['absent'] }}A</span>
								<span class="badge bg-warning bg-opacity-10 text-warning">{{ $attendanceStats['late'] }}L</span>
							</div>
						</div>
						<div class="quick-action-icon bg-success bg-opacity-10">
							<i data-feather="check-circle" class="text-success" style="width: 28px; height: 28px;"></i>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xl-3 col-md-6 mb-3">
			<div class="card stat-card h-100">
				<div class="card-body">
					<div class="d-flex justify-content-between align-items-center">
						<div>
							<p class="text-muted mb-1 small">Fee Status</p>
							@if($feeStats['total_due'] > 0)
								<h3 class="mb-0 text-warning">Rs. {{ number_format($feeStats['total_due'], 0) }}</h3>
								<small class="text-muted">Due Amount</small>
							@else
								<h3 class="mb-0 text-success">All Clear</h3>
								<small class="text-muted">No pending fees</small>
							@endif
						</div>
						<div class="quick-action-icon {{ $feeStats['total_due'] > 0 ? 'bg-warning' : 'bg-success' }} bg-opacity-10">
							<i data-feather="credit-card" class="{{ $feeStats['total_due'] > 0 ? 'text-warning' : 'text-success' }}" style="width: 28px; height: 28px;"></i>
						</div>
					</div>
					@if($feeStats['total_due'] > 0)
						<a href="{{ route('portal.payment.checkout') }}" class="btn btn-sm btn-warning mt-2">Pay Now</a>
					@endif
				</div>
			</div>
		</div>

		<div class="col-xl-3 col-md-6 mb-3">
			<div class="card stat-card h-100">
				<div class="card-body">
					<div class="d-flex justify-content-between align-items-center">
						<div>
							<p class="text-muted mb-1 small">Today's Classes</p>
							<h3 class="mb-0 text-info">{{ $todaysTimetable->count() }}</h3>
							<small class="text-muted">{{ now()->format('l') }}</small>
						</div>
						<div class="quick-action-icon bg-info bg-opacity-10">
							<i data-feather="book-open" class="text-info" style="width: 28px; height: 28px;"></i>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xl-3 col-md-6 mb-3">
			<div class="card stat-card h-100">
				<div class="card-body">
					<div class="d-flex justify-content-between align-items-center">
						<div>
							<p class="text-muted mb-1 small">Leave Applications</p>
							<h3 class="mb-0 text-primary">{{ $pendingLeaves->count() }}</h3>
							<small class="text-muted">Pending</small>
						</div>
						<div class="quick-action-icon bg-primary bg-opacity-10">
							<i data-feather="file-text" class="text-primary" style="width: 28px; height: 28px;"></i>
						</div>
					</div>
					<a href="{{ route('portal.leaves.create') }}" class="btn btn-sm btn-outline-primary mt-2">Apply Leave</a>
				</div>
			</div>
		</div>
	</div>

	<!-- Quick Actions -->
	<div class="row mb-4">
		<div class="col-12">
			<h6 class="text-muted mb-3"><i data-feather="zap" style="width: 16px; height: 16px;"></i> Quick Actions</h6>
		</div>
		<div class="col-6 col-md-4 col-lg-2 mb-3">
			<a href="{{ route('portal.timetable') }}" class="card quick-action-card h-100 text-center p-3 text-decoration-none">
				<div class="quick-action-icon bg-primary bg-opacity-10 mx-auto" style="width: 50px; height: 50px;">
					<i data-feather="calendar" class="text-primary"></i>
				</div>
				<span class="text-dark small">Timetable</span>
			</a>
		</div>
		<div class="col-6 col-md-4 col-lg-2 mb-3">
			<a href="{{ route('portal.attendance') }}" class="card quick-action-card h-100 text-center p-3 text-decoration-none">
				<div class="quick-action-icon bg-success bg-opacity-10 mx-auto" style="width: 50px; height: 50px;">
					<i data-feather="check-square" class="text-success"></i>
				</div>
				<span class="text-dark small">Attendance</span>
			</a>
		</div>
		<div class="col-6 col-md-4 col-lg-2 mb-3">
			<a href="{{ route('portal.homework.index') }}" class="card quick-action-card h-100 text-center p-3 text-decoration-none">
				<div class="quick-action-icon bg-warning bg-opacity-10 mx-auto" style="width: 50px; height: 50px;">
					<i data-feather="edit-3" class="text-warning"></i>
				</div>
				<span class="text-dark small">Homework</span>
			</a>
		</div>
		<div class="col-6 col-md-4 col-lg-2 mb-3">
			<a href="{{ route('portal.exams.index') }}" class="card quick-action-card h-100 text-center p-3 text-decoration-none">
				<div class="quick-action-icon bg-danger bg-opacity-10 mx-auto" style="width: 50px; height: 50px;">
					<i data-feather="award" class="text-danger"></i>
				</div>
				<span class="text-dark small">Exams</span>
			</a>
		</div>
		<div class="col-6 col-md-4 col-lg-2 mb-3">
			<a href="{{ route('portal.fees.overview') }}" class="card quick-action-card h-100 text-center p-3 text-decoration-none">
				<div class="quick-action-icon bg-info bg-opacity-10 mx-auto" style="width: 50px; height: 50px;">
					<i data-feather="dollar-sign" class="text-info"></i>
				</div>
				<span class="text-dark small">Fees</span>
			</a>
		</div>
		<div class="col-6 col-md-4 col-lg-2 mb-3">
			<a href="{{ route('portal.library.index') }}" class="card quick-action-card h-100 text-center p-3 text-decoration-none">
				<div class="quick-action-icon bg-secondary bg-opacity-10 mx-auto" style="width: 50px; height: 50px;">
					<i data-feather="book" class="text-secondary"></i>
				</div>
				<span class="text-dark small">Library</span>
			</a>
		</div>
	</div>

	<div class="row">
		<!-- Today's Timetable -->
		<div class="col-xl-6 mb-4">
			<div class="card h-100">
				<div class="card-header pb-0">
					<div class="d-flex justify-content-between align-items-center">
						<h5 class="mb-0">Today's Schedule</h5>
						<span class="badge bg-primary">{{ now()->format('l') }}</span>
					</div>
				</div>
				<div class="card-body">
					@if($todaysTimetable->count() > 0)
						<div class="list-group list-group-flush">
							@foreach($todaysTimetable as $entry)
								<div class="list-group-item px-0">
									<div class="d-flex align-items-center">
										<div class="flex-shrink-0 me-3 text-center" style="width: 60px;">
											<span class="badge bg-primary d-block py-2">{{ $entry->period->name ?? 'N/A' }}</span>
											<small class="text-muted">
												{{ $entry->period ? \Carbon\Carbon::parse($entry->period->start_time)->format('h:i') : '' }}
											</small>
										</div>
										<div class="flex-grow-1">
											<h6 class="mb-1">{{ $entry->subject->name ?? 'N/A' }}</h6>
											<small class="text-muted">
												<i data-feather="user" style="width: 12px; height: 12px;"></i>
												{{ $entry->teacher->first_name ?? '' }} {{ $entry->teacher->last_name ?? '' }}
											</small>
										</div>
										@if($entry->room)
											<span class="badge bg-light text-dark">{{ $entry->room }}</span>
										@endif
									</div>
								</div>
							@endforeach
						</div>
						<div class="text-center mt-3">
							<a href="{{ route('portal.timetable') }}" class="btn btn-sm btn-outline-primary">View Full Timetable</a>
						</div>
					@else
						<div class="text-center py-4">
							<i data-feather="coffee" style="width: 48px; height: 48px;" class="text-muted mb-3"></i>
							<p class="text-muted mb-0">No classes scheduled for today</p>
							<small class="text-muted">Enjoy your day off!</small>
						</div>
					@endif
				</div>
			</div>
		</div>

		<!-- Notices & Homework -->
		<div class="col-xl-6 mb-4">
			<div class="card h-100">
				<div class="card-header pb-0">
					<ul class="nav nav-tabs card-header-tabs" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" data-bs-toggle="tab" href="#noticesTab" role="tab">
								<i data-feather="bell" style="width: 14px; height: 14px;"></i> Notices
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" data-bs-toggle="tab" href="#homeworkTab" role="tab">
								<i data-feather="edit-3" style="width: 14px; height: 14px;"></i> Homework
							</a>
						</li>
					</ul>
				</div>
				<div class="card-body">
					<div class="tab-content">
						<!-- Notices Tab -->
						<div class="tab-pane fade show active" id="noticesTab" role="tabpanel">
							@if($notices->count() > 0)
								<div class="list-group list-group-flush">
									@foreach($notices->take(4) as $notice)
										<a href="{{ route('portal.notices.show', $notice) }}" class="list-group-item list-group-item-action px-0">
											<div class="d-flex justify-content-between align-items-start">
												<div>
													<h6 class="mb-1">{{ Str::limit($notice->title, 40) }}</h6>
													<small class="text-muted">{{ $notice->publish_date->format('M d, Y') }}</small>
												</div>
												@if($notice->priority == 'high')
													<span class="badge bg-danger">Important</span>
												@endif
											</div>
										</a>
									@endforeach
								</div>
								<div class="text-center mt-3">
									<a href="{{ route('portal.notices') }}" class="btn btn-sm btn-outline-primary">View All Notices</a>
								</div>
							@else
								<div class="text-center py-4">
									<p class="text-muted mb-0">No recent notices</p>
								</div>
							@endif
						</div>
						<!-- Homework Tab -->
						<div class="tab-pane fade" id="homeworkTab" role="tabpanel">
							@php
								$pendingHomework = \App\Models\Homework::where('class_id', $student->class_id)
									->where(function($q) use ($student) {
										$q->whereNull('section_id')->orWhere('section_id', $student->section_id);
									})
									->where('submission_date', '>=', now())
									->latest()
									->take(4)
									->get();
							@endphp
							@if($pendingHomework->count() > 0)
								<div class="list-group list-group-flush">
									@foreach($pendingHomework as $hw)
										<a href="{{ route('portal.homework.show', $hw) }}" class="list-group-item list-group-item-action px-0">
											<div class="d-flex justify-content-between align-items-start">
												<div>
													<h6 class="mb-1">{{ Str::limit($hw->title, 35) }}</h6>
													<small class="text-muted">{{ $hw->subject->name ?? '' }}</small>
												</div>
												<span class="badge {{ $hw->submission_date->isToday() ? 'bg-danger' : 'bg-warning' }}">
													Due {{ $hw->submission_date->format('M d') }}
												</span>
											</div>
										</a>
									@endforeach
								</div>
								<div class="text-center mt-3">
									<a href="{{ route('portal.homework.pending') }}" class="btn btn-sm btn-outline-warning">View Pending Homework</a>
								</div>
							@else
								<div class="text-center py-4">
									<i data-feather="check-circle" style="width: 48px; height: 48px;" class="text-success mb-3"></i>
									<p class="text-muted mb-0">No pending homework</p>
								</div>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Upcoming Events -->
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-header pb-0">
					<div class="d-flex justify-content-between align-items-center">
						<h5 class="mb-0">Upcoming Events</h5>
						<a href="{{ route('portal.events') }}" class="btn btn-sm btn-outline-primary">View All</a>
					</div>
				</div>
				<div class="card-body">
					@if($upcomingEvents->count() > 0)
						<div class="row">
							@foreach($upcomingEvents->take(4) as $event)
								<div class="col-md-6 col-lg-3 mb-3">
									<div class="card info-card border h-100">
										<div class="card-body p-3">
											<div class="d-flex align-items-start mb-2">
												<div class="flex-shrink-0 me-2 text-center rounded p-2" style="background: {{ $event->color ?? '#7366ff' }}20;">
													<span class="d-block fw-bold" style="color: {{ $event->color ?? '#7366ff' }};">{{ $event->start_date->format('d') }}</span>
													<small style="color: {{ $event->color ?? '#7366ff' }};">{{ $event->start_date->format('M') }}</small>
												</div>
												<div class="flex-grow-1">
													<h6 class="mb-1" style="font-size: 14px;">{{ Str::limit($event->title, 30) }}</h6>
													@if($event->venue)
														<small class="text-muted">
															<i data-feather="map-pin" style="width: 12px; height: 12px;"></i>
															{{ Str::limit($event->venue, 20) }}
														</small>
													@endif
												</div>
											</div>
										</div>
									</div>
								</div>
							@endforeach
						</div>
					@else
						<div class="text-center py-4">
							<i data-feather="calendar" style="width: 48px; height: 48px;" class="text-muted mb-3"></i>
							<p class="text-muted mb-0">No upcoming events</p>
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
