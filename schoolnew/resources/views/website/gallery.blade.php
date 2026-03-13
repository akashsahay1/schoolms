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
        <div class="section-title">
            <h2>Our Photo Gallery</h2>
            <div class="divider"></div>
            <p>Explore memorable moments and glimpses of school life</p>
        </div>

        @if($page?->content)
            <div class="row mb-4">
                <div class="col-lg-8 mx-auto text-center">
                    {!! $page->content !!}
                </div>
            </div>
        @endif

        <!-- Category Filter -->
        @if(count($categories) > 0)
            <div class="gallery-filters">
                <a href="{{ route('website.gallery') }}" class="filter-btn {{ !$category ? 'active' : '' }}">
                    <i data-feather="grid"></i> All Photos
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('website.gallery', ['category' => $cat]) }}" class="filter-btn {{ $category === $cat ? 'active' : '' }}">
                        {{ $cat }}
                    </a>
                @endforeach
            </div>
        @endif

        <!-- Gallery Grid -->
        <div class="row g-4">
            @forelse($gallery as $item)
                <div class="col-lg-3 col-md-4 col-6">
                    <div class="gallery-card">
                        <div class="gallery-image">
                            <a href="{{ asset('storage/' . $item->image) }}" data-bs-toggle="modal" data-bs-target="#galleryModal" data-image="{{ asset('storage/' . $item->image) }}" data-title="{{ $item->title }}" data-description="{{ $item->description }}">
                                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}">
                                <div class="gallery-overlay">
                                    <div class="gallery-icon">
                                        <i data-feather="zoom-in"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @if($item->title)
                            <div class="gallery-caption">
                                <h6>{{ $item->title }}</h6>
                                @if($item->category)
                                    <span class="gallery-category">{{ $item->category }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i data-feather="image"></i>
                        </div>
                        <h4>No Photos Available</h4>
                        <p>Our gallery is being updated.<br>Check back later for amazing photos!</p>
                        <a href="{{ route('website.home') }}" class="btn btn-primary">
                            <i data-feather="home" style="width: 16px;"></i> Back to Home
                        </a>
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
        <div class="modal-content gallery-modal-content">
            <button type="button" class="gallery-modal-close" data-bs-dismiss="modal">
                <i data-feather="x"></i>
            </button>
            <div class="gallery-modal-image">
                <img src="" id="modalImage" class="img-fluid" alt="">
            </div>
            <div class="gallery-modal-info">
                <h5 id="modalTitle"></h5>
                <p id="modalDescription"></p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Gallery Filters */
    .gallery-filters {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 12px;
        margin-bottom: 50px;
    }

    .filter-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        background: #fff;
        border: 2px solid rgba(96, 101, 242, 0.15);
        border-radius: 50px;
        color: var(--dark-color);
        font-weight: 500;
        font-size: 0.95rem;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .filter-btn i {
        width: 18px;
        height: 18px;
    }

    .filter-btn:hover {
        border-color: var(--primary-color);
        color: var(--primary-color);
        transform: translateY(-2px);
    }

    .filter-btn.active {
        background: var(--primary-color);
        border-color: transparent;
        color: #fff;
        box-shadow: 0 8px 25px rgba(96, 101, 242, 0.3);
    }

    /* Gallery Card */
    .gallery-card {
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 30px rgba(0,0,0,0.06);
        transition: all 0.4s ease;
    }

    .gallery-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 40px rgba(96, 101, 242, 0.15);
    }

    .gallery-image {
        position: relative;
        overflow: hidden;
        cursor: pointer;
    }

    .gallery-image img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .gallery-card:hover .gallery-image img {
        transform: scale(1.1);
    }

    .gallery-overlay {
        position: absolute;
        inset: 0;
        background: rgba(96, 101, 242, 0.85);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.4s ease;
    }

    .gallery-card:hover .gallery-overlay {
        opacity: 1;
    }

    .gallery-icon {
        width: 60px;
        height: 60px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transform: scale(0.5);
        transition: transform 0.4s ease;
    }

    .gallery-card:hover .gallery-icon {
        transform: scale(1);
    }

    .gallery-icon i {
        color: #fff;
        width: 28px;
        height: 28px;
    }

    .gallery-caption {
        padding: 18px;
        text-align: center;
    }

    .gallery-caption h6 {
        color: var(--dark-color);
        font-weight: 600;
        margin-bottom: 5px;
        font-size: 0.95rem;
    }

    .gallery-category {
        font-size: 0.8rem;
        color: var(--primary-color);
        font-weight: 500;
    }

    /* Gallery Modal */
    .gallery-modal-content {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        background: #fff;
    }

    .gallery-modal-close {
        position: absolute;
        top: 15px;
        right: 15px;
        width: 40px;
        height: 40px;
        background: rgba(0,0,0,0.5);
        border: none;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .gallery-modal-close i {
        color: #fff;
        width: 20px;
        height: 20px;
    }

    .gallery-modal-close:hover {
        background: var(--primary-color);
        transform: rotate(90deg);
    }

    .gallery-modal-image {
        background: #000;
    }

    .gallery-modal-image img {
        width: 100%;
        max-height: 70vh;
        object-fit: contain;
    }

    .gallery-modal-info {
        padding: 25px;
        text-align: center;
    }

    .gallery-modal-info h5 {
        color: var(--dark-color);
        font-weight: 600;
        margin-bottom: 10px;
    }

    .gallery-modal-info p {
        color: var(--text-color);
        margin-bottom: 0;
    }

    @media (max-width: 767px) {
        .gallery-image img {
            height: 160px;
        }

        .gallery-caption {
            padding: 12px;
        }

        .filter-btn {
            padding: 10px 18px;
            font-size: 0.85rem;
        }
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
