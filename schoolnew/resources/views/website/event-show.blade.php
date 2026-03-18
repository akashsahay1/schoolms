@extends('layouts.website')

@section('title', $event->title)

@section('meta_description', Str::limit(strip_tags($event->description), 160))

@section('content')
<!-- Page Banner -->
@php $__bannerPage = \App\Models\WebsitePage::findBySlug('events'); @endphp
<section class="page-banner" @if($__bannerPage) style="{{ $__bannerPage->banner_image ? 'background-image: url(' . asset('storage/' . $__bannerPage->banner_image) . ');' : '' }}{{ $__bannerPage->banner_color ? '--banner-color: ' . $__bannerPage->banner_color . ';' : '' }}" @endif>
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
            <div class="col-lg-8 mb-4 mb-lg-0">
                <article class="event-detail-card">
                    @if($event->photos->count() > 0)
                        <div class="event-detail-image">
                            <img src="{{ asset('storage/' . $event->photos->first()->photo_path) }}" alt="{{ $event->title }}">
                        </div>
                    @else
                        <div class="event-detail-placeholder">
                            <i data-feather="calendar"></i>
                            <span>{{ $event->start_date->format('M d, Y') }}</span>
                        </div>
                    @endif
                    <div class="event-detail-body">
                        <div class="event-description">
                            {!! $event->description !!}
                        </div>
                    </div>
                </article>

                <!-- Event Photos -->
                @if($event->photos->count() > 1)
                    <div class="event-photos-card">
                        <div class="event-photos-header">
                            <i data-feather="image"></i>
                            <h5>Event Photos</h5>
                        </div>
                        <div class="event-photos-grid">
                            @foreach($event->photos as $photo)
                                <a href="{{ asset('storage/' . $photo->photo_path) }}" target="_blank" class="event-photo-item">
                                    <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="{{ $photo->caption ?? $event->title }}">
                                    <div class="event-photo-overlay">
                                        <i data-feather="zoom-in"></i>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <!-- Event Info -->
                <div class="event-info-card">
                    <div class="event-info-header">
                        <i data-feather="info"></i>
                        <h5>Event Details</h5>
                    </div>
                    <div class="event-info-body">
                        <div class="event-info-item">
                            <div class="event-info-icon">
                                <i data-feather="calendar"></i>
                            </div>
                            <div class="event-info-content">
                                <strong>Date</strong>
                                <span>{{ $event->start_date->format('F d, Y') }}</span>
                                @if($event->end_date && !$event->start_date->isSameDay($event->end_date))
                                    <span>to {{ $event->end_date->format('F d, Y') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="event-info-item">
                            <div class="event-info-icon">
                                <i data-feather="clock"></i>
                            </div>
                            <div class="event-info-content">
                                <strong>Time</strong>
                                <span>{{ $event->start_date->format('h:i A') }}@if($event->end_date) - {{ $event->end_date->format('h:i A') }}@endif</span>
                            </div>
                        </div>
                        @if($event->location)
                            <div class="event-info-item">
                                <div class="event-info-icon">
                                    <i data-feather="map-pin"></i>
                                </div>
                                <div class="event-info-content">
                                    <strong>Location</strong>
                                    <span>{{ $event->location }}</span>
                                </div>
                            </div>
                        @endif
                        @if($event->organizer)
                            <div class="event-info-item">
                                <div class="event-info-icon">
                                    <i data-feather="user"></i>
                                </div>
                                <div class="event-info-content">
                                    <strong>Organizer</strong>
                                    <span>{{ $event->organizer }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Back Button -->
                <a href="{{ route('website.events') }}" class="btn btn-outline-primary btn-lg w-100">
                    <i data-feather="arrow-left" style="width: 16px;"></i> Back to Events
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
