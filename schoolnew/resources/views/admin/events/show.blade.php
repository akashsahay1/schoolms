@extends('layouts.app')

@section('title', 'View Event')
@section('page-title', 'Event Details')

@section('breadcrumb')
    <li class="breadcrumb-item">Communication</li>
    <li class="breadcrumb-item"><a href="{{ route('admin.events.index') }}">Events</a></li>
    <li class="breadcrumb-item active">View</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                @if($event->image)
                    <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}" class="card-img-top" style="max-height: 300px; object-fit: cover;">
                @endif
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>Event Details</h5>
                        <div>
                            <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <a href="{{ route('admin.events.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span class="badge" style="background-color: {{ $event->color }}; color: white;">{{ $event->getTypeLabel() }}</span>
                        @if($event->is_holiday)
                            <span class="badge badge-light-danger">Holiday</span>
                        @endif
                        @if($event->is_public)
                            <span class="badge badge-light-info">Public</span>
                        @endif
                    </div>

                    <h3 class="mb-3">{{ $event->title }}</h3>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="mb-2">
                                <i class="fa fa-calendar text-primary me-2"></i>
                                <strong>Date:</strong>
                                {{ $event->start_date->format('F d, Y') }}
                                @if($event->isMultiDay())
                                    - {{ $event->end_date->format('F d, Y') }}
                                @endif
                            </p>
                            @if($event->start_time)
                                <p class="mb-2">
                                    <i class="fa fa-clock-o text-primary me-2"></i>
                                    <strong>Time:</strong>
                                    {{ \Carbon\Carbon::parse($event->start_time)->format('h:i A') }}
                                    @if($event->end_time)
                                        - {{ \Carbon\Carbon::parse($event->end_time)->format('h:i A') }}
                                    @endif
                                </p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            @if($event->venue)
                                <p class="mb-2">
                                    <i class="fa fa-map-marker text-primary me-2"></i>
                                    <strong>Venue:</strong> {{ $event->venue }}
                                </p>
                            @endif
                            <p class="mb-2">
                                <i class="fa fa-user text-primary me-2"></i>
                                <strong>Created by:</strong> {{ $event->creator->name ?? 'N/A' }}
                            </p>
                        </div>
                    </div>

                    @if($event->description)
                        <hr>
                        <div class="mb-4">
                            <h6>Description</h6>
                            <div class="bg-light text-dark p-3 rounded">
                                {!! nl2br(e($event->description)) !!}
                            </div>
                        </div>
                    @endif

                    @if($event->photos->count() > 0)
                        <hr>
                        <div class="mb-4">
                            <h6>Photo Gallery</h6>
                            <div class="row g-3">
                                @foreach($event->photos as $photo)
                                    <div class="col-md-3">
                                        <a href="{{ $photo->image_url }}" target="_blank">
                                            <img src="{{ $photo->image_url }}" alt="" class="img-fluid rounded" style="height: 120px; width: 100%; object-fit: cover;">
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="bg-light rounded p-4 mb-3">
                        <h1 class="display-4 mb-0" style="color: {{ $event->color }};">{{ $event->start_date->format('d') }}</h1>
                        <h5 class="text-muted">{{ $event->start_date->format('F Y') }}</h5>
                    </div>
                    <p class="text-muted mb-0">{{ $event->start_date->diffForHumans() }}</p>
                    @if($event->isMultiDay())
                        <p class="mt-2"><span class="badge badge-light-primary">{{ $event->getDurationDays() }} days event</span></p>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header pb-0">
                    <h6>Target Audience</h6>
                </div>
                <div class="card-body">
                    @foreach($event->target_audience ?? ['all'] as $audience)
                        <span class="badge badge-light-primary me-1">{{ ucfirst($audience) }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
