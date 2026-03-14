@extends('layouts.website')

@section('title', 'Academics')

@section('meta_description', $page?->meta_description ?? 'Explore our comprehensive academic programs and curriculum.')

@section('content')
<!-- Page Banner -->
<section class="page-banner" @if($page) style="{{ $page->banner_image ? 'background-image: url(' . asset('storage/' . $page->banner_image) . ');' : '' }}{{ $page->banner_color ? '--banner-color: ' . $page->banner_color . ';' : '' }}" @endif>
    <div class="container">
        <h1>Academics</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('website.home') }}">Home</a></li>
                <li class="breadcrumb-item active">Academics</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Academic Overview -->
<section class="about-section section-padding">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <div class="about-image">
                    @if($page?->banner_image)
                        <img src="{{ asset('storage/' . $page->banner_image) }}" alt="Academics" class="img-fluid w-100" style="max-height: 450px; object-fit: cover;">
                    @else
                        <img src="{{ asset('assets/images/banner/2.jpg') }}" alt="Academics" class="img-fluid w-100" style="max-height: 450px; object-fit: cover;">
                    @endif
                </div>
            </div>
            <div class="col-lg-6 ps-lg-5">
                <div class="about-content">
                    <h6 class="text-primary fw-bold mb-3">OUR CURRICULUM</h6>
                    <h2 class="mb-4" style="line-height: 1.3;">{{ $page?->title ?? 'Excellence in Academic Education' }}</h2>
                    @if($page?->content)
                        <div class="text-muted" style="line-height: 1.8;">
                            {!! $page->content !!}
                        </div>
                    @else
                        <p class="text-muted" style="line-height: 1.8;">Our curriculum is designed to provide a comprehensive and balanced education that develops critical thinking, creativity, and a love for learning. We follow a structured approach that combines academic rigor with practical application.</p>
                        <p class="text-muted mb-4" style="line-height: 1.8;">We are affiliated with recognized education boards and follow their guidelines while incorporating innovative teaching methodologies to ensure our students are well-prepared for future challenges.</p>
                        <div class="row">
                            <div class="col-6">
                                <div class="check-item">
                                    <i data-feather="check-circle" class="text-primary me-2"></i>
                                    <span>CBSE Affiliated</span>
                                </div>
                                <div class="check-item">
                                    <i data-feather="check-circle" class="text-primary me-2"></i>
                                    <span>Modern Pedagogy</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="check-item">
                                    <i data-feather="check-circle" class="text-primary me-2"></i>
                                    <span>Smart Classes</span>
                                </div>
                                <div class="check-item">
                                    <i data-feather="check-circle" class="text-primary me-2"></i>
                                    <span>Lab Facilities</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Academic Programs -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="section-title">
            <h2>Academic Programs</h2>
            <div class="divider"></div>
            <p>Comprehensive education from kindergarten to higher secondary</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="feature-card h-100">
                    <div class="icon">
                        <i data-feather="smile"></i>
                    </div>
                    <h4>Pre-Primary (Nursery - KG)</h4>
                    <p>A play-based learning approach that develops social skills, motor skills, and a foundation for formal education through fun activities and games.</p>
                    <ul class="list-unstyled mt-3">
                        <li><i data-feather="check" class="text-primary me-2" style="width: 16px;"></i> Activity-based learning</li>
                        <li><i data-feather="check" class="text-primary me-2" style="width: 16px;"></i> Language development</li>
                        <li><i data-feather="check" class="text-primary me-2" style="width: 16px;"></i> Number concepts</li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card h-100">
                    <div class="icon">
                        <i data-feather="book-open"></i>
                    </div>
                    <h4>Primary (Classes 1-5)</h4>
                    <p>Building strong fundamentals in core subjects while encouraging curiosity and developing essential academic skills.</p>
                    <ul class="list-unstyled mt-3">
                        <li><i data-feather="check" class="text-primary me-2" style="width: 16px;"></i> Core subjects foundation</li>
                        <li><i data-feather="check" class="text-primary me-2" style="width: 16px;"></i> Environmental studies</li>
                        <li><i data-feather="check" class="text-primary me-2" style="width: 16px;"></i> Computer basics</li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card h-100">
                    <div class="icon">
                        <i data-feather="layers"></i>
                    </div>
                    <h4>Middle School (Classes 6-8)</h4>
                    <p>Expanding knowledge horizons with specialized subjects and developing analytical thinking abilities.</p>
                    <ul class="list-unstyled mt-3">
                        <li><i data-feather="check" class="text-primary me-2" style="width: 16px;"></i> Science and Mathematics</li>
                        <li><i data-feather="check" class="text-primary me-2" style="width: 16px;"></i> Social Sciences</li>
                        <li><i data-feather="check" class="text-primary me-2" style="width: 16px;"></i> Languages</li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card h-100">
                    <div class="icon">
                        <i data-feather="award"></i>
                    </div>
                    <h4>Secondary (Classes 9-10)</h4>
                    <p>Preparing students for board examinations with focused academics and career guidance.</p>
                    <ul class="list-unstyled mt-3">
                        <li><i data-feather="check" class="text-primary me-2" style="width: 16px;"></i> Board exam preparation</li>
                        <li><i data-feather="check" class="text-primary me-2" style="width: 16px;"></i> Career counseling</li>
                        <li><i data-feather="check" class="text-primary me-2" style="width: 16px;"></i> Practical labs</li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card h-100">
                    <div class="icon">
                        <i data-feather="trending-up"></i>
                    </div>
                    <h4>Senior Secondary (Classes 11-12)</h4>
                    <p>Specialized streams - Science, Commerce, and Arts - to prepare students for higher education and careers.</p>
                    <ul class="list-unstyled mt-3">
                        <li><i data-feather="check" class="text-primary me-2" style="width: 16px;"></i> Science (PCM/PCB)</li>
                        <li><i data-feather="check" class="text-primary me-2" style="width: 16px;"></i> Commerce with Maths</li>
                        <li><i data-feather="check" class="text-primary me-2" style="width: 16px;"></i> Arts/Humanities</li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card h-100">
                    <div class="icon">
                        <i data-feather="star"></i>
                    </div>
                    <h4>Co-Curricular Activities</h4>
                    <p>A wide range of activities to develop well-rounded personalities and discover hidden talents.</p>
                    <ul class="list-unstyled mt-3">
                        <li><i data-feather="check" class="text-primary me-2" style="width: 16px;"></i> Sports & Games</li>
                        <li><i data-feather="check" class="text-primary me-2" style="width: 16px;"></i> Music & Dance</li>
                        <li><i data-feather="check" class="text-primary me-2" style="width: 16px;"></i> Art & Craft</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Teaching Methodology -->
<section class="section-padding">
    <div class="container">
        <div class="section-title">
            <h2>Our Teaching Methodology</h2>
            <div class="divider"></div>
            <p>Innovative approaches for effective learning</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="methodology-card">
                    <div class="methodology-icon">
                        <i data-feather="monitor"></i>
                    </div>
                    <h5>Smart Learning</h5>
                    <p>Interactive digital content and multimedia resources for engaging lessons</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="methodology-card">
                    <div class="methodology-icon">
                        <i data-feather="users"></i>
                    </div>
                    <h5>Collaborative Learning</h5>
                    <p>Group projects and peer learning to develop teamwork and communication</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="methodology-card">
                    <div class="methodology-icon">
                        <i data-feather="tool"></i>
                    </div>
                    <h5>Practical Approach</h5>
                    <p>Hands-on experiments and real-world applications of concepts</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="methodology-card">
                    <div class="methodology-icon">
                        <i data-feather="user-check"></i>
                    </div>
                    <h5>Individual Attention</h5>
                    <p>Personalized guidance and support for every student's unique needs</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="content">
            <h2>Ready to Give Your Child the Best Education?</h2>
            <p class="mb-4">Contact us to learn more about admissions and academic programs</p>
            <a href="{{ route('website.contact') }}" class="btn btn-primary btn-lg">Get in Touch</a>
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
