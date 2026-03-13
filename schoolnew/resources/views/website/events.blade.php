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
        @if($events->count() > 0)
            <div class="section-title">
                <h2>Upcoming & Recent Events</h2>
                <div class="divider"></div>
                <p>Stay updated with our school activities and celebrations</p>
            </div>
        @endif

        <div class="row g-4">
            @forelse($events as $event)
                <div class="col-lg-4 col-md-6">
                    <div class="event-card position-relative h-100">
                        <div class="event-image-wrapper">
                            <div class="event-date">
                                <span class="day">{{ $event->start_date->format('d') }}</span>
                                <span class="month">{{ $event->start_date->format('M') }}</span>
                            </div>
                            @if($event->photos->count() > 0)
                                <img src="{{ asset('storage/' . $event->photos->first()->photo_path) }}" alt="{{ $event->title }}">
                            @else
                                <div class="event-placeholder">
                                    <i data-feather="calendar"></i>
                                </div>
                            @endif
                        </div>
                        <div class="event-content">
                            <h5>
                                <a href="{{ route('website.events.show', $event) }}">
                                    {{ $event->title }}
                                </a>
                            </h5>
                            <div class="event-meta">
                                <span>
                                    <i data-feather="clock"></i> {{ $event->start_date->format('h:i A') }}
                                </span>
                                @if($event->location)
                                    <span>
                                        <i data-feather="map-pin"></i> {{ Str::limit($event->location, 20) }}
                                    </span>
                                @endif
                            </div>
                            <p class="event-desc">{{ Str::limit(strip_tags($event->description), 100) }}</p>
                            <a href="{{ route('website.events.show', $event) }}" class="btn btn-sm btn-primary">
                                View Details <i data-feather="arrow-right" style="width: 14px;"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i data-feather="calendar"></i>
                        </div>
                        <h4>No Events Available</h4>
                        <p>There are no events scheduled at the moment.<br>Check back later for updates!</p>
                        <a href="{{ route('website.home') }}" class="btn btn-primary">
                            <i data-feather="home" style="width: 16px;"></i> Back to Home
                        </a>
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
