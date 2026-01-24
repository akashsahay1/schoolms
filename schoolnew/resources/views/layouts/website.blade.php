<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', \App\Models\Setting::get('school_name', config('app.name')) . ' - Quality Education for a Brighter Future')">
    <meta name="keywords" content="@yield('meta_keywords', 'school, education, learning, students, academics')">
    <meta name="author" content="{{ \App\Models\Setting::get('school_name', config('app.name')) }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('assets/images/favicon.png') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" type="image/x-icon">

    <title>@yield('title', 'Home') - {{ \App\Models\Setting::get('school_name', config('app.name')) }}</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/vendors/bootstrap.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/css/vendors/fontawesome.css') }}">
    <!-- Feather Icons -->
    <link rel="stylesheet" href="{{ asset('assets/css/vendors/feather-icon.css') }}">

    <style>
        :root {
            --primary-color: #5c61f2;
            --secondary-color: #ffa941;
            --dark-color: #2c323f;
            --light-color: #f8f9fa;
            --text-color: #6c757d;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text-color);
        }

        /* Top Bar */
        .top-bar {
            background: var(--dark-color);
            color: #fff;
            padding: 8px 0;
            font-size: 13px;
        }

        .top-bar a {
            color: #fff;
            text-decoration: none;
        }

        .top-bar a:hover {
            color: var(--secondary-color);
        }

        /* Navigation */
        .navbar {
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 0;
        }

        .navbar-brand img {
            max-height: 50px;
        }

        .navbar-nav .nav-link {
            color: var(--dark-color);
            font-weight: 500;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: var(--primary-color);
        }

        .btn-login {
            background: var(--primary-color);
            color: #fff;
            padding: 10px 25px;
            border-radius: 25px;
            font-weight: 500;
        }

        .btn-login:hover {
            background: var(--secondary-color);
            color: #fff;
        }

        /* Hero Section */
        .hero-section {
            position: relative;
            overflow: hidden;
        }

        .hero-slider .carousel-item {
            height: 600px;
            background-size: cover;
            background-position: center;
        }

        .hero-slider .carousel-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
        }

        .hero-slider .carousel-caption {
            bottom: 50%;
            transform: translateY(50%);
        }

        .hero-slider .carousel-caption h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .hero-slider .carousel-caption p {
            font-size: 1.2rem;
            margin-bottom: 30px;
        }

        /* Section Styling */
        .section-padding {
            padding: 80px 0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 15px;
        }

        .section-title p {
            font-size: 1.1rem;
            color: var(--text-color);
            max-width: 600px;
            margin: 0 auto;
        }

        .section-title .divider {
            width: 80px;
            height: 4px;
            background: var(--primary-color);
            margin: 20px auto;
            border-radius: 2px;
        }

        /* Feature Cards */
        .feature-card {
            background: #fff;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 5px 30px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .feature-card .icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: #fff;
            font-size: 32px;
        }

        .feature-card h4 {
            color: var(--dark-color);
            font-weight: 600;
            margin-bottom: 15px;
        }

        /* About Section */
        .about-section {
            background: var(--light-color);
        }

        .about-image {
            position: relative;
        }

        .about-image img {
            border-radius: 15px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.15);
        }

        .about-image .experience-badge {
            position: absolute;
            bottom: -30px;
            right: -30px;
            background: var(--primary-color);
            color: #fff;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
        }

        .about-image .experience-badge h3 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
        }

        /* Stats Section */
        .stats-section {
            background: linear-gradient(135deg, var(--primary-color), #7c81f4);
            color: #fff;
        }

        .stat-item {
            text-align: center;
            padding: 30px;
        }

        .stat-item h3 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .stat-item p {
            font-size: 1.1rem;
            margin: 0;
            opacity: 0.9;
        }

        /* Events Section */
        .event-card {
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 30px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        .event-card:hover {
            transform: translateY(-5px);
        }

        .event-card .event-date {
            position: absolute;
            top: 15px;
            left: 15px;
            background: var(--primary-color);
            color: #fff;
            padding: 10px 15px;
            border-radius: 10px;
            text-align: center;
        }

        .event-card .event-date .day {
            font-size: 1.5rem;
            font-weight: 700;
            display: block;
        }

        .event-card .event-date .month {
            font-size: 0.8rem;
            text-transform: uppercase;
        }

        .event-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .event-card .event-content {
            padding: 20px;
        }

        .event-card h5 {
            color: var(--dark-color);
            font-weight: 600;
        }

        /* News Section */
        .news-card {
            background: #fff;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .news-card:hover {
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
        }

        .news-card .news-date {
            background: var(--light-color);
            padding: 10px 15px;
            border-radius: 10px;
            text-align: center;
            min-width: 70px;
        }

        .news-card .news-date .day {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary-color);
            display: block;
        }

        .news-card h6 {
            color: var(--dark-color);
            font-weight: 600;
            margin-bottom: 5px;
        }

        /* Gallery Section */
        .gallery-item {
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 30px;
        }

        .gallery-item img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            transition: all 0.3s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.1);
        }

        .gallery-item .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(92, 97, 242, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: all 0.3s ease;
        }

        .gallery-item:hover .overlay {
            opacity: 1;
        }

        .gallery-item .overlay i {
            color: #fff;
            font-size: 2rem;
        }

        /* Testimonials */
        .testimonial-card {
            background: #fff;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 30px rgba(0,0,0,0.08);
            margin: 15px;
        }

        .testimonial-card .quote-icon {
            color: var(--primary-color);
            font-size: 2rem;
            margin-bottom: 15px;
        }

        .testimonial-card .author {
            display: flex;
            align-items: center;
            margin-top: 20px;
        }

        .testimonial-card .author img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-right: 15px;
        }

        .testimonial-card .author h6 {
            margin: 0;
            color: var(--dark-color);
            font-weight: 600;
        }

        .testimonial-card .author span {
            font-size: 0.85rem;
            color: var(--text-color);
        }

        /* CTA Section */
        .cta-section {
            background: url('/assets/images/cta-bg.jpg') center/cover no-repeat;
            position: relative;
            padding: 100px 0;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(92, 97, 242, 0.9);
        }

        .cta-section .content {
            position: relative;
            z-index: 1;
            color: #fff;
            text-align: center;
        }

        .cta-section h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .cta-section .btn-primary {
            background: var(--secondary-color);
            border: none;
            padding: 15px 40px;
            border-radius: 30px;
            font-weight: 600;
        }

        /* Footer */
        .footer {
            background: var(--dark-color);
            color: #fff;
            padding: 60px 0 20px;
        }

        .footer h5 {
            font-weight: 600;
            margin-bottom: 25px;
            position: relative;
            padding-bottom: 15px;
        }

        .footer h5::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--primary-color);
        }

        .footer-logo {
            max-width: 150px;
            margin-bottom: 20px;
        }

        .footer p {
            color: #a0a0a0;
            line-height: 1.8;
        }

        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-links li {
            margin-bottom: 12px;
        }

        .footer-links a {
            color: #a0a0a0;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--secondary-color);
            padding-left: 5px;
        }

        .footer-contact li {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
            color: #a0a0a0;
        }

        .footer-contact li i {
            color: var(--primary-color);
            margin-right: 15px;
            margin-top: 5px;
        }

        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            color: #fff;
            margin-right: 10px;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            background: var(--primary-color);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 20px;
            margin-top: 40px;
            text-align: center;
            color: #a0a0a0;
        }

        /* Page Banner */
        .page-banner {
            background: linear-gradient(135deg, var(--primary-color), #7c81f4);
            padding: 100px 0 60px;
            color: #fff;
            text-align: center;
        }

        .page-banner h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .page-banner .breadcrumb {
            justify-content: center;
            background: none;
            margin: 0;
        }

        .page-banner .breadcrumb-item a {
            color: rgba(255,255,255,0.8);
        }

        .page-banner .breadcrumb-item.active {
            color: #fff;
        }

        /* Contact Page */
        .contact-info-card {
            background: #fff;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 5px 30px rgba(0,0,0,0.08);
            height: 100%;
        }

        .contact-info-card .icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: #fff;
            font-size: 24px;
        }

        .contact-form {
            background: #fff;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 5px 30px rgba(0,0,0,0.08);
        }

        .contact-form .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 20px;
            margin-bottom: 20px;
        }

        .contact-form .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: none;
        }

        .contact-form textarea {
            min-height: 150px;
        }

        .contact-form .btn-submit {
            background: var(--primary-color);
            color: #fff;
            padding: 15px 40px;
            border-radius: 30px;
            border: none;
            font-weight: 600;
            width: 100%;
        }

        .contact-form .btn-submit:hover {
            background: var(--secondary-color);
        }

        /* Responsive */
        @media (max-width: 991px) {
            .hero-slider .carousel-item {
                height: 500px;
            }

            .hero-slider .carousel-caption h1 {
                font-size: 2rem;
            }

            .navbar-nav {
                background: #fff;
                padding: 20px;
                margin-top: 15px;
                border-radius: 10px;
            }
        }

        @media (max-width: 767px) {
            .hero-slider .carousel-item {
                height: 400px;
            }

            .section-padding {
                padding: 50px 0;
            }

            .section-title h2 {
                font-size: 1.8rem;
            }

            .stat-item h3 {
                font-size: 2rem;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar d-none d-lg-block">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <span class="me-4"><i data-feather="phone" class="me-2" style="width: 14px;"></i> {{ \App\Models\Setting::get('school_phone', '+1 234 567 890') }}</span>
                    <span><i data-feather="mail" class="me-2" style="width: 14px;"></i> {{ \App\Models\Setting::get('school_email', 'info@school.com') }}</span>
                </div>
                <div class="col-md-6 text-end">
                    <a href="#" class="me-3"><i data-feather="facebook" style="width: 14px;"></i></a>
                    <a href="#" class="me-3"><i data-feather="twitter" style="width: 14px;"></i></a>
                    <a href="#" class="me-3"><i data-feather="instagram" style="width: 14px;"></i></a>
                    <a href="#"><i data-feather="youtube" style="width: 14px;"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('website.home') }}">
                @if(\App\Models\Setting::get('school_logo'))
                    <img src="{{ asset('storage/' . \App\Models\Setting::get('school_logo')) }}" alt="{{ \App\Models\Setting::get('school_name', config('app.name')) }}">
                @else
                    <strong>{{ \App\Models\Setting::get('school_name', config('app.name')) }}</strong>
                @endif
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('website.home') ? 'active' : '' }}" href="{{ route('website.home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('website.about') ? 'active' : '' }}" href="{{ route('website.about') }}">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('website.academics') ? 'active' : '' }}" href="{{ route('website.academics') }}">Academics</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('website.facilities') ? 'active' : '' }}" href="{{ route('website.facilities') }}">Facilities</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('website.gallery*') ? 'active' : '' }}" href="{{ route('website.gallery') }}">Gallery</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('website.news*') ? 'active' : '' }}" href="{{ route('website.news') }}">News</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('website.contact') ? 'active' : '' }}" href="{{ route('website.contact') }}">Contact</a>
                    </li>
                </ul>
                <a href="{{ route('login') }}" class="btn btn-login">
                    <i data-feather="log-in" class="me-1" style="width: 16px;"></i> Login
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    @yield('content')

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <img src="{{ asset('assets/images/logo-white.png') }}" alt="Logo" class="footer-logo">
                    <p>{{ \App\Models\Setting::get('school_about', 'Providing quality education and nurturing young minds for a brighter future. Join us in this journey of excellence.') }}</p>
                    <div class="social-links mt-4">
                        <a href="#"><i data-feather="facebook"></i></a>
                        <a href="#"><i data-feather="twitter"></i></a>
                        <a href="#"><i data-feather="instagram"></i></a>
                        <a href="#"><i data-feather="youtube"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Quick Links</h5>
                    <ul class="footer-links">
                        <li><a href="{{ route('website.about') }}">About Us</a></li>
                        <li><a href="{{ route('website.academics') }}">Academics</a></li>
                        <li><a href="{{ route('website.facilities') }}">Facilities</a></li>
                        <li><a href="{{ route('website.gallery') }}">Gallery</a></li>
                        <li><a href="{{ route('website.contact') }}">Contact Us</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6 mb-4">
                    <h5>Portals</h5>
                    <ul class="footer-links">
                        <li><a href="{{ route('login') }}">Admin Login</a></li>
                        <li><a href="{{ route('login') }}">Student Login</a></li>
                        <li><a href="{{ route('login') }}">Parent Login</a></li>
                        <li><a href="{{ route('login') }}">Teacher Login</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6 mb-4">
                    <h5>Contact Info</h5>
                    <ul class="footer-contact list-unstyled">
                        <li>
                            <i data-feather="map-pin"></i>
                            <span>{{ \App\Models\Setting::get('school_address', '123 Education Street, City, Country') }}</span>
                        </li>
                        <li>
                            <i data-feather="phone"></i>
                            <span>{{ \App\Models\Setting::get('school_phone', '+1 234 567 890') }}</span>
                        </li>
                        <li>
                            <i data-feather="mail"></i>
                            <span>{{ \App\Models\Setting::get('school_email', 'info@school.com') }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} {{ \App\Models\Setting::get('school_name', config('app.name')) }}. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/icons/feather-icon/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/icons/feather-icon/feather-icon.js') }}"></script>

    <script>
        jQuery(document).ready(function() {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
