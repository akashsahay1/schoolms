@extends('layouts.portal')

@section('title', $event->title)
@section('page-title', 'Event Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('portal.events') }}">Events</a></li>
    <li class="breadcrumb-item active">View</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                @if($event->image)
                    <img src="{{ asset('storage/' . $event->image) }}" class="card-img-top" alt="{{ $event->title }}" style="max-height: 300px; object-fit: cover;">
                @endif
                <div class="card-body">
                    <div class="mb-3">
                        <span class="badge" style="background-color: {{ $event->color }}; color: white;">{{ $event->getTypeLabel() }}</span>
                        @if($event->is_holiday)
                            <span class="badge badge-light-danger ms-2">Holiday</span>
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
                            @if($event->isMultiDay())
                                <p class="mb-2">
                                    <i class="fa fa-calendar-check-o text-primary me-2"></i>
                                    <strong>Duration:</strong> {{ $event->getDurationDays() }} days
                                </p>
                            @endif
                        </div>
                    </div>

                    @if($event->description)
                        <hr>
                        <div class="py-3">
                            <h6>Description</h6>
                            {!! nl2br(e($event->description)) !!}
                        </div>
                    @endif
                </div>
            </div>

            @if($event->photos->count() > 0)
                <div class="card">
                    <div class="card-header pb-0">
                        <h5>Photo Gallery</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach($event->photos as $photo)
                                <div class="col-md-4">
                                    <a href="{{ $photo->image_url }}" target="_blank">
                                        <img src="{{ $photo->image_url }}" alt="{{ $photo->caption ?? 'Event Photo' }}" class="img-fluid rounded" style="width: 100%; height: 150px; object-fit: cover;">
                                    </a>
                                    @if($photo->caption)
                                        <small class="text-muted d-block mt-1">{{ $photo->caption }}</small>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="bg-light rounded p-4 mb-3">
                        <h1 class="display-4 text-primary mb-0">{{ $event->start_date->format('d') }}</h1>
                        <h5 class="text-muted">{{ $event->start_date->format('F Y') }}</h5>
                    </div>
                    <p class="text-muted">{{ $event->start_date->diffForHumans() }}</p>
                </div>
            </div>

            <div class="mt-3">
                <a href="{{ route('portal.events') }}" class="btn btn-outline-secondary w-100">
                    <i class="fa fa-arrow-left me-1"></i> Back to Calendar
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
