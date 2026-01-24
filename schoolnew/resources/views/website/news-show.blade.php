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
                <article class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-lg-5">
                        <div class="mb-4">
                            <div class="d-flex align-items-center gap-3 text-muted small mb-3">
                                <span><i data-feather="calendar" class="me-1" style="width: 14px;"></i> {{ $notice->publish_date->format('F d, Y') }}</span>
                                @if($notice->category)
                                    <span><i data-feather="folder" class="me-1" style="width: 14px;"></i> {{ $notice->category }}</span>
                                @endif
                            </div>
                            @if($notice->is_important)
                                <span class="badge bg-danger mb-3">Important</span>
                            @endif
                        </div>

                        <div class="notice-content">
                            {!! $notice->content !!}
                        </div>

                        @if($notice->attachment)
                            <div class="mt-4 p-3 bg-light rounded">
                                <h6><i data-feather="paperclip" class="me-2" style="width: 16px;"></i> Attachment</h6>
                                <a href="{{ asset('storage/' . $notice->attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i data-feather="download" class="me-1" style="width: 14px;"></i> Download Attachment
                                </a>
                            </div>
                        @endif

                        <hr class="my-4">

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('website.news') }}" class="btn btn-outline-secondary">
                                <i data-feather="arrow-left" class="me-1" style="width: 14px;"></i> Back to News
                            </a>
                        </div>
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
