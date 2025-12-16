@extends('layouts.portal')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Welcome Banner -->
    <div class="row">
        <div class="col-12">
            <div class="card bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-white">
                            <h4 class="text-white mb-1">Welcome back, {{ $student->first_name }}!</h4>
                            <p class="mb-0">Class: {{ $student->schoolClass->name ?? 'N/A' }} | Section: {{ $student->section->name ?? 'N/A' }}</p>
                            <p class="mb-0 opacity-75">Academic Year: {{ $currentAcademicYear->name ?? 'N/A' }}</p>
                        </div>
                        <div class="d-none d-md-block">
                            <img src="{{ $student->photo_url }}" alt="{{ $student->full_name }}" class="rounded-circle" width="80" height="80" style="object-fit: cover; border: 3px solid white;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <!-- Attendance Stats -->
        <div class="col-xl-3 col-sm-6">
            <div class="card o-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="f-w-500 f-light">Attendance This Month</span>
                            <h4 class="mt-1 mb-0">{{ $attendanceStats['percentage'] }}%</h4>
                        </div>
                        <div class="bg-light-success rounded-circle p-3">
                            <svg class="stroke-icon" style="width: 24px; height: 24px;">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-calendar') }}"></use>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge badge-light-success">{{ $attendanceStats['present'] }} Present</span>
                        <span class="badge badge-light-danger">{{ $attendanceStats['absent'] }} Absent</span>
                        <span class="badge badge-light-warning">{{ $attendanceStats['late'] }} Late</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fee Stats -->
        <div class="col-xl-3 col-sm-6">
            <div class="card o-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="f-w-500 f-light">Total Paid</span>
                            <h4 class="mt-1 mb-0">Rs. {{ number_format($feeStats['total_paid'], 2) }}</h4>
                        </div>
                        <div class="bg-light-primary rounded-circle p-3">
                            <svg class="stroke-icon" style="width: 24px; height: 24px;">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-ecommerce') }}"></use>
                            </svg>
                        </div>
                    </div>
                    @if($feeStats['total_due'] > 0)
                        <div class="mt-3">
                            <span class="badge badge-light-warning">Rs. {{ number_format($feeStats['total_due'], 2) }} Due</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Pending Leaves -->
        <div class="col-xl-3 col-sm-6">
            <div class="card o-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="f-w-500 f-light">Pending Leaves</span>
                            <h4 class="mt-1 mb-0">{{ $pendingLeaves->count() }}</h4>
                        </div>
                        <div class="bg-light-warning rounded-circle p-3">
                            <svg class="stroke-icon" style="width: 24px; height: 24px;">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-file') }}"></use>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('portal.leaves.create') }}" class="btn btn-sm btn-outline-primary">Apply for Leave</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="col-xl-3 col-sm-6">
            <div class="card o-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="f-w-500 f-light">Quick Actions</span>
                        </div>
                    </div>
                    <div class="mt-3 d-flex gap-2 flex-wrap">
                        <a href="{{ route('portal.attendance') }}" class="btn btn-sm btn-outline-success">Attendance</a>
                        <a href="{{ route('portal.timetable') }}" class="btn btn-sm btn-outline-info">Timetable</a>
                        <a href="{{ route('portal.fees.overview') }}" class="btn btn-sm btn-outline-primary">Fees</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Today's Timetable -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>Today's Timetable</h5>
                        <span class="badge badge-light-primary">{{ now()->format('l') }}</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($todaysTimetable->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Period</th>
                                        <th>Time</th>
                                        <th>Subject</th>
                                        <th>Teacher</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todaysTimetable as $entry)
                                        <tr>
                                            <td>{{ $entry->period->name ?? 'N/A' }}</td>
                                            <td>{{ $entry->period ? $entry->period->start_time->format('h:i A') . ' - ' . $entry->period->end_time->format('h:i A') : 'N/A' }}</td>
                                            <td>{{ $entry->subject->name ?? 'N/A' }}</td>
                                            <td>{{ $entry->teacher->first_name ?? '' }} {{ $entry->teacher->last_name ?? '' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted mb-0">No classes scheduled for today</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Notices -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>Recent Notices</h5>
                        <a href="{{ route('portal.notices') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                </div>
                <div class="card-body">
                    @if($notices->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($notices as $notice)
                                <li class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">
                                                <span class="badge {{ $notice->getTypeBadgeClass() }} me-2">{{ $notice->getTypeLabel() }}</span>
                                                {{ $notice->title }}
                                            </h6>
                                            <small class="text-muted">{{ $notice->publish_date->format('M d, Y') }}</small>
                                        </div>
                                        <a href="{{ route('portal.notices.show', $notice) }}" class="btn btn-sm btn-light">View</a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted mb-0">No notices available</p>
                        </div>
                    @endif
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
                        <h5>Upcoming Events</h5>
                        <a href="{{ route('portal.events') }}" class="btn btn-sm btn-outline-primary">View Calendar</a>
                    </div>
                </div>
                <div class="card-body">
                    @if($upcomingEvents->count() > 0)
                        <div class="row">
                            @foreach($upcomingEvents as $event)
                                <div class="col-md-4 col-sm-6 mb-3">
                                    <div class="card border h-100" style="border-left: 4px solid {{ $event->color }} !important;">
                                        <div class="card-body py-3">
                                            <h6 class="mb-2">{{ $event->title }}</h6>
                                            <p class="text-muted small mb-1">
                                                <i class="fa fa-calendar me-1"></i>
                                                {{ $event->start_date->format('M d, Y') }}
                                                @if($event->isMultiDay())
                                                    - {{ $event->end_date->format('M d, Y') }}
                                                @endif
                                            </p>
                                            @if($event->venue)
                                                <p class="text-muted small mb-0">
                                                    <i class="fa fa-map-marker me-1"></i>
                                                    {{ $event->venue }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted mb-0">No upcoming events</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
