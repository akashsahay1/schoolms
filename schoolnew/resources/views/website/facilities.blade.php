@extends('layouts.website')

@section('title', 'Our Facilities')

@section('meta_description', $page?->meta_description ?? 'Explore our world-class facilities designed to provide the best learning environment.')

@section('content')
<!-- Page Banner -->
<section class="page-banner">
    <div class="container">
        <h1>Our Facilities</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('website.home') }}">Home</a></li>
                <li class="breadcrumb-item active">Facilities</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Facilities Content -->
<section class="section-padding">
    <div class="container">
        @if($page?->content)
            <div class="row mb-5">
                <div class="col-lg-8 mx-auto text-center">
                    {!! $page->content !!}
                </div>
            </div>
        @else
            <div class="section-title">
                <h2>World-Class Infrastructure</h2>
                <div class="divider"></div>
                <p>We provide state-of-the-art facilities to ensure the best learning experience for our students</p>
            </div>
        @endif

        <div class="row g-4">
            @forelse($facilities as $facility)
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card h-100">
                        @if($facility->image)
                            <img src="{{ asset('storage/' . $facility->image) }}" alt="{{ $facility->title }}" class="img-fluid rounded mb-3" style="height: 200px; width: 100%; object-fit: cover;">
                        @else
                            <div class="icon">
                                @if($facility->icon)
                                    <i data-feather="{{ $facility->icon }}"></i>
                                @else
                                    <i data-feather="star"></i>
                                @endif
                            </div>
                        @endif
                        <h4>{{ $facility->title }}</h4>
                        <p>{{ $facility->description }}</p>
                    </div>
                </div>
            @empty
                <!-- Default Facilities -->
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card h-100">
                        <div class="icon"><i data-feather="monitor"></i></div>
                        <h4>Smart Classrooms</h4>
                        <p>Modern classrooms equipped with interactive whiteboards, projectors, and audio-visual aids for enhanced learning.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card h-100">
                        <div class="icon"><i data-feather="book"></i></div>
                        <h4>Library</h4>
                        <p>Well-stocked library with thousands of books, journals, and digital resources to encourage reading habits.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card h-100">
                        <div class="icon"><i data-feather="cpu"></i></div>
                        <h4>Computer Lab</h4>
                        <p>Modern computer labs with latest hardware and software for practical computer education.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card h-100">
                        <div class="icon"><i data-feather="activity"></i></div>
                        <h4>Science Labs</h4>
                        <p>Well-equipped physics, chemistry, and biology labs for hands-on scientific experiments.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card h-100">
                        <div class="icon"><i data-feather="dribbble"></i></div>
                        <h4>Sports Facilities</h4>
                        <p>Extensive sports grounds, indoor games area, and professional coaching for various sports.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card h-100">
                        <div class="icon"><i data-feather="music"></i></div>
                        <h4>Music & Art Room</h4>
                        <p>Dedicated spaces for music practice and art activities to nurture creative talents.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card h-100">
                        <div class="icon"><i data-feather="truck"></i></div>
                        <h4>Transport</h4>
                        <p>Safe and comfortable school buses covering all major areas with GPS tracking.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card h-100">
                        <div class="icon"><i data-feather="coffee"></i></div>
                        <h4>Cafeteria</h4>
                        <p>Hygienic cafeteria serving nutritious meals and snacks prepared in clean kitchen.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card h-100">
                        <div class="icon"><i data-feather="plus-circle"></i></div>
                        <h4>Medical Room</h4>
                        <p>First-aid facility with trained staff and regular health check-ups for students.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="content">
            <h2>Want to See Our Facilities?</h2>
            <p class="mb-4">Schedule a campus tour and experience our world-class infrastructure</p>
            <a href="{{ route('website.contact') }}" class="btn btn-primary btn-lg">Contact Us</a>
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
