<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ config('app.name') }} - Student Portal">
    <meta name="author" content="School Management System">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('assets/images/favicon.png') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" type="image/x-icon">

    <title>@yield('title', 'Dashboard') - Student Portal</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Rubik:400,400i,500,500i,700,700i&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900&amp;display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/fontawesome.css') }}">
    <!-- ICO Font -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/icofont.css') }}">
    <!-- Themify Icon -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/themify.css') }}">
    <!-- Flag Icon -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/flag-icon.css') }}">
    <!-- Feather Icon -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/feather-icon.css') }}">

    <!-- Plugins CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/scrollbar.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/slick.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/slick-theme.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
    @stack('styles')

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/bootstrap.css') }}">
    <!-- App CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}">
    <link id="color" rel="stylesheet" href="{{ asset('assets/css/color-1.css') }}" media="screen">
    <!-- Responsive CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive.css') }}">

    <style>
        .logo-custom {
            max-height: 45px !important;
            max-width: 200px !important;
            object-fit: contain;
        }
        .sidebar-main-title { margin-bottom: 5px !important; }
        .sidebar-list:not(:first-child) { margin-top: 2px; }
        .logo-wrapper { padding: 15px 20px !important; margin-bottom: 10px; }
        .sidebar-main-title + .sidebar-list { margin-top: 8px !important; }
        .portal-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
            margin-left: 8px;
        }
    </style>
</head>
<body>
    <!-- Loader -->
    <div class="loader-wrapper">
        <div class="loader-index"><span></span></div>
        <svg><defs></defs><filter id="goo"><fegaussianblur in="SourceGraphic" stddeviation="11" result="blur"></fegaussianblur><fecolormatrix in="blur" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 19 -9" result="goo"></fecolormatrix></filter></svg>
    </div>

    <!-- Tap on Top -->
    <div class="tap-top"><i data-feather="chevrons-up"></i></div>

    <!-- Page Wrapper -->
    <div class="page-wrapper compact-wrapper" id="pageWrapper">
        <!-- Page Header -->
        @include('portal.components.header')

        <!-- Page Body -->
        <div class="page-body-wrapper">
            <!-- Page Sidebar -->
            @include('portal.components.sidebar')

            <!-- Page Body -->
            <div class="page-body">
                <!-- Page Title / Breadcrumb -->
                <div class="container-fluid">
                    <div class="page-title">
                        <div class="row">
                            <div class="col-sm-6">
                                <h3>@yield('page-title', 'Dashboard')</h3>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('portal.dashboard') }}">
                                            <svg class="stroke-icon">
                                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                            </svg>
                                        </a>
                                    </li>
                                    @yield('breadcrumb')
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                @yield('content')
            </div>

            <!-- Footer -->
            @include('components.footer')
        </div>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <!-- Feather Icon JS -->
    <script src="{{ asset('assets/js/icons/feather-icon/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/icons/feather-icon/feather-icon.js') }}"></script>
    <!-- Scrollbar JS -->
    <script src="{{ asset('assets/js/scrollbar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/scrollbar/custom.js') }}"></script>
    <!-- Sidebar jQuery -->
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <!-- Sidebar JS -->
    <script src="{{ asset('assets/js/sidebar-menu.js') }}"></script>
    <script src="{{ asset('assets/js/sidebar-pin.js') }}"></script>
    <!-- Slick Slider JS -->
    <script src="{{ asset('assets/js/slick/slick.js') }}"></script>
    <script src="{{ asset('assets/js/slick/slick.min.js') }}"></script>
    <script src="{{ asset('assets/js/header-slick.js') }}"></script>
    <!-- Height Equal JS -->
    <script src="{{ asset('assets/js/height-equal.js') }}"></script>
    <!-- SweetAlert2 JS -->
    <script src="{{ asset('assets/js/sweet-alert/sweetalert.min.js') }}"></script>

    @stack('scripts')

    <script src="{{ asset('assets/js/script.js') }}"></script>

    <script>
        if (typeof feather !== 'undefined') { feather.replace(); }

        jQuery.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content') }
        });

        jQuery(document).ready(function() {
            jQuery('#sidebar-toggle-btn').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                jQuery('.sidebar-wrapper').toggleClass('close_icon');
                jQuery('.page-header').toggleClass('close_icon');
            });

            jQuery(document).on('click', '.toggle-sidebar', function(e) {
                if (jQuery(this).attr('id') !== 'sidebar-toggle-btn') {
                    e.preventDefault();
                    jQuery('.sidebar-wrapper').toggleClass('close_icon');
                    jQuery('.page-header').toggleClass('close_icon');
                }
            });

            jQuery(document).on('click', '.bg-overlay', function() {
                jQuery('.sidebar-wrapper').addClass('close_icon');
                jQuery('.page-header').addClass('close_icon');
                jQuery(this).remove();
            });
        });
    </script>
</body>
</html>
