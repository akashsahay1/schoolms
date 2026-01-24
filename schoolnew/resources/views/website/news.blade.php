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
        <div class="row">
            @forelse($notices as $notice)
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-start gap-3">
                                <div class="news-date text-center">
                                    <span class="day d-block fw-bold text-primary" style="font-size: 1.5rem;">{{ $notice->publish_date->format('d') }}</span>
                                    <span class="small text-muted">{{ $notice->publish_date->format('M Y') }}</span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="mb-2">
                                        @if($notice->category)
                                            <span class="badge bg-light text-primary">{{ $notice->category }}</span>
                                        @endif
                                        @if($notice->is_important)
                                            <span class="badge bg-danger">Important</span>
                                        @endif
                                    </div>
                                    <h5 class="card-title">
                                        <a href="{{ route('website.news.show', $notice) }}" class="text-dark text-decoration-none">
                                            {{ $notice->title }}
                                        </a>
                                    </h5>
                                    <p class="card-text text-muted small">
                                        {{ Str::limit(strip_tags($notice->content), 150) }}
                                    </p>
                                    <a href="{{ route('website.news.show', $notice) }}" class="btn btn-sm btn-outline-primary">
                                        Read More <i data-feather="arrow-right" class="ms-1" style="width: 14px;"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i data-feather="file-text" class="text-muted mb-3" style="width: 64px; height: 64px;"></i>
                        <h5 class="text-muted">No news available yet</h5>
                        <p class="text-muted">Check back later for updates!</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($notices->hasPages())
            <div class="d-flex justify-content-center mt-4">
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
