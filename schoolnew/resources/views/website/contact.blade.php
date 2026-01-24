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
        <div class="row g-4 mb-5">
            <div class="col-lg-4 col-md-6">
                <div class="contact-info-card">
                    <div class="icon">
                        <i data-feather="map-pin"></i>
                    </div>
                    <h5>Our Address</h5>
                    <p class="text-muted mb-0">{{ \App\Models\Setting::get('school_address', '123 Education Street, City, Country') }}</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="contact-info-card">
                    <div class="icon">
                        <i data-feather="phone"></i>
                    </div>
                    <h5>Phone Number</h5>
                    <p class="text-muted mb-0">
                        {{ \App\Models\Setting::get('school_phone', '+1 234 567 890') }}
                        @if(\App\Models\Setting::get('school_phone_2'))
                            <br>{{ \App\Models\Setting::get('school_phone_2') }}
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
                    <p class="text-muted mb-0">
                        {{ \App\Models\Setting::get('school_email', 'info@school.com') }}
                        @if(\App\Models\Setting::get('school_email_2'))
                            <br>{{ \App\Models\Setting::get('school_email_2') }}
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <!-- Contact Form -->
                <div class="contact-form">
                    <h4 class="mb-4">Send us a Message</h4>

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('website.contact.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" name="name" class="form-control" placeholder="Your Name *" value="{{ old('name') }}" required>
                            </div>
                            <div class="col-md-6">
                                <input type="email" name="email" class="form-control" placeholder="Your Email *" value="{{ old('email') }}" required>
                            </div>
                        </div>
                        <input type="tel" name="phone" class="form-control" placeholder="Phone Number" value="{{ old('phone') }}">
                        <input type="text" name="subject" class="form-control" placeholder="Subject *" value="{{ old('subject') }}" required>
                        <textarea name="message" class="form-control" placeholder="Your Message *" required>{{ old('message') }}</textarea>
                        <button type="submit" class="btn btn-submit">
                            <i data-feather="send" class="me-2" style="width: 16px;"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-lg-6">
                <!-- Map -->
                <div class="rounded overflow-hidden h-100" style="min-height: 400px;">
                    @if(\App\Models\Setting::get('school_map_embed'))
                        {!! \App\Models\Setting::get('school_map_embed') !!}
                    @else
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3022.2!2d-73.98!3d40.75!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zM40xMzQnMTYuOCJOIDczwrA1OCc0OC4wIlc!5e0!3m2!1sen!2sus!4v1600000000000!5m2!1sen!2sus" width="100%" height="100%" style="border:0; min-height: 400px;" allowfullscreen="" loading="lazy"></iframe>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Office Hours -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-lg-5">
                        <h4 class="text-center mb-4"><i data-feather="clock" class="me-2" style="width: 24px;"></i> Office Hours</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="fw-bold">School Office</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="text-muted">Monday - Friday</td>
                                        <td>8:00 AM - 4:00 PM</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Saturday</td>
                                        <td>9:00 AM - 1:00 PM</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Sunday</td>
                                        <td>Closed</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold">Admissions Office</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="text-muted">Monday - Friday</td>
                                        <td>9:00 AM - 3:00 PM</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Saturday</td>
                                        <td>10:00 AM - 12:00 PM</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Sunday</td>
                                        <td>Closed</td>
                                    </tr>
                                </table>
                            </div>
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
