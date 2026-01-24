@extends('layouts.website')

@section('title', 'Events')

@section('meta_description', $page?->meta_description ?? 'Browse our upcoming and past school events.')

@section('content')
<!-- Page Banner -->
<section class="page-banner">
    <div class="container">
        <h1>School Events</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('website.home') }}">Home</a></li>
                <li class="breadcrumb-item active">Events</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Events Content -->
<section class="section-padding">
    <div class="container">
        <div class="row g-4">
            @forelse($events as $event)
                <div class="col-lg-4 col-md-6">
                    <div class="event-card position-relative h-100">
                        <div class="event-date">
                            <span class="day">{{ $event->start_date->format('d') }}</span>
                            <span class="month">{{ $event->start_date->format('M') }}</span>
                        </div>
                        @if($event->photos->count() > 0)
                            <img src="{{ asset('storage/' . $event->photos->first()->photo_path) }}" alt="{{ $event->title }}">
                        @else
                            <img src="{{ asset('assets/images/event-default.jpg') }}" alt="{{ $event->title }}">
                        @endif
                        <div class="event-content">
                            <h5>
                                <a href="{{ route('website.events.show', $event) }}" class="text-dark text-decoration-none">
                                    {{ $event->title }}
                                </a>
                            </h5>
                            <p class="text-muted small mb-2">
                                <i data-feather="clock" style="width: 14px;"></i> {{ $event->start_date->format('h:i A') }}
                                @if($event->end_date)
                                    - {{ $event->end_date->format('h:i A') }}
                                @endif
                            </p>
                            @if($event->location)
                                <p class="text-muted small mb-2">
                                    <i data-feather="map-pin" style="width: 14px;"></i> {{ $event->location }}
                                </p>
                            @endif
                            <p class="small mb-3">{{ Str::limit(strip_tags($event->description), 100) }}</p>
                            <a href="{{ route('website.events.show', $event) }}" class="btn btn-sm btn-outline-primary">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i data-feather="calendar" class="text-muted mb-3" style="width: 64px; height: 64px;"></i>
                        <h5 class="text-muted">No events available yet</h5>
                        <p class="text-muted">Check back later for updates!</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($events->hasPages())
            <div class="d-flex justify-content-center mt-5">
                {{ $events->links() }}
            </div>
        @endif
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
