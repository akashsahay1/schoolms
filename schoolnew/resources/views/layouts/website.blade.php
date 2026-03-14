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

    @php $__favicon = \App\Models\Setting::get('school_favicon'); @endphp
    <link rel="icon" href="{{ $__favicon ? asset('storage/' . $__favicon) : asset('assets/images/favicon.png') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ $__favicon ? asset('storage/' . $__favicon) : asset('assets/images/favicon.png') }}" type="image/x-icon">

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
            --primary-color: #6065f2;
            --secondary-color: #ffa941;
            --accent-color: #796ed3;
            --dark-color: #2c323f;
            --light-color: #f8f9fa;
            --text-color: #6c757d;
            --bs-primary: #796ed3;
            --bs-primary-rgb: 121, 110, 211;
            --bs-link-color: #796ed3;
            --bs-link-hover-color: #6065f2;
        }

        * {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text-color);
            overflow-x: hidden;
        }

        /* Force white text/icons on colored backgrounds */
        .page-banner h1,
        .page-banner .breadcrumb-item,
        .page-banner .breadcrumb-item.active,
        .cta-section .content h2,
        .cta-section .content p,
        .footer h4,
        .footer h5,
        .footer p,
        .footer li,
        .footer a,
        .office-hours-header h5,
        .contact-info-card .icon i,
        .feature-card .icon i {
            color: #fff !important;
        }

        .page-banner svg,
        .cta-section svg,
        .footer svg,
        .office-hours-header svg,
        .contact-info-card .icon svg,
        .feature-card .icon svg {
            stroke: #fff !important;
        }

        /* Top Bar */
        .top-bar {
            background: var(--dark-color);
            color: #fff;
            padding: 10px 0;
            font-size: 13px;
        }

        .top-bar a {
            color: #fff;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .top-bar a:hover {
            color: var(--secondary-color);
        }

        .top-bar i {
            opacity: 0.8;
        }

        /* Navigation */
        .navbar {
            background: #fff;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 12px 0;
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            padding: 8px 0;
            box-shadow: 0 4px 25px rgba(0,0,0,0.12);
        }

        .navbar-brand img {
            max-height: 55px;
            transition: all 0.3s ease;
        }

        .navbar.scrolled .navbar-brand img {
            max-height: 45px;
        }

        .navbar-nav .nav-link {
            color: var(--dark-color);
            font-weight: 500;
            padding: 12px 18px;
            transition: all 0.3s ease;
            position: relative;
        }

        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            bottom: 5px;
            left: 18px;
            right: 18px;
            height: 2px;
            background: var(--secondary-color);
            transform: scaleX(0);
            transition: transform 0.3s ease;
            border-radius: 2px;
        }

        .navbar-nav .nav-link:hover::after,
        .navbar-nav .nav-link.active::after {
            transform: scaleX(1);
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: var(--primary-color);
        }

        .btn-login {
            background: var(--primary-color);
            color: #fff;
            padding: 12px 28px;
            border-radius: 50px;
            font-weight: 500;
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(96, 101, 242, 0.3);
        }

        .btn-login:hover {
            background: var(--secondary-color);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 169, 65, 0.4);
        }

        /* Hero Section */
        .hero-section {
            position: relative;
            overflow: hidden;
        }

        .hero-slider .carousel-item {
            height: 650px;
            background-size: cover;
            background-position: center;
            background-color: var(--primary-color);
        }

        .hero-slider .carousel-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(96, 101, 242, 0.85);
        }

        .hero-slider .carousel-caption {
            bottom: 50%;
            transform: translateY(50%);
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .hero-slider .carousel-caption h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 25px;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.3);
            animation: fadeInUp 0.8s ease;
        }

        .hero-slider .carousel-caption p {
            font-size: 1.3rem;
            margin-bottom: 35px;
            opacity: 0.95;
            text-shadow: 1px 1px 4px rgba(0,0,0,0.2);
            animation: fadeInUp 1s ease;
        }

        .hero-slider .carousel-caption .btn {
            padding: 15px 40px;
            font-size: 1.1rem;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            animation: fadeInUp 1.2s ease;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }

        .hero-slider .carousel-caption .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(0,0,0,0.3);
        }

        .hero-slider .carousel-control-prev,
        .hero-slider .carousel-control-next {
            width: 60px;
            height: 60px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
            opacity: 1;
            transition: all 0.3s ease;
        }

        .hero-slider .carousel-control-prev {
            left: 30px;
        }

        .hero-slider .carousel-control-next {
            right: 30px;
        }

        .hero-slider .carousel-control-prev:hover,
        .hero-slider .carousel-control-next:hover {
            background: var(--primary-color);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Section Styling */
        .section-padding {
            padding: 100px 0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 60px;
            position: relative;
        }

        .section-title h2 {
            font-size: 2.8rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 15px;
            position: relative;
            display: inline-block;
        }

        .section-title p {
            font-size: 1.15rem;
            color: var(--text-color);
            max-width: 650px;
            margin: 0 auto;
            line-height: 1.8;
        }

        .section-title .divider {
            width: 80px;
            height: 4px;
            background: var(--secondary-color);
            margin: 20px auto;
            border-radius: 2px;
            position: relative;
        }

        .section-title .divider::before,
        .section-title .divider::after {
            content: '';
            position: absolute;
            width: 10px;
            height: 10px;
            background: var(--primary-color);
            border-radius: 50%;
            top: -3px;
        }

        .section-title .divider::before {
            left: -15px;
        }

        .section-title .divider::after {
            right: -15px;
            background: var(--secondary-color);
        }

        /* Feature Cards */
        .feature-card {
            background: #fff;
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.06);
            transition: all 0.4s ease;
            height: 100%;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(96, 101, 242, 0.08);
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--primary-color);
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }

        .feature-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 25px 60px rgba(96, 101, 242, 0.15);
        }

        .feature-card:hover::before {
            transform: scaleX(1);
        }

        .feature-card .icon {
            width: 90px;
            height: 90px;
            background: var(--accent-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            color: #fff !important;
            font-size: 36px;
            box-shadow: 0 10px 30px rgba(121, 110, 211, 0.3);
            transition: all 0.4s ease;
        }

        .feature-card:hover .icon {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 15px 40px rgba(96, 101, 242, 0.4);
        }

        .feature-card h4 {
            color: var(--dark-color);
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 1.25rem;
        }

        .feature-card p {
            color: var(--text-color);
            line-height: 1.7;
            margin-bottom: 0;
        }

        .feature-card ul {
            margin-top: 20px;
            display: inline-block;
            text-align: left;
        }

        .feature-card ul li {
            display: flex;
            align-items: center;
            padding: 10px 0;
            color: var(--dark-color);
            font-size: 0.95rem;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .feature-card ul li:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .feature-card ul li:first-child {
            padding-top: 0;
        }

        .feature-card ul li i {
            flex-shrink: 0;
            color: var(--primary-color);
        }

        /* Methodology Cards */
        .methodology-card {
            text-align: center;
            padding: 35px 25px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.06);
            transition: all 0.4s ease;
            height: 100%;
            border: 1px solid rgba(96, 101, 242, 0.08);
        }

        .methodology-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(96, 101, 242, 0.12);
        }

        .methodology-icon {
            width: 90px;
            height: 90px;
            background: rgba(121, 110, 211, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            transition: all 0.4s ease;
        }

        .methodology-icon i,
        .methodology-icon svg {
            color: var(--primary-color);
            stroke: var(--primary-color);
            width: 36px;
            height: 36px;
            transition: all 0.4s ease;
        }

        .methodology-card:hover .methodology-icon {
            background: var(--accent-color);
        }

        .methodology-card:hover .methodology-icon i,
        .methodology-card:hover .methodology-icon svg {
            color: #fff;
            stroke: #fff !important;
        }

        .methodology-card h5 {
            color: var(--dark-color);
            font-weight: 600;
            margin-bottom: 15px;
        }

        .methodology-card p {
            color: var(--text-color);
            font-size: 0.95rem;
            line-height: 1.7;
            margin-bottom: 0;
        }

        /* About Section */
        .about-section {
            background: #f8f9fa;
            position: relative;
            overflow: hidden;
        }

        .about-section::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 300px;
            height: 300px;
            background: rgba(121, 110, 211, 0.1);
            border-radius: 50%;
        }

        .about-section::after {
            content: '';
            position: absolute;
            bottom: -50px;
            left: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255, 169, 65, 0.1);
            border-radius: 50%;
        }

        .about-image {
            position: relative;
            z-index: 1;
        }

        .about-image::before {
            content: '';
            position: absolute;
            top: -20px;
            left: -20px;
            width: 100%;
            height: 100%;
            border: 4px solid var(--primary-color);
            border-radius: 20px;
            z-index: -1;
            opacity: 0.3;
        }

        .about-image img {
            border-radius: 20px;
            box-shadow: 0 25px 60px rgba(96, 101, 242, 0.2);
            transition: all 0.4s ease;
        }

        .about-image:hover img {
            transform: translateY(-5px);
            box-shadow: 0 35px 70px rgba(96, 101, 242, 0.25);
        }

        .about-image .experience-badge {
            position: absolute;
            bottom: -30px;
            right: -30px;
            background: var(--primary-color);
            color: #fff;
            padding: 30px 35px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 15px 40px rgba(96, 101, 242, 0.4);
            animation: pulse 2s infinite;
        }

        .about-image .experience-badge h3 {
            font-size: 2.8rem;
            font-weight: 700;
            margin: 0;
            line-height: 1;
        }

        .about-image .experience-badge span {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.02);
            }
        }

        .about-content h2 {
            color: var(--dark-color);
            font-weight: 700;
            position: relative;
        }

        .about-content h6 {
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .about-content .check-item {
            display: flex;
            align-items: center;
            padding: 8px 0;
            transition: all 0.3s ease;
        }

        .about-content .check-item:hover {
            transform: translateX(5px);
        }

        .about-content .check-item i {
            color: var(--primary-color);
            margin-right: 12px;
        }

        /* Stats Section */
        .stats-section {
            background: var(--primary-color);
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .stats-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E");
        }

        .stat-item {
            text-align: center;
            padding: 40px 20px;
            position: relative;
            z-index: 1;
        }

        .stat-item h3 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 10px;
            text-shadow: 2px 2px 10px rgba(0,0,0,0.2);
        }

        .stat-item p {
            font-size: 1.15rem;
            margin: 0;
            opacity: 0.95;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 500;
        }

        .stat-item::after {
            content: '';
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 1px;
            height: 60px;
            background: rgba(255,255,255,0.2);
        }

        .stat-item:last-child::after {
            display: none;
        }

        /* Events Section */
        .event-card {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            transition: all 0.4s ease;
            height: 100%;
        }

        .event-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(96, 101, 242, 0.15);
        }

        .event-card .event-date {
            position: absolute;
            top: 15px;
            left: 15px;
            background: var(--primary-color);
            color: #fff;
            padding: 12px 18px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(96, 101, 242, 0.4);
            z-index: 2;
        }

        .event-card .event-date .day {
            font-size: 1.6rem;
            font-weight: 700;
            display: block;
            line-height: 1;
        }

        .event-card .event-date .month {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.9;
        }

        .event-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .event-card:hover img {
            transform: scale(1.05);
        }

        .event-card .event-content {
            padding: 25px 20px;
        }

        .event-card h5 {
            color: var(--dark-color);
            font-weight: 600;
            margin-bottom: 10px;
            transition: color 0.3s ease;
        }

        .event-card:hover h5 {
            color: var(--primary-color);
        }

        /* News Section */
        .news-card {
            background: #fff;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            transition: all 0.4s ease;
            display: flex;
            align-items: flex-start;
            gap: 18px;
            border: 1px solid rgba(96, 101, 242, 0.08);
        }

        .news-card:hover {
            box-shadow: 0 10px 35px rgba(96, 101, 242, 0.12);
            transform: translateX(5px);
            border-color: rgba(96, 101, 242, 0.15);
        }

        .news-card .news-date {
            background: rgba(121, 110, 211, 0.1);
            padding: 12px 16px;
            border-radius: 12px;
            text-align: center;
            min-width: 70px;
            flex-shrink: 0;
        }

        .news-card .news-date .day {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--primary-color);
            display: block;
            line-height: 1;
        }

        .news-card .news-date .small {
            font-size: 0.75rem;
            color: var(--secondary-color);
            text-transform: uppercase;
            font-weight: 600;
        }

        .news-card h6 {
            color: var(--dark-color);
            font-weight: 600;
            margin-bottom: 8px;
            transition: color 0.3s ease;
        }

        .news-card:hover h6 {
            color: var(--primary-color);
        }

        /* News List Card */
        .news-list-card {
            display: flex;
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.06);
            transition: all 0.4s ease;
            height: 100%;
            border: 1px solid rgba(96, 101, 242, 0.08);
        }

        .news-list-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 50px rgba(96, 101, 242, 0.12);
        }

        .news-list-date {
            background: var(--primary-color);
            color: #fff;
            padding: 25px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-width: 100px;
        }

        .news-list-date .day {
            font-size: 2.2rem;
            font-weight: 700;
            line-height: 1;
        }

        .news-list-date .month {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.9;
            margin-top: 5px;
        }

        .news-list-date .year {
            font-size: 0.8rem;
            opacity: 0.7;
        }

        .news-list-content {
            padding: 25px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .news-list-badges {
            display: flex;
            gap: 8px;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }

        .badge-category {
            background: rgba(96, 101, 242, 0.12);
            color: var(--primary-color);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-important {
            background: #dc3545;
            color: #fff;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .news-list-content h5 {
            margin-bottom: 10px;
        }

        .news-list-content h5 a {
            color: var(--dark-color);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .news-list-content h5 a:hover {
            color: var(--primary-color);
        }

        .news-list-content p {
            color: var(--text-color);
            font-size: 0.9rem;
            line-height: 1.7;
            margin-bottom: 15px;
            flex: 1;
        }

        .news-list-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary-color);
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .news-list-link i {
            width: 16px;
            height: 16px;
            transition: transform 0.3s ease;
        }

        .news-list-link:hover {
            color: var(--secondary-color);
        }

        .news-list-link:hover i {
            transform: translateX(5px);
        }

        /* News Detail Page */
        .news-detail-card {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.06);
        }

        .news-detail-header {
            background: rgba(121, 110, 211, 0.05);
            padding: 30px 35px;
            border-bottom: 1px solid rgba(0,0,0,0.06);
        }

        .news-detail-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 15px;
        }

        .news-detail-date,
        .news-detail-category {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-color);
            font-size: 0.95rem;
        }

        .news-detail-date i,
        .news-detail-category i {
            width: 18px;
            height: 18px;
            color: var(--primary-color);
        }

        .news-detail-important {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #dc3545;
            color: #fff;
            padding: 8px 16px;
            border-radius: 25px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .news-detail-important i {
            width: 16px;
            height: 16px;
        }

        .news-detail-body {
            padding: 40px 35px;
        }

        .news-detail-content {
            color: var(--text-color);
            line-height: 1.9;
            font-size: 1.05rem;
        }

        .news-detail-content p {
            margin-bottom: 18px;
        }

        .news-detail-content h1,
        .news-detail-content h2,
        .news-detail-content h3,
        .news-detail-content h4,
        .news-detail-content h5,
        .news-detail-content h6 {
            color: var(--dark-color);
            margin-top: 25px;
            margin-bottom: 15px;
        }

        .news-detail-content ul,
        .news-detail-content ol {
            margin-bottom: 18px;
            padding-left: 25px;
        }

        .news-detail-content li {
            margin-bottom: 8px;
        }

        .news-detail-attachment {
            display: flex;
            align-items: center;
            gap: 20px;
            background: rgba(121, 110, 211, 0.08);
            padding: 25px;
            border-radius: 15px;
            margin-top: 35px;
        }

        .attachment-icon {
            width: 60px;
            height: 60px;
            background: var(--accent-color);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .attachment-icon i {
            color: #fff;
            width: 28px;
            height: 28px;
        }

        .attachment-info {
            flex: 1;
        }

        .attachment-info h6 {
            color: var(--dark-color);
            font-weight: 600;
            margin-bottom: 5px;
        }

        .attachment-info p {
            color: var(--text-color);
            font-size: 0.9rem;
            margin: 0;
        }

        .news-detail-footer {
            padding: 25px 35px;
            border-top: 1px solid rgba(0,0,0,0.06);
            background: rgba(0,0,0,0.02);
        }

        @media (max-width: 767px) {
            .news-list-card {
                flex-direction: column;
            }

            .news-list-date {
                flex-direction: row;
                gap: 10px;
                padding: 15px 20px;
                min-width: auto;
            }

            .news-list-date .day {
                font-size: 1.5rem;
            }

            .news-detail-header,
            .news-detail-body,
            .news-detail-footer {
                padding: 25px 20px;
            }

            .news-detail-attachment {
                flex-direction: column;
                text-align: center;
            }
        }

        /* Event Card Improvements */
        .event-image-wrapper {
            position: relative;
            overflow: hidden;
        }

        .event-placeholder {
            height: 200px;
            background: var(--accent-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }

        .event-placeholder i {
            width: 60px;
            height: 60px;
            opacity: 0.5;
        }

        .event-card .event-content h5 a {
            color: var(--dark-color);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .event-card .event-content h5 a:hover {
            color: var(--primary-color);
        }

        .event-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 12px;
        }

        .event-meta span {
            display: flex;
            align-items: center;
            font-size: 0.85rem;
            color: var(--text-color);
        }

        .event-meta span i {
            width: 14px;
            height: 14px;
            margin-right: 5px;
            color: var(--primary-color);
        }

        .event-desc {
            color: var(--text-color);
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.06);
        }

        .empty-state-icon {
            width: 120px;
            height: 120px;
            background: rgba(121, 110, 211, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
        }

        .empty-state-icon i {
            width: 50px;
            height: 50px;
            color: var(--primary-color);
        }

        .empty-state h4 {
            color: var(--dark-color);
            font-weight: 600;
            margin-bottom: 15px;
        }

        .empty-state p {
            color: var(--text-color);
            margin-bottom: 25px;
        }

        /* Event Detail Page */
        .event-detail-card {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.06);
            margin-bottom: 30px;
        }

        .event-detail-image img {
            width: 100%;
            height: 400px;
            object-fit: cover;
        }

        .event-detail-placeholder {
            height: 300px;
            background: var(--accent-color);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #fff;
        }

        .event-detail-placeholder i {
            width: 80px;
            height: 80px;
            opacity: 0.5;
            margin-bottom: 15px;
        }

        .event-detail-placeholder span {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .event-detail-body {
            padding: 35px;
        }

        .event-description {
            color: var(--text-color);
            line-height: 1.9;
        }

        .event-description p {
            margin-bottom: 15px;
        }

        /* Event Photos */
        .event-photos-card {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.06);
        }

        .event-photos-header {
            padding: 20px 25px;
            border-bottom: 1px solid rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .event-photos-header i {
            color: var(--primary-color);
            width: 22px;
            height: 22px;
        }

        .event-photos-header h5 {
            margin: 0;
            font-weight: 600;
            color: var(--dark-color);
        }

        .event-photos-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            padding: 25px;
        }

        .event-photo-item {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            aspect-ratio: 1;
        }

        .event-photo-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .event-photo-item:hover img {
            transform: scale(1.1);
        }

        .event-photo-overlay {
            position: absolute;
            inset: 0;
            background: rgba(96, 101, 242, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .event-photo-item:hover .event-photo-overlay {
            opacity: 1;
        }

        .event-photo-overlay i {
            color: #fff;
            width: 30px;
            height: 30px;
        }

        /* Event Info Card */
        .event-info-card {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.06);
            margin-bottom: 20px;
        }

        .event-info-header {
            background: var(--primary-color);
            padding: 20px 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #fff;
        }

        .event-info-header i {
            width: 22px;
            height: 22px;
        }

        .event-info-header h5 {
            margin: 0;
            font-weight: 600;
        }

        .event-info-body {
            padding: 25px;
        }

        .event-info-item {
            display: flex;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid rgba(0,0,0,0.06);
        }

        .event-info-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .event-info-item:first-child {
            padding-top: 0;
        }

        .event-info-icon {
            width: 45px;
            height: 45px;
            background: rgba(121, 110, 211, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .event-info-icon i {
            color: var(--primary-color);
            width: 20px;
            height: 20px;
        }

        .event-info-content {
            display: flex;
            flex-direction: column;
        }

        .event-info-content strong {
            color: var(--dark-color);
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 3px;
        }

        .event-info-content span {
            color: var(--text-color);
            font-size: 0.95rem;
        }

        @media (max-width: 767px) {
            .event-photos-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .event-detail-image img {
                height: 250px;
            }

            .event-detail-body {
                padding: 25px;
            }
        }

        /* Gallery Section */
        .gallery-item {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            margin-bottom: 30px;
            cursor: pointer;
        }

        .gallery-item img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            transition: all 0.5s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.15);
        }

        .gallery-item .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(96, 101, 242, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: all 0.4s ease;
        }

        .gallery-item:hover .overlay {
            opacity: 1;
        }

        .gallery-item .overlay i {
            color: #fff;
            font-size: 2.5rem;
            transform: scale(0.5);
            transition: transform 0.4s ease;
        }

        .gallery-item:hover .overlay i {
            transform: scale(1);
        }

        /* Testimonials */
        .testimonial-card {
            background: #fff;
            border-radius: 20px;
            padding: 35px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.06);
            margin: 15px;
            position: relative;
            border: 1px solid rgba(96, 101, 242, 0.08);
            transition: all 0.4s ease;
        }

        .testimonial-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 30px;
            right: 30px;
            height: 4px;
            background: var(--secondary-color);
            border-radius: 0 0 4px 4px;
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .testimonial-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 50px rgba(96, 101, 242, 0.12);
        }

        .testimonial-card:hover::before {
            opacity: 1;
        }

        .testimonial-card .quote-icon {
            color: var(--primary-color);
            font-size: 2.5rem;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        .testimonial-card p {
            color: var(--text-color);
            line-height: 1.8;
            font-style: italic;
        }

        .testimonial-card .author {
            display: flex;
            align-items: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid rgba(0,0,0,0.08);
        }

        .testimonial-card .author img {
            width: 65px;
            height: 65px;
            border-radius: 50%;
            margin-right: 18px;
            border: 3px solid rgba(96, 101, 242, 0.2);
        }

        .testimonial-card .author h6 {
            margin: 0 0 5px;
            color: var(--dark-color);
            font-weight: 600;
            font-size: 1.05rem;
        }

        .testimonial-card .author span {
            font-size: 0.85rem;
            color: var(--primary-color);
            font-weight: 500;
        }

        /* CTA Section */
        .cta-section {
            background: url('/assets/images/banner/4.jpg') center/cover no-repeat fixed;
            position: relative;
            padding: 120px 0;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(96, 101, 242, 0.85);
            pointer-events: none;
        }

        @keyframes ctaPulse {
            0%, 100% {
                transform: translate(0, 0);
            }
            50% {
                transform: translate(25%, 25%);
            }
        }

        .cta-section .content {
            position: relative;
            z-index: 1;
            color: #fff !important;
            text-align: center;
        }

        .cta-section h2 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 25px;
            color: #fff !important;
            text-shadow: 2px 2px 10px rgba(0,0,0,0.3);
        }

        .cta-section p {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto 30px;
        }

        .cta-section .btn-primary {
            background: var(--secondary-color);
            border: none;
            padding: 18px 50px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 10px 30px rgba(255,169,65,0.4);
            transition: all 0.4s ease;
        }

        .cta-section .btn-primary:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(255,169,65,0.5);
            background: var(--secondary-color);
            filter: brightness(1.1);
        }

        /* Footer */
        .footer {
            background: #796ed3;
            color: #fff !important;
            padding: 80px 0 30px;
            position: relative;
        }

        .footer h5 {
            font-weight: 600;
            margin-bottom: 30px;
            position: relative;
            padding-bottom: 15px;
            font-size: 1.15rem;
            color: #fff !important;
        }

        .footer h5::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: #fff;
            border-radius: 2px;
        }

        .footer-logo {
            max-width: 160px;
            margin-bottom: 25px;
        }

        .footer p {
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.9;
        }

        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-links li {
            margin-bottom: 14px;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
        }

        .footer-links a::before {
            content: '';
            width: 0;
            height: 2px;
            background: #fff;
            margin-right: 0;
            transition: all 0.3s ease;
        }

        .footer-links a:hover {
            color: #fff;
            padding-left: 8px;
        }

        .footer-links a:hover::before {
            width: 15px;
            margin-right: 8px;
        }

        .footer-contact li {
            display: flex;
            align-items: flex-start;
            margin-bottom: 18px;
            color: rgba(255, 255, 255, 0.85);
        }

        .footer-contact a {
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-contact a:hover {
            color: #fff;
        }

        .footer-contact li i {
            color: #fff;
            margin-right: 15px;
            margin-top: 5px;
            min-width: 20px;
        }

        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            color: #fff;
            margin-right: 12px;
            transition: all 0.4s ease;
        }

        .social-links a:hover {
            background: #fff;
            color: #796ed3;
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            padding-top: 25px;
            margin-top: 50px;
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
        }

        .footer-bottom p {
            margin: 0;
            font-size: 0.95rem;
        }

        /* Page Banner */
        .page-banner {
            background: url('/assets/images/banner/4.jpg') center/cover no-repeat fixed;
            padding: 120px 0 80px;
            color: #fff !important;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .page-banner[style*="background-image"] {
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        .page-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--banner-color, #6065f2);
            opacity: 0.85;
            pointer-events: none;
        }

        .page-banner h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: #fff !important;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.2);
            position: relative;
            z-index: 1;
            animation: fadeInDown 0.6s ease;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .page-banner .breadcrumb {
            justify-content: center;
            background: rgba(255,255,255,0.15);
            margin: 0;
            padding: 12px 25px;
            border-radius: 50px;
            display: inline-flex;
            position: relative;
            z-index: 1;
            backdrop-filter: blur(10px);
        }

        .page-banner .breadcrumb-item a {
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .page-banner .breadcrumb-item a:hover {
            color: var(--secondary-color);
        }

        .page-banner .breadcrumb-item.active {
            color: #fff;
            font-weight: 500;
        }

        .page-banner .breadcrumb-item + .breadcrumb-item::before {
            color: rgba(255,255,255,0.6);
        }

        /* Contact Page */
        .contact-info-card {
            background: #fff;
            border-radius: 20px;
            padding: 35px 30px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.06);
            height: 100%;
            transition: all 0.4s ease;
            border: 1px solid rgba(96, 101, 242, 0.08);
        }

        .contact-info-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(96, 101, 242, 0.12);
        }

        .contact-info-card .icon {
            width: 80px;
            height: 80px;
            background: var(--accent-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            color: #fff !important;
            font-size: 28px;
            box-shadow: 0 10px 30px rgba(121, 110, 211, 0.3);
            transition: all 0.4s ease;
        }

        .contact-info-card:hover .icon {
            transform: scale(1.1);
        }

        .contact-info-card h5 {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 12px;
        }

        .contact-form {
            background: #fff;
            border-radius: 20px;
            padding: 45px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.06);
            border: 1px solid rgba(96, 101, 242, 0.08);
        }

        .contact-form h4 {
            color: var(--dark-color);
            font-weight: 600;
        }

        .contact-form .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 14px 22px;
            margin-bottom: 20px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .contact-form .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(96, 101, 242, 0.1);
        }

        .contact-form textarea {
            min-height: 160px;
            resize: vertical;
        }

        .contact-form .btn-submit {
            background: var(--primary-color);
            color: #fff;
            padding: 16px 45px;
            border-radius: 50px;
            border: none;
            font-weight: 600;
            width: 100%;
            font-size: 1rem;
            box-shadow: 0 8px 25px rgba(96, 101, 242, 0.3);
            transition: all 0.4s ease;
        }

        .contact-form .btn-submit:hover {
            background: var(--secondary-color);
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(255, 169, 65, 0.4);
        }

        .contact-form-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(0,0,0,0.08);
        }

        .contact-form-header i {
            color: var(--primary-color);
            width: 28px;
            height: 28px;
        }

        .contact-form-header h4 {
            margin: 0;
            color: var(--dark-color);
            font-weight: 600;
        }

        .contact-form .form-group {
            margin-bottom: 20px;
        }

        .contact-form .form-label {
            font-weight: 500;
            color: var(--dark-color);
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .contact-info-card p {
            color: var(--text-color);
            margin-bottom: 0;
            line-height: 1.7;
        }

        .contact-info-card p a {
            color: var(--text-color);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .contact-info-card p a:hover {
            color: var(--primary-color);
        }

        /* Contact Map */
        .contact-map-wrapper {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.06);
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .contact-map-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 25px 30px;
            border-bottom: 1px solid rgba(0,0,0,0.08);
        }

        .contact-map-header i {
            color: var(--primary-color);
            width: 28px;
            height: 28px;
        }

        .contact-map-header h4 {
            margin: 0;
            color: var(--dark-color);
            font-weight: 600;
        }

        .contact-map {
            flex: 1;
            min-height: 400px;
        }

        .contact-map iframe {
            width: 100%;
            height: 100%;
            min-height: 400px;
            border: none;
        }

        /* Office Hours Card */
        .office-hours-card {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.06);
            height: 100%;
            border: 1px solid rgba(96, 101, 242, 0.08);
            transition: all 0.4s ease;
        }

        .office-hours-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 50px rgba(96, 101, 242, 0.12);
        }

        .office-hours-header {
            background: var(--primary-color);
            padding: 25px 30px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .office-hours-icon {
            width: 50px;
            height: 50px;
            background: rgba(255,255,255,0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .office-hours-icon i,
        .office-hours-icon svg {
            color: #fff !important;
            stroke: #fff !important;
            width: 24px;
            height: 24px;
        }

        .office-hours-header h5 {
            margin: 0;
            color: #fff !important;
            font-weight: 600;
        }

        .office-hours-body {
            padding: 25px 30px;
        }

        .hours-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid rgba(0,0,0,0.06);
        }

        .hours-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .hours-item:first-child {
            padding-top: 0;
        }

        .hours-day {
            color: var(--text-color);
            font-weight: 500;
        }

        .hours-time {
            color: var(--dark-color);
            font-weight: 600;
            background: rgba(121, 110, 211, 0.1);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.9rem;
        }

        .hours-item.closed .hours-time {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        /* Carousel Indicators */
        .hero-slider .carousel-indicators {
            bottom: 30px;
        }

        .hero-slider .carousel-indicators button {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin: 0 6px;
            background: rgba(255,255,255,0.5);
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .hero-slider .carousel-indicators button.active {
            background: #fff;
            transform: scale(1.2);
        }

        /* Bootstrap Primary Color Overrides */
        .bg-primary {
            background-color: #796ed3 !important;
        }

        .text-primary {
            color: #796ed3 !important;
        }

        .border-primary {
            border-color: #796ed3 !important;
        }

        /* Button Styles */
        .btn-primary {
            background: #796ed3;
            border-color: #796ed3;
            transition: all 0.3s ease;
        }

        .btn-primary:hover,
        .btn-primary:focus,
        .btn-primary:active {
            background: var(--primary-color);
            border-color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(121, 110, 211, 0.3);
        }

        .btn-outline-primary {
            border: 2px solid #796ed3;
            color: #796ed3;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover,
        .btn-outline-primary:focus,
        .btn-outline-primary:active {
            background: #796ed3;
            border-color: #796ed3;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(121, 110, 211, 0.3);
        }

        .btn-check:checked + .btn-outline-primary,
        .btn-check:active + .btn-outline-primary {
            background: #796ed3;
            border-color: #796ed3;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .hero-slider .carousel-item {
                height: 550px;
            }

            .hero-slider .carousel-caption h1 {
                font-size: 2.5rem;
            }

            .hero-slider .carousel-caption p {
                font-size: 1.1rem;
            }

            .hero-slider .carousel-control-prev,
            .hero-slider .carousel-control-next {
                width: 45px;
                height: 45px;
            }

            .hero-slider .carousel-control-prev {
                left: 15px;
            }

            .hero-slider .carousel-control-next {
                right: 15px;
            }

            .navbar-nav {
                background: #fff;
                padding: 20px;
                margin-top: 15px;
                border-radius: 10px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            }

            .section-padding {
                padding: 70px 0;
            }

            .page-banner {
                padding: 100px 0 60px;
            }

            .page-banner h1 {
                font-size: 2.2rem;
            }
        }

        @media (max-width: 767px) {
            .hero-slider .carousel-item {
                height: 500px;
            }

            .hero-slider .carousel-caption h1 {
                font-size: 1.8rem;
                margin-bottom: 15px;
            }

            .hero-slider .carousel-caption p {
                font-size: 1rem;
                margin-bottom: 25px;
            }

            .hero-slider .carousel-caption .btn {
                padding: 12px 30px;
                font-size: 0.95rem;
            }

            .section-padding {
                padding: 60px 0;
            }

            .section-title h2 {
                font-size: 2rem;
            }

            .section-title p {
                font-size: 1rem;
            }

            .stat-item h3 {
                font-size: 2.2rem;
            }

            .stat-item p {
                font-size: 0.9rem;
                letter-spacing: 1px;
            }

            .stat-item::after {
                display: none;
            }

            .page-banner {
                padding: 80px 0 50px;
            }

            .page-banner h1 {
                font-size: 1.8rem;
            }

            .cta-section {
                padding: 80px 0;
            }

            .cta-section h2 {
                font-size: 2rem;
            }

            .feature-card {
                padding: 30px 20px;
            }

            .about-image .experience-badge {
                padding: 20px 25px;
                bottom: -20px;
                right: -10px;
            }

            .about-image .experience-badge h3 {
                font-size: 2rem;
            }

            .about-image::before {
                top: -10px;
                left: -10px;
            }
        }

        @media (max-width: 575px) {
            .hero-slider .carousel-item {
                height: 450px;
            }

            .hero-slider .carousel-control-prev,
            .hero-slider .carousel-control-next {
                display: none;
            }

            .section-title .divider::before,
            .section-title .divider::after {
                display: none;
            }
        }

        /* BG-OPACITY SUPPORT (not in this Bootstrap) */
        .bg-primary.bg-opacity-10 { background-color: rgba(115, 102, 255, 0.1) !important; }
        .bg-primary.bg-opacity-15 { background-color: rgba(115, 102, 255, 0.15) !important; }
        .bg-primary.bg-opacity-25 { background-color: rgba(115, 102, 255, 0.25) !important; }
        .bg-success.bg-opacity-10 { background-color: rgba(101, 193, 92, 0.1) !important; }
        .bg-success.bg-opacity-15 { background-color: rgba(101, 193, 92, 0.15) !important; }
        .bg-success.bg-opacity-25 { background-color: rgba(101, 193, 92, 0.25) !important; }
        .bg-danger.bg-opacity-10 { background-color: rgba(252, 86, 74, 0.1) !important; }
        .bg-danger.bg-opacity-15 { background-color: rgba(252, 86, 74, 0.15) !important; }
        .bg-danger.bg-opacity-25 { background-color: rgba(252, 86, 74, 0.25) !important; }
        .bg-warning.bg-opacity-10 { background-color: rgba(255, 184, 41, 0.1) !important; }
        .bg-warning.bg-opacity-15 { background-color: rgba(255, 184, 41, 0.15) !important; }
        .bg-warning.bg-opacity-25 { background-color: rgba(255, 184, 41, 0.25) !important; }
        .bg-info.bg-opacity-10 { background-color: rgba(64, 184, 245, 0.1) !important; }
        .bg-info.bg-opacity-15 { background-color: rgba(64, 184, 245, 0.15) !important; }
        .bg-info.bg-opacity-25 { background-color: rgba(64, 184, 245, 0.25) !important; }
        .bg-secondary.bg-opacity-10 { background-color: rgba(131, 131, 131, 0.1) !important; }
        .bg-secondary.bg-opacity-15 { background-color: rgba(131, 131, 131, 0.15) !important; }
        .bg-secondary.bg-opacity-25 { background-color: rgba(131, 131, 131, 0.25) !important; }
        .bg-dark.bg-opacity-10 { background-color: rgba(63, 71, 90, 0.1) !important; }
        .bg-dark.bg-opacity-15 { background-color: rgba(63, 71, 90, 0.15) !important; }
        .bg-dark.bg-opacity-25 { background-color: rgba(63, 71, 90, 0.25) !important; }
        .bg-white.bg-opacity-25 { background-color: rgba(255, 255, 255, 0.25) !important; }

        /* Button text color fix */
        .btn-primary, .btn-success, .btn-danger, .btn-warning,
        .btn-info, .btn-secondary, .btn-dark {
            color: #fff !important;
        }
        .btn-light, .btn-white {
            color: #000 !important;
        }
        .btn-outline-primary, .btn-outline-success, .btn-outline-danger,
        .btn-outline-warning, .btn-outline-info, .btn-outline-secondary, .btn-outline-dark {
            color: inherit;
        }
        .btn-outline-primary:hover, .btn-outline-success:hover, .btn-outline-danger:hover,
        .btn-outline-warning:hover, .btn-outline-info:hover, .btn-outline-secondary:hover, .btn-outline-dark:hover {
            color: #fff !important;
        }

        /* White text on ALL colored backgrounds */
        .card-header.bg-primary *,
        .card-header.bg-success *,
        .card-header.bg-danger *,
        .card-header.bg-info *,
        .card-header.bg-secondary *,
        .card-header.bg-dark *,
        .card.bg-primary .card-body *,
        .card.bg-success .card-body *,
        .card.bg-danger .card-body *,
        .card.bg-info .card-body *,
        .card.bg-secondary .card-body *,
        .card.bg-dark .card-body *,
        .card-body.bg-primary *,
        .card-body.bg-success *,
        .card-body.bg-danger *,
        .card-body.bg-info *,
        .card-body.bg-secondary *,
        .card-body.bg-dark *,
        thead.bg-primary *,
        thead.bg-success *,
        thead.bg-danger *,
        thead.bg-info *,
        thead.bg-secondary *,
        thead.bg-dark * {
            color: #fff !important;
        }
        .card-header.bg-warning *,
        .card.bg-warning .card-body *,
        .card-body.bg-warning *,
        thead.bg-warning * {
            color: #fff !important;
        }
        /* Dark text on white/light backgrounds */
        .bg-white, .bg-white *,
        .bg-light, .bg-light * {
            color: #000 !important;
        }
        .bg-white svg, .bg-white [data-feather],
        .bg-light svg, .bg-light [data-feather] {
            stroke: #000 !important;
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
                    <a href="tel:{{ \App\Models\Setting::get('school_phone', '+1234567890') }}" class="me-4 text-white text-decoration-none"><i data-feather="phone" class="me-2" style="width: 14px;"></i> {{ \App\Models\Setting::get('school_phone', '+1 234 567 890') }}</a>
                    <a href="mailto:{{ \App\Models\Setting::get('school_email', 'info@school.com') }}" class="text-white text-decoration-none"><i data-feather="mail" class="me-2" style="width: 14px;"></i> {{ \App\Models\Setting::get('school_email', 'info@school.com') }}</a>
                </div>
                <div class="col-md-6 text-end">
                    @php
                        $__socialLinks = [
                            ['key' => 'social_facebook', 'icon' => 'facebook'],
                            ['key' => 'social_twitter', 'icon' => 'twitter'],
                            ['key' => 'social_instagram', 'icon' => 'instagram'],
                            ['key' => 'social_youtube', 'icon' => 'youtube'],
                            ['key' => 'social_linkedin', 'icon' => 'linkedin'],
                        ];
                    @endphp
                    @foreach($__socialLinks as $__social)
                        @if(\App\Models\Setting::get($__social['key']))
                            <a href="{{ \App\Models\Setting::get($__social['key']) }}" target="_blank" rel="noopener noreferrer" class="me-3"><i data-feather="{{ $__social['icon'] }}" style="width: 14px;"></i></a>
                        @endif
                    @endforeach
                    @if(\App\Models\Setting::get('social_whatsapp'))
                        <a href="{{ \App\Models\Setting::get('social_whatsapp') }}" target="_blank" rel="noopener noreferrer"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg></a>
                    @endif
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
                    <span style="font-size: 20px; font-weight: 700; color: #2c323f;">{{ \App\Models\Setting::get('school_name', config('app.name')) }}</span>
                @endif
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            @php
                $navPages = \App\Models\WebsitePage::where('is_active', true)->pluck('slug')->toArray();
            @endphp
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('website.home') ? 'active' : '' }}" href="{{ route('website.home') }}">Home</a>
                    </li>
                    @if(in_array('about', $navPages))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('website.about') ? 'active' : '' }}" href="{{ route('website.about') }}">About Us</a>
                    </li>
                    @endif
                    @if(in_array('academics', $navPages))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('website.academics') ? 'active' : '' }}" href="{{ route('website.academics') }}">Academics</a>
                    </li>
                    @endif
                    @if(in_array('facilities', $navPages))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('website.facilities') ? 'active' : '' }}" href="{{ route('website.facilities') }}">Facilities</a>
                    </li>
                    @endif
                    @if(in_array('gallery', $navPages))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('website.gallery*') ? 'active' : '' }}" href="{{ route('website.gallery') }}">Gallery</a>
                    </li>
                    @endif
                    @if(in_array('news', $navPages) || in_array('events', $navPages))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('website.news*') ? 'active' : '' }}" href="{{ route('website.news') }}">News</a>
                    </li>
                    @endif
                    @if(in_array('contact', $navPages))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('website.contact') ? 'active' : '' }}" href="{{ route('website.contact') }}">Contact</a>
                    </li>
                    @endif
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
                    @if(\App\Models\Setting::get('school_logo'))
                        <img src="{{ asset('storage/' . \App\Models\Setting::get('school_logo')) }}" alt="Logo" class="footer-logo">
                    @else
                        <h4 style="font-weight: 700; color: #fff;">{{ \App\Models\Setting::get('school_name', config('app.name')) }}</h4>
                    @endif
                    <p>{{ \App\Models\Setting::get('school_about', 'Providing quality education and nurturing young minds for a brighter future. Join us in this journey of excellence.') }}</p>
                    <div class="social-links mt-4">
                        @foreach($__socialLinks as $__social)
                            @if(\App\Models\Setting::get($__social['key']))
                                <a href="{{ \App\Models\Setting::get($__social['key']) }}" target="_blank" rel="noopener noreferrer"><i data-feather="{{ $__social['icon'] }}"></i></a>
                            @endif
                        @endforeach
                        @if(\App\Models\Setting::get('social_whatsapp'))
                            <a href="{{ \App\Models\Setting::get('social_whatsapp') }}" target="_blank" rel="noopener noreferrer"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg></a>
                        @endif
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Quick Links</h5>
                    <ul class="footer-links">
                        @if(in_array('about', $navPages))<li><a href="{{ route('website.about') }}">About Us</a></li>@endif
                        @if(in_array('academics', $navPages))<li><a href="{{ route('website.academics') }}">Academics</a></li>@endif
                        @if(in_array('facilities', $navPages))<li><a href="{{ route('website.facilities') }}">Facilities</a></li>@endif
                        @if(in_array('gallery', $navPages))<li><a href="{{ route('website.gallery') }}">Gallery</a></li>@endif
                        @if(in_array('contact', $navPages))<li><a href="{{ route('website.contact') }}">Contact Us</a></li>@endif
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
                            <a href="tel:{{ \App\Models\Setting::get('school_phone', '+1234567890') }}">{{ \App\Models\Setting::get('school_phone', '+1 234 567 890') }}</a>
                        </li>
                        <li>
                            <i data-feather="mail"></i>
                            <a href="mailto:{{ \App\Models\Setting::get('school_email', 'info@school.com') }}">{{ \App\Models\Setting::get('school_email', 'info@school.com') }}</a>
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
            // Initialize Feather icons
            if (typeof feather !== 'undefined') {
                feather.replace();
            }

            // Navbar scroll effect
            jQuery(window).scroll(function() {
                if (jQuery(this).scrollTop() > 100) {
                    jQuery('.navbar').addClass('scrolled');
                } else {
                    jQuery('.navbar').removeClass('scrolled');
                }
            });

            // Smooth scroll for anchor links
            jQuery('a[href*="#"]:not([href="#"])').click(function() {
                if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
                    var target = jQuery(this.hash);
                    target = target.length ? target : jQuery('[name=' + this.hash.slice(1) + ']');
                    if (target.length) {
                        jQuery('html, body').animate({
                            scrollTop: target.offset().top - 80
                        }, 800);
                        return false;
                    }
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
