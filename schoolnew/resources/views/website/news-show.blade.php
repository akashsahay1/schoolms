@extends('layouts.website')

@section('title', $notice->title)

@section('meta_description', Str::limit(strip_tags($notice->content), 160))

@section('content')
<!-- Page Banner -->
<section class="page-banner">
    <div class="container">
        <h1>{{ $notice->title }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('website.home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('website.news') }}">News</a></li>
                <li class="breadcrumb-item active">{{ Str::limit($notice->title, 30) }}</li>
            </ol>
        </nav>
    </div>
</section>

<!-- News Detail -->
<section class="section-padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <article class="news-detail-card">
                    <div class="news-detail-header">
                        <div class="news-detail-meta">
                            <span class="news-detail-date">
                                <i data-feather="calendar"></i> {{ $notice->publish_date->format('F d, Y') }}
                            </span>
                            @if($notice->category)
                                <span class="news-detail-category">
                                    <i data-feather="folder"></i> {{ $notice->category }}
                                </span>
                            @endif
                        </div>
                        @if($notice->is_important)
                            <span class="news-detail-important">
                                <i data-feather="alert-circle"></i> Important Notice
                            </span>
                        @endif
                    </div>

                    <div class="news-detail-body">
                        <div class="news-detail-content">
                            {!! $notice->content !!}
                        </div>

                        @if($notice->attachment)
                            <div class="news-detail-attachment">
                                <div class="attachment-icon">
                                    <i data-feather="paperclip"></i>
                                </div>
                                <div class="attachment-info">
                                    <h6>Attachment Available</h6>
                                    <p>Download the attached file for more information</p>
                                </div>
                                <a href="{{ asset('storage/' . $notice->attachment) }}" target="_blank" class="btn btn-primary">
                                    <i data-feather="download" style="width: 16px;"></i> Download
                                </a>
                            </div>
                        @endif
                    </div>

                    <div class="news-detail-footer">
                        <a href="{{ route('website.news') }}" class="btn btn-outline-primary btn-lg">
                            <i data-feather="arrow-left" style="width: 16px;"></i> Back to News
                        </a>
                    </div>
                </article>
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
