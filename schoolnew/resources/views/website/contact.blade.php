@extends('layouts.website')

@section('title', 'Contact Us')

@section('meta_description', $page?->meta_description ?? 'Get in touch with us. We\'d love to hear from you.')

@section('content')
<!-- Page Banner -->
<section class="page-banner">
    <div class="container">
        <h1>Contact Us</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('website.home') }}">Home</a></li>
                <li class="breadcrumb-item active">Contact</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Contact Info Cards -->
<section class="section-padding">
    <div class="container">
        <div class="section-title">
            <h2>Get In Touch</h2>
            <div class="divider"></div>
            <p>We'd love to hear from you. Reach out to us through any of these channels.</p>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-lg-4 col-md-6">
                <div class="contact-info-card">
                    <div class="icon">
                        <i data-feather="map-pin"></i>
                    </div>
                    <h5>Our Address</h5>
                    <p>{{ \App\Models\Setting::get('school_address', '123 Education Street, City, Country') }}</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="contact-info-card">
                    <div class="icon">
                        <i data-feather="phone"></i>
                    </div>
                    <h5>Phone Number</h5>
                    <p>
                        <a href="tel:{{ \App\Models\Setting::get('school_phone', '+1234567890') }}">{{ \App\Models\Setting::get('school_phone', '+1 234 567 890') }}</a>
                        @if(\App\Models\Setting::get('school_phone_2'))
                            <br><a href="tel:{{ \App\Models\Setting::get('school_phone_2') }}">{{ \App\Models\Setting::get('school_phone_2') }}</a>
                        @endif
                    </p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="contact-info-card">
                    <div class="icon">
                        <i data-feather="mail"></i>
                    </div>
                    <h5>Email Address</h5>
                    <p>
                        <a href="mailto:{{ \App\Models\Setting::get('school_email', 'info@school.com') }}">{{ \App\Models\Setting::get('school_email', 'info@school.com') }}</a>
                        @if(\App\Models\Setting::get('school_email_2'))
                            <br><a href="mailto:{{ \App\Models\Setting::get('school_email_2') }}">{{ \App\Models\Setting::get('school_email_2') }}</a>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <!-- Contact Form -->
                <div class="contact-form">
                    <div class="contact-form-header">
                        <i data-feather="edit-3"></i>
                        <h4>Send us a Message</h4>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i data-feather="check-circle" class="me-2" style="width: 18px;"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('website.contact.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Your Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" placeholder="John Doe" value="{{ old('name') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Your Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" placeholder="john@example.com" value="{{ old('email') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Phone Number</label>
                                    <input type="tel" name="phone" class="form-control" placeholder="+1 234 567 890" value="{{ old('phone') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Subject <span class="text-danger">*</span></label>
                                    <input type="text" name="subject" class="form-control" placeholder="How can we help?" value="{{ old('subject') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Your Message <span class="text-danger">*</span></label>
                            <textarea name="message" class="form-control" placeholder="Write your message here..." required>{{ old('message') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-submit">
                            <i data-feather="send" style="width: 18px;"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-lg-6">
                <!-- Map -->
                <div class="contact-map-wrapper">
                    <div class="contact-map-header">
                        <i data-feather="map"></i>
                        <h4>Find Us on Map</h4>
                    </div>
                    <div class="contact-map">
                        @if(\App\Models\Setting::get('school_map_embed'))
                            {!! \App\Models\Setting::get('school_map_embed') !!}
                        @else
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3022.2!2d-73.98!3d40.75!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zM40xMzQnMTYuOCJOIDczwrA1OCc0OC4wIlc!5e0!3m2!1sen!2sus!4v1600000000000!5m2!1sen!2sus" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Office Hours -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="section-title">
            <h2>Office Hours</h2>
            <div class="divider"></div>
            <p>Visit us during our working hours</p>
        </div>

        <div class="row g-4 justify-content-center">
            <div class="col-lg-5 col-md-6">
                <div class="office-hours-card">
                    <div class="office-hours-header">
                        <div class="office-hours-icon">
                            <i data-feather="briefcase"></i>
                        </div>
                        <h5>School Office</h5>
                    </div>
                    <div class="office-hours-body">
                        <div class="hours-item">
                            <span class="hours-day">Monday - Friday</span>
                            <span class="hours-time">8:00 AM - 4:00 PM</span>
                        </div>
                        <div class="hours-item">
                            <span class="hours-day">Saturday</span>
                            <span class="hours-time">9:00 AM - 1:00 PM</span>
                        </div>
                        <div class="hours-item closed">
                            <span class="hours-day">Sunday</span>
                            <span class="hours-time">Closed</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 col-md-6">
                <div class="office-hours-card">
                    <div class="office-hours-header">
                        <div class="office-hours-icon">
                            <i data-feather="user-plus"></i>
                        </div>
                        <h5>Admissions Office</h5>
                    </div>
                    <div class="office-hours-body">
                        <div class="hours-item">
                            <span class="hours-day">Monday - Friday</span>
                            <span class="hours-time">9:00 AM - 3:00 PM</span>
                        </div>
                        <div class="hours-item">
                            <span class="hours-day">Saturday</span>
                            <span class="hours-time">10:00 AM - 12:00 PM</span>
                        </div>
                        <div class="hours-item closed">
                            <span class="hours-day">Sunday</span>
                            <span class="hours-time">Closed</span>
                        </div>
                    </div>
                </div>
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
