@extends('layouts.portal')

@section('title', 'Parent Dashboard')
@section('page-title', 'Parent Dashboard')

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
                    <div class="text-white">
                        <h4 class="text-white mb-1">Welcome, {{ $parent->father_name ?? $parent->mother_name ?? $parent->guardian_name ?? 'Parent' }}!</h4>
                        <p class="mb-0 opacity-75">Academic Year: {{ $currentAcademicYear->name ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Children Overview -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>My Children</h5>
                </div>
                <div class="card-body">
                    @if($children->count() > 0)
                        <div class="row">
                            @foreach($children as $child)
                                <div class="col-xl-6 mb-4">
                                    <div class="card border h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start">
                                                <img src="{{ $child->photo_url }}" alt="{{ $child->full_name }}" class="rounded-circle me-3" width="60" height="60" style="object-fit: cover;">
                                                <div class="flex-grow-1">
                                                    <h5 class="mb-1">{{ $child->full_name }}</h5>
                                                    <p class="text-muted mb-2">{{ $child->schoolClass->name ?? '' }} - {{ $child->section->name ?? '' }}</p>
                                                    <div class="mb-2">
                                                        <span class="badge badge-light-primary">Adm. No: {{ $child->admission_no }}</span>
                                                        @if($child->roll_no)
                                                            <span class="badge badge-light-secondary">Roll: {{ $child->roll_no }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <hr>

                                            @if(isset($childrenStats[$child->id]))
                                                <div class="row text-center">
                                                    <div class="col-4">
                                                        <div class="bg-light-success rounded p-2">
                                                            <h6 class="text-success mb-0">{{ $childrenStats[$child->id]['attendance']['percentage'] }}%</h6>
                                                            <small class="text-muted">Attendance</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="bg-light-primary rounded p-2">
                                                            <h6 class="text-primary mb-0">Rs. {{ number_format($childrenStats[$child->id]['fees']['total_paid'], 0) }}</h6>
                                                            <small class="text-muted">Paid</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="bg-light-{{ $childrenStats[$child->id]['fees']['total_due'] > 0 ? 'danger' : 'success' }} rounded p-2">
                                                            <h6 class="text-{{ $childrenStats[$child->id]['fees']['total_due'] > 0 ? 'danger' : 'success' }} mb-0">Rs. {{ number_format($childrenStats[$child->id]['fees']['total_due'], 0) }}</h6>
                                                            <small class="text-muted">Due</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fa fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No children linked to your account</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
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
                                            <span class="badge {{ $notice->getTypeBadgeClass() }}">{{ $notice->getTypeLabel() }}</span>
                                            <h6 class="mb-1 mt-2">{{ $notice->title }}</h6>
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

        <!-- Upcoming Events -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>Upcoming Events</h5>
                        <a href="{{ route('portal.events') }}" class="btn btn-sm btn-outline-primary">View Calendar</a>
                    </div>
                </div>
                <div class="card-body">
                    @if($upcomingEvents->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($upcomingEvents as $event)
                                <li class="list-group-item px-0">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3 text-center" style="min-width: 50px;">
                                            <div class="bg-light rounded p-2">
                                                <div class="fw-bold text-primary">{{ $event->start_date->format('d') }}</div>
                                                <div class="small text-muted">{{ $event->start_date->format('M') }}</div>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ $event->title }}</h6>
                                            <small class="text-muted">
                                                <span class="badge" style="background-color: {{ $event->color }}; color: white;">{{ $event->getTypeLabel() }}</span>
                                            </small>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
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
