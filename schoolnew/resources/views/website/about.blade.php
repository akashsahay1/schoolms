@extends('layouts.website')

@section('title', 'About Us')

@section('meta_description', $page?->meta_description ?? 'Learn about our school\'s history, mission, vision and values.')

@section('content')
<!-- Page Banner -->
<section class="page-banner">
    <div class="container">
        <h1>About Us</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('website.home') }}">Home</a></li>
                <li class="breadcrumb-item active">About Us</li>
            </ol>
        </nav>
    </div>
</section>

<!-- About Content -->
<section class="about-section section-padding">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <div class="about-image">
                    @if($page?->banner_image)
                        <img src="{{ asset('storage/' . $page->banner_image) }}" alt="About Us" class="img-fluid w-100" style="max-height: 500px; object-fit: cover;">
                    @else
                        <img src="{{ asset('assets/images/banner/4.jpg') }}" alt="About Us" class="img-fluid w-100" style="max-height: 500px; object-fit: cover;">
                    @endif
                    <div class="experience-badge d-none d-md-block">
                        <h3>{{ \App\Models\Setting::get('school_years', '25') }}+</h3>
                        <span>Years of<br>Excellence</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 ps-lg-5">
                <div class="about-content">
                    <h6 class="text-primary fw-bold mb-3">OUR STORY</h6>
                    <h2 class="mb-4" style="line-height: 1.3;">{{ $page?->title ?? 'A Legacy of Educational Excellence' }}</h2>
                    @if($page?->content)
                        <div class="text-muted" style="line-height: 1.8;">
                            {!! $page->content !!}
                        </div>
                    @else
                        <p class="text-muted" style="line-height: 1.8;">Welcome to {{ \App\Models\Setting::get('school_name', config('app.name')) }}, where we have been shaping young minds for over {{ \App\Models\Setting::get('school_years', '25') }} years. Our commitment to excellence in education has made us one of the leading educational institutions in the region.</p>
                        <p class="text-muted mb-4" style="line-height: 1.8;">We believe in providing a holistic education that nurtures not just academic excellence but also personal growth, creativity, and social responsibility. Our dedicated faculty and state-of-the-art facilities create an environment where students can thrive and reach their full potential.</p>
                        <div class="row">
                            <div class="col-6">
                                <div class="check-item">
                                    <i data-feather="check-circle" class="text-primary me-2"></i>
                                    <span>Quality Education</span>
                                </div>
                                <div class="check-item">
                                    <i data-feather="check-circle" class="text-primary me-2"></i>
                                    <span>Expert Faculty</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="check-item">
                                    <i data-feather="check-circle" class="text-primary me-2"></i>
                                    <span>Modern Facilities</span>
                                </div>
                                <div class="check-item">
                                    <i data-feather="check-circle" class="text-primary me-2"></i>
                                    <span>Holistic Growth</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission & Vision -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="feature-card h-100">
                    <div class="icon">
                        <i data-feather="target"></i>
                    </div>
                    <h4>Our Mission</h4>
                    <p>{{ \App\Models\Setting::get('school_mission', 'To provide quality education that empowers students with knowledge, skills, and values necessary for success in a global society, while fostering creativity, critical thinking, and character development.') }}</p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="feature-card h-100">
                    <div class="icon">
                        <i data-feather="eye"></i>
                    </div>
                    <h4>Our Vision</h4>
                    <p>{{ \App\Models\Setting::get('school_vision', 'To be a premier educational institution recognized for academic excellence, innovative teaching, and producing responsible citizens who contribute positively to society.') }}</p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="feature-card h-100">
                    <div class="icon">
                        <i data-feather="heart"></i>
                    </div>
                    <h4>Our Values</h4>
                    <p>{{ \App\Models\Setting::get('school_values', 'Excellence, Integrity, Respect, Innovation, and Responsibility. These core values guide our every action and decision in shaping the future of our students.') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Principal's Message -->
<section class="section-padding">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-4 text-center mb-4 mb-lg-0">
                @if(\App\Models\Setting::get('principal_photo'))
                    <img src="{{ asset('storage/' . \App\Models\Setting::get('principal_photo')) }}" alt="Principal" class="img-fluid rounded-circle shadow" style="width: 250px; height: 250px; object-fit: cover;">
                @else
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center shadow" style="width: 250px; height: 250px; font-size: 5rem;">
                        <i data-feather="user"></i>
                    </div>
                @endif
                <h4 class="mt-4">{{ \App\Models\Setting::get('principal_name', 'Dr. John Smith') }}</h4>
                <p class="text-muted">Principal</p>
            </div>
            <div class="col-lg-8">
                <h6 class="text-primary fw-bold">PRINCIPAL'S MESSAGE</h6>
                <h2 class="mb-4">Welcome to Our School</h2>
                <p>{{ \App\Models\Setting::get('principal_message', 'Dear Parents and Students, it is with great pleasure that I welcome you to our school. Our institution is committed to providing an enriching educational experience that prepares students for the challenges of tomorrow. We believe in nurturing not just academic excellence but also developing well-rounded individuals with strong moral values and social responsibility. Our dedicated team of educators works tirelessly to create a supportive and stimulating environment where every student can discover their potential and excel. I invite you to be part of our wonderful community where we shape futures and build dreams together.') }}</p>
            </div>
        </div>
    </div>
</section>

<!-- Stats -->
<section class="stats-section section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="stat-item">
                    <h3>{{ \App\Models\Setting::get('total_students', '1500') }}+</h3>
                    <p>Students</p>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="stat-item">
                    <h3>{{ \App\Models\Setting::get('total_teachers', '100') }}+</h3>
                    <p>Teachers</p>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="stat-item">
                    <h3>{{ \App\Models\Setting::get('school_years', '25') }}+</h3>
                    <p>Years</p>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="stat-item">
                    <h3>{{ \App\Models\Setting::get('awards_count', '50') }}+</h3>
                    <p>Awards</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
@if($testimonials->count() > 0)
<section class="section-padding">
    <div class="container">
        <div class="section-title">
            <h2>What Parents Say</h2>
            <div class="divider"></div>
            <p>Hear from our school community</p>
        </div>

        <div class="row">
            @foreach($testimonials->take(3) as $testimonial)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="testimonial-card">
                        <div class="quote-icon">
                            <i data-feather="message-circle"></i>
                        </div>
                        <p class="mb-4">{{ Str::limit($testimonial->content, 200) }}</p>
                        <div class="author">
                            @if($testimonial->photo)
                                <img src="{{ asset('storage/' . $testimonial->photo) }}" alt="{{ $testimonial->name }}">
                            @else
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; margin-right: 15px;">
                                    {{ substr($testimonial->name, 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <h6>{{ $testimonial->name }}</h6>
                                <span>{{ $testimonial->designation }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
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
