@extends('layouts.app')

@section('title', 'Dashboard')

@section('page-title', 'School Management')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">School Manage</li>
@endsection

@push('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/flatpickr/flatpickr.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/jquery.dataTables.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select.bootstrap5.css') }}">
<style>
.card-header-right-icon .dropdown-toggle {
	border: 1px solid rgba(82, 82, 108, 0.2);
	padding: 6px 12px;
	font-size: 14px;
	color: var(--body-font-color);
	line-height: 1.5;
	border-radius: 5px;
	background-color: transparent;
	max-width: 160px;
	text-overflow: ellipsis;
	white-space: nowrap;
}
.card-header-right-icon .dropdown-toggle::after {
	display: none;
}
.card-header-right-icon .dropdown-toggle:hover {
	background-color: var(--theme-default);
	color: #fff;
	border-color: var(--theme-default);
}
.card-header-right-icon .dropdown-toggle i {
	font-size: 10px;
	vertical-align: middle;
}
/* Student class filter dropdown */
.student-header .dropdown-menu {
	max-height: 300px;
	overflow-y: auto;
}
/* New Enrolled Students - Admission Date header no wrap */
.new-enroll-student th {
	white-space: nowrap;
}
/* Fix: dropdown overflow in dashboard performance cards */
.dashboard-7 .card:has(.card-header-right-icon),
.dashboard-7 .card:has(.icon-dropdown) {
	overflow: visible !important;
}
.dashboard-7 .card-header-right-icon .dropdown {
	position: relative;
}
.dashboard-7 .card-header-right-icon .dropdown-menu {
	z-index: 1060;
	min-width: 140px;
	right: 0;
	left: auto;
	box-shadow: 0 4px 12px rgba(0,0,0,0.1);
	border-radius: 6px;
}
.dashboard-7 .icon-dropdown .dropdown-menu {
	z-index: 1060;
	box-shadow: 0 4px 12px rgba(0,0,0,0.1);
	border-radius: 6px;
}
/* Button text truncate for small screens */
.dashboard-7 .card-header-right-icon .dropdown-toggle {
	overflow: hidden;
	max-width: 130px;
	text-overflow: ellipsis;
	display: inline-block;
	vertical-align: middle;
}
@media (max-width: 767px) {
	.dashboard-7 .card-header-right-icon .dropdown-toggle {
		max-width: 100px;
		padding: 4px 8px;
		font-size: 12px;
	}
}
</style>
@endpush

@section('content')
<div class="container-fluid dashboard-7">
    {{-- Welcome Banner for non-admin roles --}}
    @if(!in_array($userRole, ['Super Admin', 'Admin']))
    <div class="row mb-3">
        <div class="col-12">
            <div class="card bg-primary">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="text-white mb-1">Welcome, {{ Auth::user()->name }}!</h5>
                            <p class="text-white mb-0" style="opacity: 0.8;">You are logged in as <strong>{{ $userRole }}</strong></p>
                        </div>
                        <div>
                            <span class="badge fs-6" style="background: rgba(255,255,255,0.25); color: #fff;">{{ $userRole }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Librarian Dashboard --}}
    @if($userRole === 'Librarian')
    <div class="row">
        <div class="col-sm-6 col-xl-3">
            <div class="card widget-hover overflow-hidden">
                <div class="card-header card-no-border pb-2">
                    <h5>Total Books</h5>
                </div>
                <div class="card-body pt-0 count-student">
                    <div class="school-wrapper">
                        <div class="school-header">
                            <h4 class="txt-primary">{{ $stats['total_books'] ?? 0 }}</h4>
                        </div>
                        <div class="school-body">
                            <svg class="stroke-icon" style="width: 50px; height: 50px;">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-bookmark') }}"></use>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card widget-hover overflow-hidden">
                <div class="card-header card-no-border pb-2">
                    <h5>Issued Books</h5>
                </div>
                <div class="card-body pt-0 count-student">
                    <div class="school-wrapper">
                        <div class="school-header">
                            <h4 class="txt-warning">{{ $stats['issued_books'] ?? 0 }}</h4>
                        </div>
                        <div class="school-body">
                            <svg class="stroke-icon" style="width: 50px; height: 50px;">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-file') }}"></use>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card widget-hover overflow-hidden">
                <div class="card-header card-no-border pb-2">
                    <h5>Available Books</h5>
                </div>
                <div class="card-body pt-0 count-student">
                    <div class="school-wrapper">
                        <div class="school-header">
                            <h4 class="txt-success">{{ $stats['available_books'] ?? 0 }}</h4>
                        </div>
                        <div class="school-body">
                            <svg class="stroke-icon" style="width: 50px; height: 50px;">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-learning') }}"></use>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card widget-hover overflow-hidden">
                <div class="card-header card-no-border pb-2">
                    <h5>Overdue Books</h5>
                </div>
                <div class="card-body pt-0 count-student">
                    <div class="school-wrapper">
                        <div class="school-header">
                            <h4 class="txt-danger">{{ $stats['overdue_books'] ?? 0 }}</h4>
                        </div>
                        <div class="school-body">
                            <svg class="stroke-icon" style="width: 50px; height: 50px;">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-calendar') }}"></use>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('admin.library.books.index') }}" class="btn btn-primary"><i data-feather="book-open" class="me-2"></i>Manage Books</a>
                        <a href="{{ route('admin.library.issue.index') }}" class="btn btn-warning"><i data-feather="refresh-cw" class="me-2"></i>Issue / Return</a>
                        <a href="{{ route('admin.library.categories.index') }}" class="btn btn-info"><i data-feather="layers" class="me-2"></i>Categories</a>
                        <a href="{{ route('admin.library.reports.index') }}" class="btn btn-success"><i data-feather="bar-chart-2" class="me-2"></i>Reports</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Receptionist Dashboard --}}
    @elseif($userRole === 'Receptionist')
    <div class="row">
        <div class="col-sm-6 col-xl-4">
            <div class="card widget-hover overflow-hidden">
                <div class="card-header card-no-border pb-2">
                    <h5>Total Students</h5>
                </div>
                <div class="card-body pt-0 count-student">
                    <div class="school-wrapper">
                        <div class="school-header">
                            <h4 class="txt-primary">{{ $stats['total_students'] ?? 0 }}</h4>
                        </div>
                        <div class="school-body">
                            <img src="{{ asset('assets/images/dashboard-7/icon1.svg') }}" alt="total students">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card widget-hover overflow-hidden">
                <div class="card-header card-no-border pb-2">
                    <h5>Total Notices</h5>
                </div>
                <div class="card-body pt-0 count-student">
                    <div class="school-wrapper">
                        <div class="school-header">
                            <h4 class="txt-warning">{{ $communicationStats['total_notices'] ?? 0 }}</h4>
                        </div>
                        <div class="school-body">
                            <svg class="stroke-icon" style="width: 50px; height: 50px;">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-form') }}"></use>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card widget-hover overflow-hidden">
                <div class="card-header card-no-border pb-2">
                    <h5>Total Events</h5>
                </div>
                <div class="card-body pt-0 count-student">
                    <div class="school-wrapper">
                        <div class="school-header">
                            <h4 class="txt-success">{{ $communicationStats['total_events'] ?? 0 }}</h4>
                        </div>
                        <div class="school-body">
                            <svg class="stroke-icon" style="width: 50px; height: 50px;">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-calendar') }}"></use>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('admin.students.index') }}" class="btn btn-primary"><i data-feather="users" class="me-2"></i>View Students</a>
                        <a href="{{ route('admin.notices.index') }}" class="btn btn-warning"><i data-feather="bell" class="me-2"></i>Notices</a>
                        <a href="{{ route('admin.events.index') }}" class="btn btn-info"><i data-feather="calendar" class="me-2"></i>Events</a>
                        <a href="{{ route('admin.messaging.bulk.index') }}" class="btn btn-success"><i data-feather="mail" class="me-2"></i>Send Messages</a>
                    </div>
                </div>
            </div>
        </div>
        {{-- Notice Board for Receptionist --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header card-no-border">
                    <div class="header-top">
                        <h5>Notice Board</h5>
                        <a href="{{ route('admin.notices.index') }}" class="f-light text-decoration-underline">View All</a>
                    </div>
                </div>
                <div class="card-body pt-0 notice-board">
                    <ul>
                        @forelse($notices ?? [] as $notice)
                        <li class="d-flex {{ $loop->last ? 'pb-0' : '' }}">
                            <div class="activity-dot-{{ ['primary', 'secondary', 'success', 'warning'][$loop->index % 4] }}"></div>
                            <div class="ms-3">
                                <p class="d-flex mb-2">
                                    <span class="date-content light-background">{{ $notice->publish_date ? $notice->publish_date->format('d M, Y') : date('d M, Y') }}</span>
                                </p>
                                <h6>{{ $notice->title ?? 'Notice Title' }}</h6>
                                <p class="f-light">{{ $notice->creator->name ?? 'Admin' }}</p>
                            </div>
                        </li>
                        @empty
                        <li class="d-flex">
                            <div class="activity-dot-primary"></div>
                            <div class="ms-3"><p class="text-muted">No notices available</p></div>
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        {{-- Upcoming Events for Receptionist --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header card-no-border">
                    <div class="header-top">
                        <h5>Upcoming Events</h5>
                        <a href="{{ route('admin.events.index') }}" class="f-light text-decoration-underline">View All</a>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <ul class="list-group list-group-flush">
                        @forelse($upcomingEvents ?? [] as $event)
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">{{ $event->title }}</h6>
                                <small class="text-muted">{{ $event->start_date->format('M d, Y') }}</small>
                            </div>
                            <span class="badge bg-primary">{{ $event->start_date->diffForHumans() }}</span>
                        </li>
                        @empty
                        <li class="list-group-item px-0 text-center text-muted">No upcoming events</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Accountant Dashboard --}}
    @elseif($userRole === 'Accountant')
    <div class="row">
        <div class="col-sm-6 col-xl-3">
            <div class="card widget-hover overflow-hidden">
                <div class="card-header card-no-border pb-2">
                    <h5>Total Collection</h5>
                </div>
                <div class="card-body pt-0 count-student">
                    <div class="school-wrapper">
                        <div class="school-header">
                            <h4 class="txt-primary">₹{{ number_format($stats['total_income'] ?? 0) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card widget-hover overflow-hidden">
                <div class="card-header card-no-border pb-2">
                    <h5>Today's Collection</h5>
                </div>
                <div class="card-body pt-0 count-student">
                    <div class="school-wrapper">
                        <div class="school-header">
                            <h4 class="txt-success">₹{{ number_format($stats['today_collection'] ?? 0) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card widget-hover overflow-hidden">
                <div class="card-header card-no-border pb-2">
                    <h5>This Month</h5>
                </div>
                <div class="card-body pt-0 count-student">
                    <div class="school-wrapper">
                        <div class="school-header">
                            <h4 class="txt-warning">₹{{ number_format($stats['month_collection'] ?? 0) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card widget-hover overflow-hidden">
                <div class="card-header card-no-border pb-2">
                    <h5>Total Students</h5>
                </div>
                <div class="card-body pt-0 count-student">
                    <div class="school-wrapper">
                        <div class="school-header">
                            <h4 class="txt-info">{{ $stats['total_students'] ?? 0 }}</h4>
                        </div>
                        <div class="school-body">
                            <img src="{{ asset('assets/images/dashboard-7/icon1.svg') }}" alt="total students">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('admin.fees.collection') }}" class="btn btn-primary"><span style="font-size: 16px; font-weight: bold;" class="me-2">₹</span>Collect Fees</a>
                        <a href="{{ route('admin.fees.outstanding') }}" class="btn btn-warning"><i data-feather="alert-circle" class="me-2"></i>Outstanding Fees</a>
                        <a href="{{ route('admin.fees.reports.index') }}" class="btn btn-success"><i data-feather="bar-chart-2" class="me-2"></i>Fee Reports</a>
                        <a href="{{ route('admin.fees.reconciliation.index') }}" class="btn btn-info"><i data-feather="check-square" class="me-2"></i>Reconciliation</a>
                    </div>
                </div>
            </div>
        </div>
        {{-- Unpaid Fees Table for Accountant --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header card-no-border">
                    <div class="header-top">
                        <h5>Unpaid Fees</h5>
                        <a href="{{ route('admin.fees.outstanding') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                </div>
                <div class="card-body px-0 pt-0">
                    <div class="recent-table table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Class</th>
                                    <th>Fees</th>
                                    <th>Fine</th>
                                    <th>Total Due</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($unpaidFees ?? [] as $fee)
                                <tr>
                                    <td>
                                        <div class="common-align justify-content-start">
                                            <img class="rounded-circle me-2" src="{{ $fee->student->photo_url ?? asset('assets/images/dashboard/profile.png') }}" alt="user" style="width: 40px; height: 40px; object-fit: cover;">
                                            <div class="img-content-box">
                                                <span class="f-w-500">{{ $fee->student->full_name ?? 'Student Name' }}</span>
                                                <small class="text-muted d-block">{{ $fee->student->schoolClass->name ?? '' }} - {{ $fee->student->section->name ?? '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>₹{{ number_format($fee->total_fees ?? 0, 2) }}</td>
                                    <td class="{{ ($fee->fine_amount ?? 0) > 0 ? 'text-warning fw-bold' : '' }}">₹{{ number_format($fee->fine_amount ?? 0, 2) }}</td>
                                    <td class="text-danger fw-bold">₹{{ number_format($fee->pending_amount ?? 0, 2) }}</td>
                                    <td>
                                        <a href="{{ route('admin.fees.collect', $fee->student->id) }}" class="btn btn-sm btn-primary">Collect</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4"><p class="text-muted">No unpaid fees</p></td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- Notices for Accountant --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header card-no-border">
                    <h5>Recent Notices</h5>
                </div>
                <div class="card-body pt-0 notice-board">
                    <ul>
                        @forelse($notices ?? [] as $notice)
                        <li class="d-flex {{ $loop->last ? 'pb-0' : '' }}">
                            <div class="activity-dot-{{ ['primary', 'secondary', 'success', 'warning'][$loop->index % 4] }}"></div>
                            <div class="ms-3">
                                <p class="d-flex mb-2"><span class="date-content light-background">{{ $notice->publish_date ? $notice->publish_date->format('d M, Y') : date('d M, Y') }}</span></p>
                                <h6>{{ $notice->title ?? 'Notice Title' }}</h6>
                                <p class="f-light">{{ $notice->creator->name ?? 'Admin' }}</p>
                            </div>
                        </li>
                        @empty
                        <li class="d-flex">
                            <div class="activity-dot-primary"></div>
                            <div class="ms-3"><p class="text-muted">No notices available</p></div>
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @else
    {{-- Super Admin / Admin Full Dashboard --}}
    <div class="row">
        <div class="col-xxl-9 box-col-12">
            <div class="row">
                <!-- Academic Performance -->
                <div class="col-xxl-4 col-md-5">
                    <div class="card">
                        <div class="card-header card-no-border">
                            <div class="header-top">
                                <h5>Academic Performance</h5>
                                <div class="dropdown icon-dropdown">
                                    <button class="btn dropdown-toggle" id="performance_dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="icon-more-alt"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="performance_dropdown">
                                        <a class="dropdown-item academic-period active" href="#" data-period="this_month">This Month</a>
                                        <a class="dropdown-item academic-period" href="#" data-period="previous_month">Prev Month</a>
                                        <a class="dropdown-item academic-period" href="#" data-period="last_3_months">3 Months</a>
                                        <a class="dropdown-item academic-period" href="#" data-period="last_6_months">6 Months</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="performance-wrap">
                                <div id="academic_performance-chart"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- School Performance -->
                <div class="col-xxl-8 col-md-7">
                    <div class="card">
                        <div class="card-header card-no-border">
                            <div class="header-top">
                                <h5>School Performance</h5>
                                <div class="card-header-right-icon">
                                    <div class="dropdown">
                                        <button class="btn dropdown-toggle" id="viewButton" type="button" data-bs-toggle="dropdown" aria-expanded="false">This Month <i class="fa fa-angle-down ms-1"></i></button>
                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="viewButton">
                                            <a class="dropdown-item school-period" href="#" data-period="today">Today</a>
                                            <a class="dropdown-item school-period" href="#" data-period="this_week">This Week</a>
                                            <a class="dropdown-item school-period active" href="#" data-period="this_month">This Month</a>
                                            <a class="dropdown-item school-period" href="#" data-period="last_3_months">3 Months</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="school-performance-wrap">
                                <div id="chart-school-performance"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Teachers Card -->
                <div class="col-sm-4">
                    <div class="card widget-hover overflow-hidden">
                        <div class="card-header card-no-border pb-2">
                            <h5>Teachers</h5>
                        </div>
                        <div class="card-body pt-0 count-student">
                            <div class="school-wrapper">
                                <div class="school-header">
                                    <h4 class="txt-warning">{{ $stats['total_teachers'] }}</h4>
                                    <div class="d-flex gap-1 align-items-center flex-wrap pt-xxl-0 pt-2">
                                        <i class="icon-arrow-up f-light"></i>
                                        <span class="f-w-500 f-light">Active</span>
                                    </div>
                                </div>
                                <div class="school-body">
                                    <img src="{{ asset('assets/images/dashboard-7/icon-2.svg') }}" alt="total teachers">
                                    <div class="right-line">
                                        <img src="{{ asset('assets/images/dashboard-7/line.png') }}" alt="line">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Students Card -->
                <div class="col-sm-4">
                    <div class="card widget-hover overflow-hidden">
                        <div class="card-header card-no-border pb-2">
                            <h5>Students</h5>
                        </div>
                        <div class="card-body pt-0 count-student">
                            <div class="school-wrapper">
                                <div class="school-header">
                                    <h4 class="txt-primary">{{ $stats['total_students'] }}</h4>
                                    <div class="d-flex gap-1 align-items-center flex-wrap pt-xxl-0 pt-2">
                                        <i class="icon-arrow-up f-light"></i>
                                        <span class="f-w-500 f-light">Active</span>
                                    </div>
                                </div>
                                <div class="school-body">
                                    <img src="{{ asset('assets/images/dashboard-7/icon1.svg') }}" alt="total students">
                                    <div class="right-line">
                                        <img src="{{ asset('assets/images/dashboard-7/line.png') }}" alt="line">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Parents Card -->
                <div class="col-sm-4">
                    <div class="card widget-hover overflow-hidden">
                        <div class="card-header card-no-border pb-2">
                            <h5>Parents</h5>
                        </div>
                        <div class="card-body pt-0 count-student">
                            <div class="school-wrapper">
                                <div class="school-header">
                                    <h4 class="txt-success">{{ $stats['total_parents'] ?? 0 }}</h4>
                                    <div class="d-flex gap-1 align-items-center flex-wrap pt-xxl-0 pt-2">
                                        <i class="icon-arrow-up f-light"></i>
                                        <span class="f-w-500 f-light">Registered</span>
                                    </div>
                                </div>
                                <div class="school-body">
                                    <img src="{{ asset('assets/images/dashboard-7/icon-3.svg') }}" alt="Total parents">
                                    <div class="right-line">
                                        <img src="{{ asset('assets/images/dashboard-7/line.png') }}" alt="line">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- School Finance -->
                <div class="col-xl-4 col-sm-6 box-col-5">
                    <div class="card height-equal">
                        <div class="card-header card-no-border">
                            <div class="header-top">
                                <h5>School Finance</h5>
                                <div class="dropdown icon-dropdown">
                                    <button class="btn dropdown-toggle" id="income_dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="icon-more-alt"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="income_dropdown">
                                        <a class="dropdown-item finance-period active" href="#" data-period="this_month">This Month</a>
                                        <a class="dropdown-item finance-period" href="#" data-period="previous_month">Prev Month</a>
                                        <a class="dropdown-item finance-period" href="#" data-period="last_3_months">3 Months</a>
                                        <a class="dropdown-item finance-period" href="#" data-period="last_6_months">6 Months</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="income-wrapper">
                                <ul>
                                    <li>
                                        <div class="income-dot dot-primary"></div>
                                        <span class="text-muted">Income</span>
                                        <h6>₹{{ number_format($stats['total_income'] ?? 0) }}</h6>
                                    </li>
                                    <li>
                                        <div class="income-dot dot-warning"></div>
                                        <span class="text-muted">Expense</span>
                                        <h6>₹{{ number_format($stats['total_expense'] ?? 0) }}</h6>
                                    </li>
                                    <li>
                                        <div class="income-dot dot-success"></div>
                                        <span class="text-muted">Revenue</span>
                                        <h6>₹{{ number_format($stats['total_revenue'] ?? 0) }}</h6>
                                    </li>
                                </ul>
                                <div class="main-income-chart">
                                    <div id="income_chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance Overview -->
                <div class="col-xl-8 col-12 order-1 order-xl-0 box-col-7">
                    <div class="card height-equal">
                        <div class="card-header">
                            <div class="header-top">
                                <h5 class="m-0">Performance Overview</h5>
                                <div class="performance-right">
                                    <p class="mb-0">{{ date('d-m-Y') }}</p>
                                    <i class="fa-solid fa-calendar txt-primary"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-md-0 g-4">
                                <div class="col-xl-5 col-md-4 box-col-12">
                                    <div class="attendance-chart">
                                        <div id="chart_current_academic"></div>
                                    </div>
                                </div>
                                <div class="col-xl-7 col-md-8 box-col-none">
                                    <div class="row g-3">
                                        <div class="col-xl-12">
                                            <div class="light-card attendance-card widget-hover">
                                                <div class="left-overview-content">
                                                    <div class="svg-box">
                                                        <img src="{{ asset('assets/images/dashboard-7/attendance/1.png') }}" alt="homework">
                                                    </div>
                                                </div>
                                                <div class="right-overview-content">
                                                    <div>
                                                        <h6>Homework</h6>
                                                        <span class="text-muted text-ellipsis">Bring Something into the Classroom...</span>
                                                    </div>
                                                    <div class="d-flex marks-count">
                                                        <h5>{{ $stats['homework_completion'] ?? 89 }}/<sub class="text-muted">100</sub></h5>
                                                        <div class="d-flex justify-content-center align-items-center">
                                                            <i class="icon-arrow-up txt-success pe-2 f-w-600"></i>
                                                            <span class="txt-success f-w-500">+80%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-12">
                                            <div class="light-card attendance-card widget-hover">
                                                <div class="left-overview-content">
                                                    <div class="svg-box">
                                                        <img src="{{ asset('assets/images/dashboard-7/attendance/2.png') }}" alt="tests">
                                                    </div>
                                                </div>
                                                <div class="right-overview-content">
                                                    <div>
                                                        <h6>Tests</h6>
                                                        <span class="text-muted text-ellipsis">These 5 study tips can help you take...</span>
                                                    </div>
                                                    <div class="d-flex marks-count">
                                                        <h5>{{ $stats['test_average'] ?? 95 }}/<sub class="text-muted">100</sub></h5>
                                                        <div class="d-flex justify-content-center align-items-center">
                                                            <i class="icon-arrow-up txt-success pe-2 f-w-600"></i>
                                                            <span class="txt-success f-w-500">+97%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-12">
                                            <div class="light-card attendance-card widget-hover">
                                                <div class="left-overview-content">
                                                    <div class="svg-box">
                                                        <img src="{{ asset('assets/images/dashboard-7/attendance/3.png') }}" alt="attendance">
                                                    </div>
                                                </div>
                                                <div class="right-overview-content">
                                                    <div>
                                                        <h6>Attendance</h6>
                                                        <span class="text-muted text-ellipsis">Student absence reduces even best...</span>
                                                    </div>
                                                    <div class="d-flex marks-count">
                                                        <h5>{{ $stats['attendance_rate'] ?? 92 }}/<sub class="text-muted">100</sub></h5>
                                                        <div class="d-flex justify-content-center align-items-center">
                                                            <i class="icon-arrow-up txt-success pe-2 f-w-600"></i>
                                                            <span class="txt-success f-w-500">+94%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- School Calendar -->
                <div class="col-xl-4 col-sm-6 order-0">
                    <div class="card default-inline-calender">
                        <div class="card-header card-no-border">
                            <div class="header-top">
                                <h5>School Calendar</h5>
                                <div class="dropdown icon-dropdown">
                                    <button class="btn dropdown-toggle" id="calender_dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="icon-more-alt"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="calender_dropdown">
                                        <a class="dropdown-item" href="#">This Month</a>
                                        <a class="dropdown-item" href="#">Previous Month</a>
                                        <a class="dropdown-item" href="#">Last 3 Months</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0 school-calender">
                            <div class="input-group main-inline-calender">
                                <input class="form-control" id="inline-calender2" type="date">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Today's Task -->
                <div class="col-xl-8 order-2">
                    <div class="card">
                        <div class="card-header card-no-border">
                            <div class="header-top">
                                <h5>Today's Task</h5>
                                <div class="dropdown icon-dropdown">
                                    <button class="btn dropdown-toggle" id="task_dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="icon-more-alt"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="task_dropdown">
                                        <a class="dropdown-item" href="#">This Month</a>
                                        <a class="dropdown-item" href="#">Previous Month</a>
                                        <a class="dropdown-item" href="#">Last 3 Months</a>
                                        <a class="dropdown-item" href="#">Last 6 Months</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0 task-table">
                            <div class="main-task">
                                <span class="text-muted">{{ $completedTasks ?? 0 }} Task <span class="txt-success">completed <span class="text-muted"> out of {{ $totalTasks ?? 0 }}</span></span></span>
                                <div class="progress task-progress">
                                    <div class="progress-bar w-{{ $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0 }} bg-success" role="progressbar" aria-label="Task Progress" aria-valuenow="{{ $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            <div class="recent-table table-responsive currency-table task-table">
                                <table class="table">
                                    <tbody class="main-task-wrapper">
                                        @forelse($tasks ?? [] as $task)
                                        <tr class="{{ $loop->even ? 'light-card' : '' }}">
                                            <td>
                                                <div class="d-flex">
                                                    <div class="form-check checkbox-width checkbox checkbox-primary mb-0">
                                                        <input class="from-check-input" id="checkbox-task-{{ $task->id ?? $loop->index }}" type="checkbox" {{ ($task->status ?? '') == 'completed' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="checkbox-task-{{ $task->id ?? $loop->index }}"></label>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2 justify-content-center">
                                                        <div>
                                                            <a class="pb-1" href="#">{{ $task->title ?? 'Task Title' }}</a>
                                                            <ul class="task-icons">
                                                                <li><span class="text-muted">{{ $task->class ?? 'Class' }}</span></li>
                                                                <li class="f-light flex-wrap">
                                                                    <svg class="fill-icon fill-primary">
                                                                        <use href="{{ asset('assets/svg/icon-sprite.svg#clock') }}"></use>
                                                                    </svg>
                                                                    <span>{{ $task->time ?? '09:00 AM' }}</span>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <button class="btn {{ ($task->status ?? '') == 'completed' ? 'badge-light-success' : 'button-primary' }}">
                                                    {{ ($task->status ?? '') == 'completed' ? 'Done' : 'In Progress' }}
                                                </button>
                                            </td>
                                            <td class="icons-box">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="square-white"><i class="fa-solid fa-pencil"></i></div>
                                                    <div class="square-white"><i class="fa-solid fa-trash"></i></div>
                                                    <div class="square-white"><i class="fa-solid fa-print"></i></div>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4">
                                                <p class="text-muted">No tasks for today</p>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="col-xxl-3 d-xxl-block d-none box-col-none">
            <div class="row">
                <!-- Knowledge Base -->
                <div class="col-xl-12 d-xl-block d-none">
                    <div class="card">
                        <div class="card-header card-no-border pb-4">
                            <h5>Increase your knowledge by Learning!</h5>
                        </div>
                        <div class="card-body pt-0 position-relative pb-0 pe-0 increase-content">
                            <div class="knowledge-wrapper">
                                <div>
                                    <p class="f-light">The essential way to learn about anything is by reading quality literature!</p>
                                    <a class="btn btn-primary btn-hover-effect f-w-500 knowledge-btn" href="{{ route('admin.library.books.index') }}">Learn More</a>
                                </div>
                                <div class="knowledgebase-wrapper">
                                    <img class="knowledge-img img-fluid w-100" src="{{ asset('assets/images/dashboard-7/knowledge-base.png') }}" alt="knowledge-base">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Leave Applications -->
                @if($pendingLeavesCount > 0)
                <div class="col-xl-12 d-xl-block d-none">
                    <div class="card">
                        <div class="card-header card-no-border">
                            <div class="header-top">
                                <h5>Pending Leaves <span class="badge badge-light-warning ms-2">{{ $pendingLeavesCount }}</span></h5>
                                <a href="{{ route('admin.leaves.index') }}" class="f-light text-decoration-underline">View All</a>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <ul class="list-group list-group-flush">
                                @foreach($pendingLeaves as $leave)
                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-2">
                                        @if($leave->student && $leave->student->photo)
                                            <img src="{{ asset('storage/' . $leave->student->photo) }}" alt="" class="rounded-circle" width="35" height="35">
                                        @else
                                            <div class="bg-light-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                <span class="text-primary">{{ $leave->student ? substr($leave->student->first_name, 0, 1) : '?' }}</span>
                                            </div>
                                        @endif
                                        <div>
                                            <span class="f-w-500">{{ $leave->student->first_name ?? '' }} {{ $leave->student->last_name ?? '' }}</span>
                                            <br><small class="text-muted">{{ $leave->student->schoolClass->name ?? '' }} - {{ $leave->from_date->format('M d') }}</small>
                                        </div>
                                    </div>
                                    <a href="{{ route('admin.leaves.show', $leave) }}" class="btn btn-sm btn-outline-primary">Review</a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Notice Board -->
                <div class="col-xl-12 notification box-col-6 d-xl-block d-none">
                    <div class="card">
                        <div class="card-header card-no-border">
                            <div class="header-top">
                                <h5>Notice Board</h5>
                                <div class="dropdown icon-dropdown">
                                    <button class="btn dropdown-toggle" id="notice_dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="icon-more-alt"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="notice_dropdown">
                                        <a class="dropdown-item" href="#">This Month</a>
                                        <a class="dropdown-item" href="#">Previous Month</a>
                                        <a class="dropdown-item" href="#">Last 3 Months</a>
                                        <a class="dropdown-item" href="#">Last 6 Months</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0 notice-board">
                            <ul>
                                @forelse($notices ?? [] as $notice)
                                <li class="d-flex {{ $loop->last ? 'pb-0' : '' }}">
                                    <div class="activity-dot-{{ ['primary', 'secondary', 'success', 'warning'][$loop->index % 4] }}"></div>
                                    <div class="ms-3">
                                        <p class="d-flex mb-2">
                                            <span class="date-content light-background">{{ $notice->publish_date ? $notice->publish_date->format('d M, Y') : date('d M, Y') }}</span>
                                        </p>
                                        <h6>{{ $notice->title ?? 'Notice Title' }}<span class="dot-notification"></span></h6>
                                        <p class="f-light">{{ $notice->creator->name ?? 'Admin' }} / {{ $notice->publish_date ? $notice->publish_date->diffForHumans() : 'Just now' }}
                                            @if($loop->first)
                                            <span class="badge alert-light-success txt-success ms-2 f-w-600">New</span>
                                            @endif
                                        </p>
                                    </div>
                                </li>
                                @empty
                                <li class="d-flex">
                                    <div class="activity-dot-primary"></div>
                                    <div class="ms-3">
                                        <p class="text-muted">No notices available</p>
                                    </div>
                                </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Shining Stars -->
                <div class="col-xl-12 d-xl-block d-none">
                    <div class="card">
                        <div class="card-header card-no-border">
                            <div class="header-top">
                                <h5>Shining Stars</h5>
                                <div class="dropdown icon-dropdown">
                                    <button class="btn dropdown-toggle" id="students_dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="icon-more-alt"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="students_dropdown">
                                        <a class="dropdown-item" href="#">This Month</a>
                                        <a class="dropdown-item" href="#">Previous Month</a>
                                        <a class="dropdown-item" href="#">Last 3 Months</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="student-leader-wrapper">
                                @forelse($topStudents ?? [] as $index => $student)
                                <div class="student-leader-content {{ $index < 3 ? 'light-card' : '' }}">
                                    <div class="d-flex align-items-center gap-2">
                                        @if($index < 3)
                                        <img src="{{ asset('assets/images/dashboard-7/attendance/student-leader/rank-' . ($index + 1) . '.svg') }}" alt="rank-{{ $index + 1 }}">
                                        @else
                                        <h5>{{ $index + 1 }}<sup>{{ $index == 3 ? 'th' : 'th' }}</sup></h5>
                                        @endif
                                        <img class="leader-img" src="{{ $student->photo_url ?? asset('assets/images/dashboard/profile.png') }}" alt="user">
                                        <div class="leader-content-height">
                                            <h6>{{ $student->first_name ?? 'Student' }}<span class="c-o-light f-14 f-w-400 ps-1">({{ $student->schoolClass->name ?? 'Grade' }})</span></h6>
                                        </div>
                                    </div>
                                    <span class="f-14 txt-primary">{{ $student->percentage ?? '0' }}%</span>
                                </div>
                                @empty
                                <div class="text-center py-3">
                                    <p class="text-muted">No data available</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Unpaid Fees Table -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header card-no-border">
                    <div class="header-top">
                        <h5>Unpaid Fees</h5>
                        <div class="card-header-right-icon">
                            <a href="{{ route('admin.fees.outstanding') }}" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 unpaid-fees-table">
                    <div class="recent-table table-responsive">
                        <table class="table" id="unpaid-fees">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Class</th>
                                    <th>Fees</th>
                                    <th>Fine</th>
                                    <th>Total Due</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($unpaidFees ?? [] as $fee)
                                <tr>
                                    <td>
                                        <div class="common-align justify-content-start">
                                            <img class="rounded-circle me-2" src="{{ $fee->student->photo_url ?? asset('assets/images/dashboard/profile.png') }}" alt="user" style="width: 40px; height: 40px; object-fit: cover;">
                                            <div class="img-content-box">
                                                <a class="f-w-500" href="{{ route('admin.students.show', $fee->student->id) }}">{{ $fee->student->full_name ?? 'Student Name' }}</a>
                                                <small class="text-muted d-block">{{ $fee->student->schoolClass->name ?? '' }} - {{ $fee->student->section->name ?? '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>₹{{ number_format($fee->total_fees ?? 0, 2) }}</td>
                                    <td class="{{ ($fee->fine_amount ?? 0) > 0 ? 'text-warning fw-bold' : '' }}">
                                        ₹{{ number_format($fee->fine_amount ?? 0, 2) }}
                                    </td>
                                    <td class="text-danger fw-bold">₹{{ number_format($fee->pending_amount ?? 0, 2) }}</td>
                                    <td>
                                        <a href="{{ route('admin.fees.collect', $fee->student->id) }}" class="btn btn-sm btn-primary">
                                            Collect
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <p class="text-muted">No unpaid fees</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Students Table -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header card-no-border">
                    <div class="header-top">
                        <h5>Top Students</h5>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 top-student-table">
                    <div class="recent-table table-responsive">
                        <table class="table" id="top-students">
                            <thead>
                                <tr>
                                    <th style="width: 5%; padding-left: 20px;">#</th>
                                    <th style="width: 30%;">Student</th>
                                    <th style="width: 15%;">Marks</th>
                                    <th style="width: 15%;">Percentage</th>
                                    <th style="width: 20%;">Class</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topPerformers ?? [] as $index => $performer)
                                <tr>
                                    <td style="padding-left: 20px;">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($performer->photo)
                                                <img class="rounded-circle me-2" src="{{ asset('storage/' . $performer->photo) }}" alt="" style="width: 36px; height: 36px; object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-primary me-2 d-flex align-items-center justify-content-center text-white" style="width: 36px; height: 36px; font-size: 14px; flex-shrink: 0;">
                                                    {{ strtoupper(substr($performer->first_name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <span class="f-w-500">{{ $performer->first_name }} {{ $performer->last_name }}</span>
                                                <br><small class="text-muted">{{ $performer->admission_no }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><strong>{{ number_format($performer->total_marks, 0) }}</strong><small class="text-muted">/{{ number_format($performer->total_full, 0) }}</small></td>
                                    <td>
                                        <span class="badge badge-light-{{ $performer->percentage >= 80 ? 'success' : ($performer->percentage >= 60 ? 'primary' : 'warning') }} px-2">
                                            {{ $performer->percentage }}%
                                        </span>
                                    </td>
                                    <td>{{ $performer->class_name ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <p class="text-muted mb-0">No exam data available yet</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students Gender Distribution -->
        <div class="col-xl-3 col-sm-5 order-xl-0 order-sm-1">
            <div class="card">
                <div class="card-header card-no-border">
                    <div class="header-top student-header">
                        <h5>Students</h5>
                        <div class="card-header-right-icon">
                            <!-- Class Dropdown -->
                            <div class="dropdown">
                                <button class="btn dropdown-toggle" id="classDropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">All Classes <i class="fa fa-angle-down ms-1"></i></button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item student-filter-class active" href="#!">All Classes</a></li>
                                    @foreach($classWiseStudents as $class)
                                    <li><a class="dropdown-item student-filter-class" href="#!" data-class-id="{{ $class->id }}">{{ $class->name }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body pb-0">
                    <div class="std-class-chart">
                        <div id="student-chart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- New Enrolled Students -->
        <div class="col-xl-5 col-12 order-xl-0 order-sm-3">
            <div class="card">
                <div class="card-header card-no-border">
                    <div class="header-top">
                        <h5>New Enrolled Students</h5>
                        <div class="card-header-right-icon">
                            <div class="dropdown icon-dropdown">
                                <button class="btn dropdown-toggle" id="enrollStudent" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="icon-more-alt"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="enrollStudent">
                                    <a class="dropdown-item" href="#!">Today</a>
                                    <a class="dropdown-item" href="#!">This Week</a>
                                    <a class="dropdown-item" href="#!">This Month</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 new-enroll-student">
                    <div class="recent-table table-responsive custom-scrollbar">
                        <table class="table" id="enroll-student">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>ID</th>
                                    <th>Standard</th>
                                    <th>Section</th>
                                    <th>Admission Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentStudents ?? [] as $student)
                                <tr>
                                    <td>
                                        <div class="common-align justify-content-start">
                                            <img class="rounded-circle me-2" src="{{ $student->photo_url ?? asset('assets/images/dashboard/profile.png') }}" alt="user" style="width: 40px; height: 40px; object-fit: cover;">
                                            <div class="img-content-box">
                                                <a class="f-w-500" href="{{ route('admin.students.show', $student) }}">{{ $student->full_name }}</a>
                                            </div>
                                        </div>
                                    </td>
                                    <td>#{{ $student->admission_no }}</td>
                                    <td>{{ $student->schoolClass->name ?? '-' }}</td>
                                    <td>{{ $student->section->name ?? '-' }}</td>
                                    <td>{{ $student->created_at ? $student->created_at->format('M d, Y') : '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <p class="text-muted">No students enrolled yet</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance -->
        <div class="col-xl-4 col-sm-7 order-xl-0 order-sm-2">
            <div class="card sales-report">
                <div class="card-header card-no-border">
                    <div class="header-top">
                        <h5>Attendance</h5>
                        <div class="card-header-right-icon">
                            <div class="dropdown icon-dropdown">
                                <button class="btn dropdown-toggle" id="attendanceDropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="icon-more-alt"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="attendanceDropdown">
                                    <a class="dropdown-item attendance-period" href="#" data-period="today">Today</a>
                                    <a class="dropdown-item attendance-period" href="#" data-period="this_week">This Week</a>
                                    <a class="dropdown-item attendance-period active" href="#" data-period="this_month">This Month</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="d-flex align-items-center gap-3 pb-3">
                        <div class="sales-report-chart">
                            <div id="attendance-chart"></div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 pb-2">
                                <span class="bg-primary" style="width: 10px; height: 10px; border-radius: 50%;"></span>
                                <span class="f-light">Present</span>
                                <span class="ms-auto f-w-500">{{ $attendanceStats['present'] ?? 0 }}%</span>
                            </div>
                            <div class="d-flex align-items-center gap-2 pb-2">
                                <span class="bg-secondary" style="width: 10px; height: 10px; border-radius: 50%;"></span>
                                <span class="f-light">Absent</span>
                                <span class="ms-auto f-w-500">{{ $attendanceStats['absent'] ?? 0 }}%</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="bg-success" style="width: 10px; height: 10px; border-radius: 50%;"></span>
                                <span class="f-light">Late</span>
                                <span class="ms-auto f-w-500">{{ $attendanceStats['late'] ?? 0 }}%</span>
                            </div>
                        </div>
                    </div>
                    <ul class="balance-box d-flex justify-content-between">
                        <li>
                            <span class="f-light d-block mb-1">Total Students</span>
                            <h6 class="f-w-600">{{ $stats['total_students'] }}</h6>
                        </li>
                        <li>
                            <span class="f-light d-block mb-1">Present Today</span>
                            <h6 class="txt-success f-w-600">{{ $attendanceStats['present_count'] ?? 0 }}</h6>
                        </li>
                        <li>
                            <span class="f-light d-block mb-1">Absent Today</span>
                            <h6 class="txt-danger f-w-600">{{ $attendanceStats['absent_count'] ?? 0 }}</h6>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
@if(in_array($userRole, ['Super Admin', 'Admin']))
<script src="{{ asset('assets/js/chart/apex-chart/apex-chart.js') }}"></script>
<script src="{{ asset('assets/js/chart/apex-chart/stock-prices.js') }}"></script>
<script src="{{ asset('assets/js/counter/counter-custom.js') }}"></script>
<script src="{{ asset('assets/js/flat-pickr/flatpickr.js') }}"></script>
<script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/datatable/datatables/dataTables.js') }}"></script>
<script>
	// Pass PHP data to JavaScript for charts
	window.dashboardData = {
		genderStats: {
			male: {{ $genderStats['male'] ?? 0 }},
			female: {{ $genderStats['female'] ?? 0 }},
			other: {{ $genderStats['other'] ?? 0 }}
		},
		totalStudents: {{ $stats['total_students'] ?? 0 }},
		attendanceStats: {
			present: {{ $attendanceStats['present'] ?? 0 }},
			absent: {{ $attendanceStats['absent'] ?? 0 }}
		},
		classes: {!! json_encode($classWiseStudents->map(function($class) {
			return [
				'id' => $class->id,
				'name' => $class->name,
				'sections' => $class->sections->map(function($section) {
					return ['id' => $section->id, 'name' => $section->name];
				})
			];
		})) !!}
	};
</script>
<script>
	// Student Chart Filter Functionality
	jQuery(document).ready(function() {
		var studentLoading = false;

		function createStudentDonut(el, data) {
			return new ApexCharts(el, {
				series: data.series,
				labels: data.labels,
				chart: { height: 338, type: 'donut' },
				plotOptions: { pie: { expandOnClick: false, donut: { size: '75%', labels: { show: true, name: { offsetY: 4 }, value: { fontSize: '14px', offsetY: 10, fontFamily: 'Rubik, sans-serif', fontWeight: 400, color: '#52526C' }, total: { show: true, fontSize: '20px', fontWeight: 500, fontFamily: 'Rubik, sans-serif', label: data.total.toString(), formatter: function() { return 'Total'; } } } } } },
				dataLabels: { enabled: false },
				colors: data.colors,
				fill: { type: 'solid' },
				legend: { show: true, position: 'bottom', horizontalAlign: 'center', fontSize: '14px', fontFamily: 'Rubik, sans-serif', fontWeight: 500, labels: { colors: 'var(--chart-text-color)' }, formatter: function(seriesName, opts) { return [seriesName, ' - ', opts.w.globals.series[opts.seriesIndex]]; }, markers: { width: 8, height: 8 } },
				stroke: { width: 0 },
				responsive: [{ breakpoint: 576, options: { chart: { height: 280 } } }]
			});
		}

		jQuery(document).on('click', '.student-filter-class', function(e) {
			e.preventDefault();
			if (studentLoading) return;
			studentLoading = true;

			var classId = jQuery(this).data('class-id') || '';
			var className = jQuery(this).text();

			jQuery('#classDropdown').html(className + ' <i class="fa fa-angle-down ms-1"></i>');
			jQuery('.student-filter-class').removeClass('active');
			jQuery(this).addClass('active');

			var wrap = jQuery('#student-chart').parent();
			wrap.css('position', 'relative');
			if (!wrap.find('.chart-loader').length) {
				wrap.append('<div class="chart-loader" style="position:absolute;top:0;left:0;right:0;bottom:0;background:rgba(255,255,255,0.7);display:flex;align-items:center;justify-content:center;z-index:5;"><div class="spinner-border spinner-border-sm text-primary"></div></div>');
			}
			wrap.find('.chart-loader').show();

			jQuery.ajax({
				url: '{{ route("admin.dashboard.student-stats") }}',
				type: 'GET',
				data: { class_id: classId },
				success: function(data) {
					console.log('Student data:', data);

					if (window.studentChartInstance) {
						try { window.studentChartInstance.destroy(); } catch(ex) {}
						window.studentChartInstance = null;
					}

					var el = document.getElementById('student-chart');
					el.innerHTML = '';

					setTimeout(function() {
						window.studentChartInstance = createStudentDonut(el, data);
						window.studentChartInstance.render();
						wrap.find('.chart-loader').hide();
						studentLoading = false;
					}, 100);
				},
				error: function(xhr) {
					console.error('Student API error:', xhr.status);
					wrap.find('.chart-loader').hide();
					studentLoading = false;
				}
			});
		});
	});
</script>
<script>
	// Global dropdown item click handler for dashboard filter dropdowns
	jQuery(document).ready(function() {
		// Handle all card dropdown items (exclude student-filter-class which has its own handler)
		jQuery(document).on('click', '.card .dropdown-menu .dropdown-item:not(.student-filter-class)', function(e) {
			e.preventDefault();

			var dropdown = jQuery(this).closest('.dropdown');
			var toggleBtn = dropdown.find('.dropdown-toggle');
			var selectedText = jQuery(this).text().trim();

			// Mark selected item as active
			jQuery(this).closest('.dropdown-menu').find('.dropdown-item').removeClass('active');
			jQuery(this).addClass('active');

			// Update button text only for text-based toggles (not icon three-dot buttons)
			if (!dropdown.hasClass('icon-dropdown')) {
				toggleBtn.html(selectedText + ' <i class="fa fa-angle-down ms-1"></i>');
			}
		});
	});
</script>
<script src="{{ asset('assets/js/dashboard/dashboard_7.js') }}"></script>
<script>
jQuery(document).ready(function() {
	var academicLoading = false;
	var schoolLoading = false;

	function createAcademicChart(el, data) {
		return new ApexCharts(el, {
			series: data.series,
			fill: { type: 'gradient', gradient: { type: 'vertical', shadeIntensity: 0.4, gradientToColors: '#54BA4A', opacityFrom: 0.4, opacityTo: 0, stops: [0, 90, 100], colorStops: [] } },
			chart: { height: 230, type: 'area', dropShadow: { enabled: true, color: '#54BA4A', top: 8, left: 0, blur: 2, opacity: 0.2 }, toolbar: { show: false } },
			colors: ['#54BA4A', '#54BA4A'],
			dataLabels: { enabled: true },
			stroke: { curve: 'smooth', width: 3 },
			tooltip: { x: { show: false }, z: { show: false } },
			markers: { size: 1 },
			xaxis: { categories: data.categories, axisTicks: { show: false }, axisBorder: { show: false } },
			yaxis: { min: 0, max: 160, tickAmount: 4 },
			legend: { position: 'top', horizontalAlign: 'right', floating: true, offsetY: -25, offsetX: -5 },
			responsive: [{ breakpoint: 1131, options: { chart: { height: 210 } } }, { breakpoint: 1007, options: { chart: { height: 225 } } }]
		});
	}

	function createSchoolChart(el, data) {
		return new ApexCharts(el, {
			series: data.series,
			chart: { height: 220, type: 'line', stacked: false, toolbar: { show: false }, dropShadow: { enabled: true, top: 4, left: 0, blur: 2, color: '#7366FF', opacity: 0.02 } },
			stroke: { width: [3, 3], curve: 'smooth' },
			grid: { show: true, borderColor: 'var(--chart-border)', strokeDashArray: 0, position: 'back', xaxis: { lines: { show: true } } },
			colors: ['#7366FF', '#54BA4A'],
			fill: { type: ['gradient', 'solid'], gradient: { shade: 'light', type: 'vertical', opacityFrom: 0.6, opacityTo: 0, stops: [0, 100] } },
			labels: data.labels,
			markers: { hover: { size: 6, sizeOffset: 0 } },
			xaxis: { type: 'category', tickAmount: 4, tickPlacement: 'on', tooltip: { enabled: false }, axisBorder: { color: 'var(--chart-border)' }, axisTicks: { show: false } },
			legend: { show: false },
			yaxis: { min: 0, max: 100, tickAmount: 5 },
			tooltip: { shared: false, intersect: false },
			responsive: [{ breakpoint: 1200, options: { chart: { height: 250 } } }, { breakpoint: 1201, options: { chart: { height: 260 } } }]
		});
	}

	// ─── Academic Performance ───
	jQuery(document).on('click', '.academic-period', function(e) {
		e.preventDefault();
		if (academicLoading) return;
		academicLoading = true;

		var period = jQuery(this).data('period');
		var wrap = jQuery('#academic_performance-chart').parent();

		// Show loading
		wrap.css('position', 'relative');
		if (!wrap.find('.chart-loader').length) {
			wrap.append('<div class="chart-loader" style="position:absolute;top:0;left:0;right:0;bottom:0;background:rgba(255,255,255,0.7);display:flex;align-items:center;justify-content:center;z-index:5;"><div class="spinner-border spinner-border-sm text-success"></div></div>');
		}
		wrap.find('.chart-loader').show();

		jQuery.ajax({
			url: '{{ route("admin.dashboard.academic-performance") }}',
			data: { period: period },
			success: function(data) {
				console.log('Academic data:', data);

				// Destroy old
				if (window._academicChart) {
					try { window._academicChart.destroy(); } catch(ex) { console.warn('Academic destroy error:', ex); }
					window._academicChart = null;
				}

				var el = document.getElementById('academic_performance-chart');
				el.innerHTML = '';

				// Wait for DOM to settle then create fresh chart
				setTimeout(function() {
					window._academicChart = createAcademicChart(el, data);
					window._academicChart.render();
					wrap.find('.chart-loader').hide();
					academicLoading = false;
				}, 100);
			},
			error: function(xhr) {
				console.error('Academic API error:', xhr.status, xhr.responseText);
				wrap.find('.chart-loader').hide();
				academicLoading = false;
				document.getElementById('academic_performance-chart').innerHTML = '<div class="text-center py-4 text-muted" style="font-size:13px;">Failed to load</div>';
			}
		});
	});

	// ─── School Performance ───
	jQuery(document).on('click', '.school-period', function(e) {
		e.preventDefault();
		if (schoolLoading) return;
		schoolLoading = true;

		var period = jQuery(this).data('period');
		var text = jQuery(this).text().trim();
		var wrap = jQuery('#chart-school-performance').parent();

		jQuery('#viewButton').html(text + ' <i class="fa fa-angle-down ms-1"></i>');

		// Show loading
		wrap.css('position', 'relative');
		if (!wrap.find('.chart-loader').length) {
			wrap.append('<div class="chart-loader" style="position:absolute;top:0;left:0;right:0;bottom:0;background:rgba(255,255,255,0.7);display:flex;align-items:center;justify-content:center;z-index:5;"><div class="spinner-border spinner-border-sm text-primary"></div></div>');
		}
		wrap.find('.chart-loader').show();

		jQuery.ajax({
			url: '{{ route("admin.dashboard.school-performance") }}',
			data: { period: period },
			success: function(data) {
				console.log('School data:', data);

				// Destroy old
				if (window._schoolChart) {
					try { window._schoolChart.destroy(); } catch(ex) { console.warn('School destroy error:', ex); }
					window._schoolChart = null;
				}

				var el = document.getElementById('chart-school-performance');
				el.innerHTML = '';

				// Wait for DOM to settle then create fresh chart
				setTimeout(function() {
					window._schoolChart = createSchoolChart(el, data);
					window._schoolChart.render();
					wrap.find('.chart-loader').hide();
					schoolLoading = false;
				}, 100);
			},
			error: function(xhr) {
				console.error('School API error:', xhr.status, xhr.responseText);
				wrap.find('.chart-loader').hide();
				schoolLoading = false;
				document.getElementById('chart-school-performance').innerHTML = '<div class="text-center py-4 text-muted" style="font-size:13px;">Failed to load</div>';
			}
		});
	});

	// ─── School Finance ───
	var financeLoading = false;

	function createFinanceChart(el, data) {
		return new ApexCharts(el, {
			series: data.series,
			chart: { height: 265, type: 'line', toolbar: { show: false }, dropShadow: { enabled: true, top: 4, left: 0, blur: 2, colors: ['#7366FF', '#54BA4A', '#FFAA05'], opacity: 0.02 } },
			grid: { show: false, xaxis: { lines: { show: false } } },
			colors: ['#7366FF', '#54BA4A', '#FFAA05'],
			stroke: { width: 3, curve: 'smooth', opacity: 1 },
			tooltip: { shared: false, intersect: false, marker: { width: 5, height: 5 } },
			xaxis: { type: 'category', categories: data.categories, crosshairs: { show: false }, labels: { style: { colors: 'var(--chart-text-color)', fontSize: '12px', fontFamily: 'Rubik, sans-serif', fontWeight: 400 } }, axisTicks: { show: false }, axisBorder: { show: false }, tooltip: { enabled: false } },
			fill: { opacity: 1, type: 'gradient', gradient: { shade: 'light', type: 'horizontal', shadeIntensity: 1, opacityFrom: 0.95, opacityTo: 1, stops: [0, 90, 100] } },
			yaxis: { tickAmount: 5, labels: { show: false } },
			legend: { show: false },
			responsive: [{ breakpoint: 1736, options: { chart: { height: 230 } } }, { breakpoint: 1401, options: { chart: { height: 250 } } }, { breakpoint: 1200, options: { chart: { height: 250 } } }, { breakpoint: 1007, options: { chart: { height: 230 } } }]
		});
	}

	jQuery(document).on('click', '.finance-period', function(e) {
		e.preventDefault();
		if (financeLoading) return;
		financeLoading = true;

		var period = jQuery(this).data('period');
		var wrap = jQuery('#income_chart').parent();

		wrap.css('position', 'relative');
		if (!wrap.find('.chart-loader').length) {
			wrap.append('<div class="chart-loader" style="position:absolute;top:0;left:0;right:0;bottom:0;background:rgba(255,255,255,0.7);display:flex;align-items:center;justify-content:center;z-index:5;"><div class="spinner-border spinner-border-sm text-primary"></div></div>');
		}
		wrap.find('.chart-loader').show();

		jQuery.ajax({
			url: '{{ route("admin.dashboard.finance-performance") }}',
			data: { period: period },
			success: function(data) {
				console.log('Finance data:', data);

				if (window._financeChart) {
					try { window._financeChart.destroy(); } catch(ex) {}
					window._financeChart = null;
				}

				// Update summary numbers
				if (data.totals) {
					jQuery('.income-wrapper li:eq(0) h6').text('₹' + (data.totals.income > 0 ? data.totals.income.toLocaleString('en-IN') + 'K' : '0'));
					jQuery('.income-wrapper li:eq(1) h6').text('₹' + (data.totals.expense > 0 ? data.totals.expense.toLocaleString('en-IN') + 'K' : '0'));
					jQuery('.income-wrapper li:eq(2) h6').text('₹' + (data.totals.revenue > 0 ? data.totals.revenue.toLocaleString('en-IN') + 'K' : '0'));
				}

				var el = document.getElementById('income_chart');
				el.innerHTML = '';

				setTimeout(function() {
					window._financeChart = createFinanceChart(el, data);
					window._financeChart.render();
					wrap.find('.chart-loader').hide();
					financeLoading = false;
				}, 100);
			},
			error: function(xhr) {
				console.error('Finance API error:', xhr.status, xhr.responseText);
				wrap.find('.chart-loader').hide();
				financeLoading = false;
			}
		});
	});

	// ─── Attendance Chart ───
	var attendanceLoading = false;

	function createAttendanceChart(el, data) {
		return new ApexCharts(el, {
			series: data.series,
			chart: { type: 'bar', height: 340, toolbar: { show: false } },
			plotOptions: { bar: { horizontal: false, columnWidth: '55%', endingShape: 'rounded' } },
			dataLabels: { enabled: false },
			stroke: { show: true, width: 2, colors: ['transparent'] },
			grid: { show: true, borderColor: 'var(--chart-border)' },
			xaxis: { categories: data.categories, axisTicks: { show: false }, labels: { style: { colors: 'var(--chart-text-color)', fontSize: '12px', fontFamily: 'Rubik, sans-serif', fontWeight: 400 } } },
			yaxis: { min: 0, tickAmount: 6, labels: { style: { colors: 'var(--chart-text-color)', fontSize: '12px', fontFamily: 'Rubik, sans-serif', fontWeight: 400 } } },
			colors: ['var(--theme-default)', '#54BA4A'],
			fill: { opacity: 1 },
			legend: { show: false },
			responsive: [{ breakpoint: 1661, options: { chart: { height: 325 } } }, { breakpoint: 1531, options: { chart: { height: 380 } } }, { breakpoint: 1400, options: { chart: { height: 370 } } }, { breakpoint: 1200, options: { chart: { height: 320 } } }, { breakpoint: 771, options: { chart: { height: 275 } } }, { breakpoint: 590, options: { chart: { height: 215 } } }]
		});
	}

	jQuery(document).on('click', '.attendance-period', function(e) {
		e.preventDefault();
		if (attendanceLoading) return;
		attendanceLoading = true;

		var period = jQuery(this).data('period');
		var wrap = jQuery('#attendance-chart').parent();

		wrap.css('position', 'relative');
		if (!wrap.find('.chart-loader').length) {
			wrap.append('<div class="chart-loader" style="position:absolute;top:0;left:0;right:0;bottom:0;background:rgba(255,255,255,0.7);display:flex;align-items:center;justify-content:center;z-index:5;"><div class="spinner-border spinner-border-sm text-primary"></div></div>');
		}
		wrap.find('.chart-loader').show();

		jQuery.ajax({
			url: '{{ route("admin.dashboard.attendance-performance") }}',
			data: { period: period },
			success: function(data) {
				console.log('Attendance data:', data);

				if (window._attendanceChart) {
					try { window._attendanceChart.destroy(); } catch(ex) {}
					window._attendanceChart = null;
				}

				// Update percentage stats
				if (data.stats) {
					jQuery('.sales-report-chart').siblings('.flex-grow-1').find('.f-w-500').eq(0).text(data.stats.present + '%');
					jQuery('.sales-report-chart').siblings('.flex-grow-1').find('.f-w-500').eq(1).text(data.stats.absent + '%');
					jQuery('.sales-report-chart').siblings('.flex-grow-1').find('.f-w-500').eq(2).text(data.stats.late + '%');
				}

				var el = document.getElementById('attendance-chart');
				el.innerHTML = '';

				setTimeout(function() {
					window._attendanceChart = createAttendanceChart(el, data);
					window._attendanceChart.render();
					wrap.find('.chart-loader').hide();
					attendanceLoading = false;
				}, 100);
			},
			error: function(xhr) {
				console.error('Attendance API error:', xhr.status, xhr.responseText);
				wrap.find('.chart-loader').hide();
				attendanceLoading = false;
			}
		});
	});
});
</script>
@endif
@endpush
