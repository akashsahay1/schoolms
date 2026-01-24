@extends('layouts.website')

@section('title', 'Welcome')

@section('content')
<!-- Hero Slider -->
<section class="hero-section">
    <div id="heroSlider" class="carousel slide hero-slider" data-bs-ride="carousel">
        <div class="carousel-inner">
            @forelse($sliders as $index => $slider)
                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}" style="background-image: url('{{ $slider->image ? asset('storage/' . $slider->image) : asset('assets/images/default-banner.jpg') }}')">
                    <div class="carousel-caption">
                        @if($slider->title)
                            <h1>{{ $slider->title }}</h1>
                        @endif
                        @if($slider->subtitle)
                            <p>{{ $slider->subtitle }}</p>
                        @endif
                        @if($slider->button_text && $slider->button_link)
                            <a href="{{ $slider->button_link }}" class="btn btn-primary btn-lg">{{ $slider->button_text }}</a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="carousel-item active" style="background-image: url('{{ asset('assets/images/default-banner.jpg') }}')">
                    <div class="carousel-caption">
                        <h1>Welcome to {{ \App\Models\Setting::get('school_name', config('app.name')) }}</h1>
                        <p>Quality Education for a Brighter Future</p>
                        <a href="{{ route('website.about') }}" class="btn btn-primary btn-lg">Learn More</a>
                    </div>
                </div>
            @endforelse
        </div>
        @if($sliders->count() > 1)
            <button class="carousel-control-prev" type="button" data-bs-target="#heroSlider" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroSlider" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        @endif
    </div>
</section>

<!-- Features/Quick Links -->
<section class="section-padding">
    <div class="container">
        <div class="section-title">
            <h2>Why Choose Us</h2>
            <div class="divider"></div>
            <p>Discover what makes our school the perfect place for your child's education</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="feature-card">
                    <div class="icon">
                        <i data-feather="book-open"></i>
                    </div>
                    <h4>Quality Education</h4>
                    <p>Comprehensive curriculum designed to nurture young minds and develop critical thinking skills.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-card">
                    <div class="icon">
                        <i data-feather="users"></i>
                    </div>
                    <h4>Expert Faculty</h4>
                    <p>Dedicated teachers with years of experience in education and child development.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-card">
                    <div class="icon">
                        <i data-feather="award"></i>
                    </div>
                    <h4>Modern Facilities</h4>
                    <p>State-of-the-art classrooms, labs, and recreational areas for holistic development.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-card">
                    <div class="icon">
                        <i data-feather="heart"></i>
                    </div>
                    <h4>Safe Environment</h4>
                    <p>Secure campus with caring staff ensuring your child's safety and well-being.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="about-section section-padding">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="about-image">
                    <img src="{{ asset('assets/images/about-school.jpg') }}" alt="About School" class="img-fluid">
                    <div class="experience-badge d-none d-md-block">
                        <h3>{{ \App\Models\Setting::get('school_years', '25') }}+</h3>
                        <span>Years of<br>Excellence</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <h6 class="text-primary fw-bold">ABOUT US</h6>
                <h2 class="mb-4">A Legacy of Educational Excellence</h2>
                <p class="mb-4">{{ \App\Models\Setting::get('school_about', 'Our school has been a beacon of educational excellence for over two decades. We are committed to providing quality education that shapes young minds and prepares them for the challenges of tomorrow.') }}</p>
                <div class="row mb-4">
                    <div class="col-6">
                        <div class="d-flex align-items-center mb-3">
                            <i data-feather="check-circle" class="text-primary me-2"></i>
                            <span>Experienced Teachers</span>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i data-feather="check-circle" class="text-primary me-2"></i>
                            <span>Modern Curriculum</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center mb-3">
                            <i data-feather="check-circle" class="text-primary me-2"></i>
                            <span>Sports Activities</span>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i data-feather="check-circle" class="text-primary me-2"></i>
                            <span>Smart Classes</span>
                        </div>
                    </div>
                </div>
                <a href="{{ route('website.about') }}" class="btn btn-primary">Read More <i data-feather="arrow-right" class="ms-2" style="width: 16px;"></i></a>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
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

<!-- Facilities Section -->
@if($facilities->count() > 0)
<section class="section-padding">
    <div class="container">
        <div class="section-title">
            <h2>Our Facilities</h2>
            <div class="divider"></div>
            <p>World-class infrastructure to support your child's learning journey</p>
        </div>

        <div class="row g-4">
            @foreach($facilities as $facility)
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="icon">
                            @if($facility->icon)
                                <i data-feather="{{ $facility->icon }}"></i>
                            @else
                                <i data-feather="star"></i>
                            @endif
                        </div>
                        <h4>{{ $facility->title }}</h4>
                        <p>{{ $facility->description }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-5">
            <a href="{{ route('website.facilities') }}" class="btn btn-outline-primary btn-lg">View All Facilities</a>
        </div>
    </div>
</section>
@endif

<!-- Events & News Section -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row">
            <!-- Upcoming Events -->
            <div class="col-lg-7 mb-4 mb-lg-0">
                <h4 class="mb-4"><i data-feather="calendar" class="me-2" style="width: 20px;"></i> Upcoming Events</h4>
                <div class="row g-4">
                    @forelse($events as $event)
                        <div class="col-md-6">
                            <div class="event-card position-relative">
                                <div class="event-date">
                                    <span class="day">{{ $event->start_date->format('d') }}</span>
                                    <span class="month">{{ $event->start_date->format('M') }}</span>
                                </div>
                                @if($event->photos->count() > 0)
                                    <img src="{{ asset('storage/' . $event->photos->first()->photo_path) }}" alt="{{ $event->title }}">
                                @else
                                    <img src="{{ asset('assets/images/event-default.jpg') }}" alt="{{ $event->title }}">
                                @endif
                                <div class="event-content">
                                    <h5>{{ $event->title }}</h5>
                                    <p class="text-muted small mb-0">
                                        <i data-feather="map-pin" style="width: 14px;"></i> {{ $event->location ?? 'School Campus' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info">No upcoming events at the moment.</div>
                        </div>
                    @endforelse
                </div>
                @if($events->count() > 0)
                    <div class="text-center mt-4">
                        <a href="{{ route('website.events') }}" class="btn btn-outline-primary">View All Events</a>
                    </div>
                @endif
            </div>

            <!-- Latest News -->
            <div class="col-lg-5">
                <h4 class="mb-4"><i data-feather="file-text" class="me-2" style="width: 20px;"></i> Latest News</h4>
                @forelse($notices as $notice)
                    <a href="{{ route('website.news.show', $notice) }}" class="text-decoration-none">
                        <div class="news-card">
                            <div class="news-date">
                                <span class="day">{{ $notice->publish_date->format('d') }}</span>
                                <span class="small">{{ $notice->publish_date->format('M') }}</span>
                            </div>
                            <div>
                                <h6>{{ Str::limit($notice->title, 50) }}</h6>
                                <p class="text-muted small mb-0">{{ Str::limit(strip_tags($notice->content), 60) }}</p>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="alert alert-info">No news available at the moment.</div>
                @endforelse
                @if($notices->count() > 0)
                    <div class="text-center mt-3">
                        <a href="{{ route('website.news') }}" class="btn btn-outline-primary btn-sm">View All News</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Gallery Section -->
@if($gallery->count() > 0)
<section class="section-padding">
    <div class="container">
        <div class="section-title">
            <h2>Photo Gallery</h2>
            <div class="divider"></div>
            <p>Glimpses of our school life and activities</p>
        </div>

        <div class="row g-4">
            @foreach($gallery as $item)
                <div class="col-lg-3 col-md-4 col-6">
                    <div class="gallery-item">
                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}">
                        <div class="overlay">
                            <i data-feather="zoom-in"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-5">
            <a href="{{ route('website.gallery') }}" class="btn btn-primary btn-lg">View Full Gallery</a>
        </div>
    </div>
</section>
@endif

<!-- Testimonials Section -->
@if($testimonials->count() > 0)
<section class="section-padding bg-light">
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

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="content">
            <h2>Ready to Join Our School?</h2>
            <p class="mb-4">Take the first step towards your child's bright future</p>
            <a href="{{ route('website.contact') }}" class="btn btn-primary btn-lg">Contact Us Today</a>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
    // Initialize Feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endpush
