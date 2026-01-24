@extends('layouts.website')

@section('title', 'Photo Gallery')

@section('meta_description', $page?->meta_description ?? 'Browse our photo gallery to see glimpses of school life and activities.')

@section('content')
<!-- Page Banner -->
<section class="page-banner">
    <div class="container">
        <h1>Photo Gallery</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('website.home') }}">Home</a></li>
                <li class="breadcrumb-item active">Gallery</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Gallery Content -->
<section class="section-padding">
    <div class="container">
        @if($page?->content)
            <div class="row mb-4">
                <div class="col-lg-8 mx-auto text-center">
                    {!! $page->content !!}
                </div>
            </div>
        @endif

        <!-- Category Filter -->
        @if(count($categories) > 0)
            <div class="text-center mb-5">
                <a href="{{ route('website.gallery') }}" class="btn {{ !$category ? 'btn-primary' : 'btn-outline-primary' }} m-1">All</a>
                @foreach($categories as $cat)
                    <a href="{{ route('website.gallery', ['category' => $cat]) }}" class="btn {{ $category === $cat ? 'btn-primary' : 'btn-outline-primary' }} m-1">{{ $cat }}</a>
                @endforeach
            </div>
        @endif

        <!-- Gallery Grid -->
        <div class="row g-4">
            @forelse($gallery as $item)
                <div class="col-lg-3 col-md-4 col-6">
                    <div class="gallery-item">
                        <a href="{{ asset('storage/' . $item->image) }}" data-bs-toggle="modal" data-bs-target="#galleryModal" data-image="{{ asset('storage/' . $item->image) }}" data-title="{{ $item->title }}" data-description="{{ $item->description }}">
                            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}">
                            <div class="overlay">
                                <i data-feather="zoom-in"></i>
                            </div>
                        </a>
                    </div>
                    <p class="text-center mt-2 mb-0 small fw-medium">{{ $item->title }}</p>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i data-feather="image" class="text-muted mb-3" style="width: 64px; height: 64px;"></i>
                        <h5 class="text-muted">No photos available yet</h5>
                        <p class="text-muted">Check back later for updates!</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($gallery->hasPages())
            <div class="d-flex justify-content-center mt-5">
                {{ $gallery->withQueryString()->links() }}
            </div>
        @endif
    </div>
</section>

<!-- Image Modal -->
<div class="modal fade" id="galleryModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="modalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <img src="" id="modalImage" class="img-fluid w-100" alt="">
            </div>
            <div class="modal-footer border-0">
                <p class="text-muted mb-0" id="modalDescription"></p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .gallery-item {
        cursor: pointer;
    }
</style>
@endpush

@push('scripts')
<script>
jQuery(document).ready(function() {
    if (typeof feather !== 'undefined') {
        feather.replace();
    }

    // Gallery Modal
    jQuery('.gallery-item a').on('click', function(e) {
        e.preventDefault();
        var image = jQuery(this).data('image');
        var title = jQuery(this).data('title');
        var description = jQuery(this).data('description');

        jQuery('#modalImage').attr('src', image);
        jQuery('#modalTitle').text(title);
        jQuery('#modalDescription').text(description || '');
    });
});
</script>
@endpush
