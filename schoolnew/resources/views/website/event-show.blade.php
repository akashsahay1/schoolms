@extends('layouts.website')

@section('title', $event->title)

@section('meta_description', Str::limit(strip_tags($event->description), 160))

@section('content')
<!-- Page Banner -->
<section class="page-banner">
    <div class="container">
        <h1>{{ $event->title }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('website.home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('website.events') }}">Events</a></li>
                <li class="breadcrumb-item active">{{ Str::limit($event->title, 30) }}</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Event Detail -->
<section class="section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <article class="card border-0 shadow-sm mb-4">
                    @if($event->photos->count() > 0)
                        <img src="{{ asset('storage/' . $event->photos->first()->photo_path) }}" class="card-img-top" alt="{{ $event->title }}" style="height: 400px; object-fit: cover;">
                    @endif
                    <div class="card-body p-4">
                        <div class="event-content">
                            {!! $event->description !!}
                        </div>
                    </div>
                </article>

                <!-- Event Photos -->
                @if($event->photos->count() > 1)
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i data-feather="image" class="me-2" style="width: 20px;"></i> Event Photos</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                @foreach($event->photos as $photo)
                                    <div class="col-md-4 col-6">
                                        <a href="{{ asset('storage/' . $photo->photo_path) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="{{ $photo->caption ?? $event->title }}" class="img-fluid rounded" style="height: 150px; width: 100%; object-fit: cover;">
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <!-- Event Info -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i data-feather="info" class="me-2" style="width: 20px;"></i> Event Details</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-3 d-flex align-items-start">
                                <i data-feather="calendar" class="text-primary me-3 mt-1" style="width: 18px;"></i>
                                <div>
                                    <strong class="d-block">Date</strong>
                                    <span class="text-muted">{{ $event->start_date->format('F d, Y') }}</span>
                                    @if($event->end_date && !$event->start_date->isSameDay($event->end_date))
                                        <br><span class="text-muted">to {{ $event->end_date->format('F d, Y') }}</span>
                                    @endif
                                </div>
                            </li>
                            <li class="mb-3 d-flex align-items-start">
                                <i data-feather="clock" class="text-primary me-3 mt-1" style="width: 18px;"></i>
                                <div>
                                    <strong class="d-block">Time</strong>
                                    <span class="text-muted">{{ $event->start_date->format('h:i A') }}</span>
                                    @if($event->end_date)
                                        <span class="text-muted">- {{ $event->end_date->format('h:i A') }}</span>
                                    @endif
                                </div>
                            </li>
                            @if($event->location)
                                <li class="mb-3 d-flex align-items-start">
                                    <i data-feather="map-pin" class="text-primary me-3 mt-1" style="width: 18px;"></i>
                                    <div>
                                        <strong class="d-block">Location</strong>
                                        <span class="text-muted">{{ $event->location }}</span>
                                    </div>
                                </li>
                            @endif
                            @if($event->organizer)
                                <li class="d-flex align-items-start">
                                    <i data-feather="user" class="text-primary me-3 mt-1" style="width: 18px;"></i>
                                    <div>
                                        <strong class="d-block">Organizer</strong>
                                        <span class="text-muted">{{ $event->organizer }}</span>
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                <!-- Back Button -->
                <a href="{{ route('website.events') }}" class="btn btn-outline-secondary w-100">
                    <i data-feather="arrow-left" class="me-1" style="width: 14px;"></i> Back to Events
                </a>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endpush
