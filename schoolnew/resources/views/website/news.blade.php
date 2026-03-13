@extends('layouts.website')

@section('title', 'News & Announcements')

@section('meta_description', $page?->meta_description ?? 'Stay updated with the latest news and announcements from our school.')

@section('content')
<!-- Page Banner -->
<section class="page-banner">
    <div class="container">
        <h1>News & Announcements</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('website.home') }}">Home</a></li>
                <li class="breadcrumb-item active">News</li>
            </ol>
        </nav>
    </div>
</section>

<!-- News Content -->
<section class="section-padding">
    <div class="container">
        @if($notices->count() > 0)
            <div class="section-title">
                <h2>Latest News & Updates</h2>
                <div class="divider"></div>
                <p>Stay informed with the latest happenings at our school</p>
            </div>
        @endif

        <div class="row g-4">
            @forelse($notices as $notice)
                <div class="col-lg-6">
                    <div class="news-list-card">
                        <div class="news-list-date">
                            <span class="day">{{ $notice->publish_date->format('d') }}</span>
                            <span class="month">{{ $notice->publish_date->format('M') }}</span>
                            <span class="year">{{ $notice->publish_date->format('Y') }}</span>
                        </div>
                        <div class="news-list-content">
                            <div class="news-list-badges">
                                @if($notice->category)
                                    <span class="badge-category">{{ $notice->category }}</span>
                                @endif
                                @if($notice->is_important)
                                    <span class="badge-important">Important</span>
                                @endif
                            </div>
                            <h5>
                                <a href="{{ route('website.news.show', $notice) }}">{{ $notice->title }}</a>
                            </h5>
                            <p>{{ Str::limit(strip_tags($notice->content), 120) }}</p>
                            <a href="{{ route('website.news.show', $notice) }}" class="news-list-link">
                                Read More <i data-feather="arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i data-feather="file-text"></i>
                        </div>
                        <h4>No News Available</h4>
                        <p>There are no news or announcements at the moment.<br>Check back later for updates!</p>
                        <a href="{{ route('website.home') }}" class="btn btn-primary">
                            <i data-feather="home" style="width: 16px;"></i> Back to Home
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($notices->hasPages())
            <div class="d-flex justify-content-center mt-5">
                {{ $notices->links() }}
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
