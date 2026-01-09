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
</style>
@endpush

@section('content')
<div class="container-fluid dashboard-7">
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
                                        <a class="dropdown-item" href="#">This Month</a>
                                        <a class="dropdown-item" href="#">Previous Month</a>
                                        <a class="dropdown-item" href="#">Last 3 Months</a>
                                        <a class="dropdown-item" href="#">Last 6 Months</a>
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
                                        <button class="btn dropdown-toggle" id="viewButton" type="button" data-bs-toggle="dropdown" aria-expanded="false">Today <i class="fa fa-angle-down ms-1"></i></button>
                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="viewButton">
                                            <a class="dropdown-item" href="#">This Month</a>
                                            <a class="dropdown-item" href="#">Previous Month</a>
                                            <a class="dropdown-item" href="#">Last 3 Months</a>
                                            <a class="dropdown-item" href="#">Last 6 Months</a>
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
                                        <a class="dropdown-item" href="#">This Month</a>
                                        <a class="dropdown-item" href="#">Previous Month</a>
                                        <a class="dropdown-item" href="#">Last 3 Months</a>
                                        <a class="dropdown-item" href="#">Last 6 Months</a>
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
                                    <a class="btn btn-primary btn-hover-effect f-w-500 knowledge-btn" href="#">Learn More</a>
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
                                    <th></th>
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
                                        @if($fee->is_overdue ?? false)
                                            <span class="badge badge-light-danger" title="Overdue">!</span>
                                        @endif
                                    </td>
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
                                    <td colspan="7" class="text-center py-4">
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
                        <div class="card-header-right-icon">
                            <div class="dropdown icon-dropdown">
                                <button class="btn dropdown-toggle" id="customerButton" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="icon-more-alt"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="customerButton">
                                    <a class="dropdown-item" href="#!">Today</a>
                                    <a class="dropdown-item" href="#!">Tomorrow</a>
                                    <a class="dropdown-item" href="#!">Yesterday</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 top-student-table">
                    <div class="recent-table table-responsive">
                        <table class="table" id="top-students">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Marks</th>
                                    <th>Percentage</th>
                                    <th>Year</th>
                                    <th>Standard</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topPerformers ?? [] as $student)
                                <tr>
                                    <td></td>
                                    <td><a href="#">#{{ $student->admission_no ?? '00000' }}</a></td>
                                    <td>
                                        <div class="common-align justify-content-start">
                                            <img class="rounded-circle me-2" src="{{ $student->photo_url ?? asset('assets/images/dashboard/profile.png') }}" alt="user" style="width: 40px; height: 40px; object-fit: cover;">
                                            <div class="img-content-box">
                                                <a class="f-w-500" href="#">{{ $student->full_name ?? 'Student Name' }}</a>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $student->total_marks ?? 0 }}</td>
                                    <td>{{ $student->percentage ?? 0 }}%</td>
                                    <td>{{ $student->academic_year ?? date('Y') }}</td>
                                    <td>{{ $student->schoolClass->name ?? 'Class' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <p class="text-muted">No data available</p>
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
                                    <a class="dropdown-item" href="#!">Today</a>
                                    <a class="dropdown-item" href="#!">This Week</a>
                                    <a class="dropdown-item" href="#!">This Month</a>
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
</div>
@endsection

@push('scripts')
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
		// Class dropdown filter
		jQuery(document).on('click', '.student-filter-class', function(e) {
			e.preventDefault();
			var classId = jQuery(this).data('class-id') || '';
			var className = jQuery(this).text();

			// Update button text
			jQuery('#classDropdown').text(className);

			// Mark active
			jQuery('.student-filter-class').removeClass('active');
			jQuery(this).addClass('active');

			// Fetch and update chart data
			updateStudentChart(classId);
		});

		// Function to update chart via AJAX
		function updateStudentChart(classId) {
			jQuery.ajax({
				url: '{{ route("admin.dashboard.student-stats") }}',
				type: 'GET',
				data: { class_id: classId },
				success: function(response) {
					if (window.studentChartInstance) {
						window.studentChartInstance.updateSeries(response.series);
						window.studentChartInstance.updateOptions({
							labels: response.labels,
							colors: response.colors,
							plotOptions: {
								pie: {
									donut: {
										labels: {
											total: {
												label: response.total.toString()
											}
										}
									}
								}
							}
						});
					}
				}
			});
		}
	});
</script>
<script src="{{ asset('assets/js/dashboard/dashboard_7.js') }}"></script>
@endpush
