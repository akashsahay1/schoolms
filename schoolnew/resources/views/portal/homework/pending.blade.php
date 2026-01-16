@extends('layouts.portal')

@section('title', 'Pending Homework')
@section('page-title', 'Pending Homework')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('portal.homework') }}">Homework</a></li>
    <li class="breadcrumb-item active">Pending</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        @if($homeworks->count() > 0)
            @foreach($homeworks as $homework)
                @php
                    $isOverdue = $homework->submission_date < now();
                    $daysLeft = now()->diffInDays($homework->submission_date, false);
                @endphp
                <div class="col-md-6 col-lg-4">
                    <div class="card border {{ $isOverdue ? 'border-danger' : ($homework->submission_date->isToday() ? 'border-warning' : '') }}">
                        <div class="card-header pb-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <h6 class="mb-0">{{ $homework->title }}</h6>
                                @if($isOverdue)
                                    <span class="badge bg-danger">Overdue</span>
                                @elseif($homework->submission_date->isToday())
                                    <span class="badge bg-warning">Due Today</span>
                                @elseif($homework->submission_date->isTomorrow())
                                    <span class="badge bg-info">Due Tomorrow</span>
                                @else
                                    <span class="badge bg-secondary">{{ abs($daysLeft) }} days left</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-2">
                                <i class="fa fa-book me-2"></i>{{ $homework->subject->name ?? 'N/A' }}
                            </p>
                            <p class="text-muted mb-2">
                                <i class="fa fa-user me-2"></i>{{ $homework->teacher->first_name ?? '' }} {{ $homework->teacher->last_name ?? '' }}
                            </p>
                            <p class="text-muted mb-2">
                                <i class="fa fa-calendar me-2"></i>Due: {{ $homework->submission_date->format('M d, Y') }}
                            </p>
                            @if($homework->max_marks)
                                <p class="text-muted mb-2">
                                    <i class="fa fa-star me-2"></i>Max Marks: {{ $homework->max_marks }}
                                </p>
                            @endif
                            @if($homework->description)
                                <p class="text-muted small mb-3">{{ Str::limit($homework->description, 100) }}</p>
                            @endif
                            <a href="{{ route('portal.homework.show', $homework) }}" class="btn btn-primary btn-sm w-100">
                                <i class="fa fa-eye me-1"></i> View & Submit
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="col-12">
                {{ $homeworks->links() }}
            </div>
        @else
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <svg class="stroke-icon" style="width: 60px; height: 60px; opacity: 0.5;">
                            <use href="{{ asset('assets/svg/icon-sprite.svg#check-circle') }}"></use>
                        </svg>
                        <h6 class="mt-3 text-muted">All Caught Up!</h6>
                        <p class="text-muted">You have no pending homework. Great job!</p>
                        <a href="{{ route('portal.homework') }}" class="btn btn-outline-primary">
                            View All Homework
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
