<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	@php
		$portalUser = Auth::user();
		$isStudentPortal = \App\Models\Student::where('user_id', $portalUser->id)->exists();
		$isParentPortal = false;
		if (!$isStudentPortal) {
			$isParentPortal = \App\Models\ParentGuardian::where('user_id', $portalUser->id)->exists()
				|| \App\Models\ParentGuardian::where('father_email', $portalUser->email)->exists()
				|| \App\Models\ParentGuardian::where('mother_email', $portalUser->email)->exists()
				|| \App\Models\ParentGuardian::where('guardian_email', $portalUser->email)->exists();
		}
	@endphp
	<meta name="description" content="{{ config('app.name') }} - {{ $isParentPortal ? 'Parent' : 'Student' }} Portal">
	<meta name="author" content="School Management System">
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<link rel="icon" href="{{ asset('assets/images/favicon.png') }}" type="image/x-icon">
	<link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" type="image/x-icon">

	<title>@yield('title', $isParentPortal ? 'Parent Portal' : 'Student Portal') - {{ config('app.name') }}</title>

	<!-- Google Fonts -->
	<link href="https://fonts.googleapis.com/css?family=Rubik:400,400i,500,500i,700,700i&amp;display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900&amp;display=swap" rel="stylesheet">

	<!-- Font Awesome -->
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/fontawesome.css') }}">
	<!-- ICO Font -->
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/icofont.css') }}">
	<!-- Themify Icon -->
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/themify.css') }}">
	<!-- Feather Icon -->
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/feather-icon.css') }}">

	<!-- Plugins CSS -->
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/scrollbar.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/sweetalert2.css') }}">
	@stack('styles')

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/bootstrap.css') }}">
	<!-- App CSS -->
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}">
	<link id="color" rel="stylesheet" href="{{ asset('assets/css/color-1.css') }}" media="screen">
	<!-- Responsive CSS -->
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive.css') }}">

	<!-- Custom Styles -->
	<style>
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

		/* Text overflow prevention */
		.card-body p, .card-body td, .card-body a,
		.card-body span, .list-unstyled a, .list-unstyled span {
			overflow-wrap: break-word;
			word-wrap: break-word;
		}
		.d-flex > span, .d-flex > div {
			min-width: 0;
		}

		/* Card text color fix */
		.card-body span:not([class*="bg-"]):not([class*="text-"]):not(.badge),
		.card-body strong:not([class*="text-"]),
		.card-body p:not([class*="text-"]),
		.card-body label,
		.card-body h6:not([class*="text-"]),
		.card-header h5:not([class*="text-"]) {
			color: #2c323f !important;
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
		.card-body.bg-dark * {
			color: #fff !important;
		}
		.card-header.bg-warning *,
		.card.bg-warning .card-body *,
		.card-body.bg-warning * {
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
</head>
<body>
	<!-- Loader -->
	<div class="loader-wrapper">
		<div class="loader-index">
			<span></span>
		</div>
		<svg>
			<defs></defs>
			<filter id="goo">
				<fegaussianblur in="SourceGraphic" stddeviation="11" result="blur"></fegaussianblur>
				<fecolormatrix in="blur" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 19 -9" result="goo"></fecolormatrix>
			</filter>
		</svg>
	</div>

	<!-- Tap on Top -->
	<div class="tap-top">
		<i data-feather="chevrons-up"></i>
	</div>

	<!-- Page Wrapper -->
	<div class="page-wrapper compact-wrapper" id="pageWrapper">
		<!-- Page Header -->
		@include('components.portal-header')

		<!-- Page Body -->
		<div class="page-body-wrapper">
			<!-- Page Sidebar -->
			@include('components.portal-sidebar')

			<!-- Page Body -->
			<div class="page-body">
				<!-- Breadcrumb -->
				@if(!empty($breadcrumbs) || isset($pageTitle))
				<div class="container-fluid">
					<div class="page-title">
						<div class="row">
							<div class="col-6">
								<h3>@yield('page-title', $isParentPortal ? 'Parent Dashboard' : 'Student Dashboard')</h3>
							</div>
							<div class="col-6">
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
				@endif

				<!-- Container-fluid starts -->
				<div class="container-fluid">
					@if(session('success'))
						<div class="alert alert-success alert-dismissible fade show" role="alert">
							<i class="fa fa-check-circle me-2"></i>{{ session('success') }}
							<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
						</div>
					@endif
					@if(session('error'))
						<div class="alert alert-danger alert-dismissible fade show" role="alert">
							<i class="fa fa-exclamation-circle me-2"></i>{{ session('error') }}
							<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
						</div>
					@endif
					@yield('content')
				</div>
				<!-- Container-fluid Ends -->
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
	<script src="{{ asset('assets/js/scrollbar/simplebar.js') }}"></script>
	<script src="{{ asset('assets/js/scrollbar/custom.js') }}"></script>
	<!-- Sidebar jQuery -->
	<script src="{{ asset('assets/js/config.js') }}"></script>
	<!-- Sidebar JS -->
	<script src="{{ asset('assets/js/sidebar-menu.js') }}"></script>
	<!-- SweetAlert2 JS -->
	<script src="{{ asset('assets/js/sweet-alert/sweetalert.min.js') }}"></script>

	@stack('scripts')

	@include('components.password-toggle')

	<!-- Theme Customizer -->
	<script src="{{ asset('assets/js/script.js') }}"></script>

	<script>
		// Initialize Feather Icons
		if (typeof feather !== 'undefined') {
			feather.replace();
		}

		// CSRF Token Setup for AJAX
		jQuery.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
			}
		});

		jQuery(document).ready(function() {
			// Highlight active sidebar menu based on current URL
			(function() {
				var currentUrl = window.location.href.split('?')[0].split('#')[0].replace(/\/$/, '');

				jQuery('.sidebar-submenu a').each(function() {
					var linkUrl = jQuery(this).attr('href');
					if (!linkUrl || linkUrl === '#') return;
					linkUrl = linkUrl.split('?')[0].split('#')[0].replace(/\/$/, '');
					if (currentUrl === linkUrl || currentUrl.indexOf(linkUrl) === 0) {
						jQuery(this).addClass('active');
						var submenu = jQuery(this).closest('.sidebar-submenu');
						submenu.slideDown(0);
						var parentTitle = submenu.prev('.sidebar-title');
						parentTitle.addClass('active');
						parentTitle.find('.according-menu i').removeClass('fa-angle-right').addClass('fa-angle-down');
					}
				});

				jQuery('.sidebar-link.link-nav').each(function() {
					var linkUrl = jQuery(this).attr('href');
					if (!linkUrl || linkUrl === '#') return;
					linkUrl = linkUrl.split('?')[0].split('#')[0].replace(/\/$/, '');
					if (currentUrl === linkUrl || currentUrl.indexOf(linkUrl) === 0) {
						jQuery(this).addClass('active');
					}
				});

				// Scroll sidebar to active menu item
				setTimeout(function() {
					var activeItem = jQuery('.sidebar-submenu a.active, .sidebar-link.link-nav.active').first();
					if (activeItem.length) {
						var sidebarWrapper = jQuery('.sidebar-wrapper, .custom-scrollbar');
						if (sidebarWrapper.length) {
							var itemTop = activeItem.offset().top - sidebarWrapper.offset().top + sidebarWrapper.scrollTop();
							var sidebarHeight = sidebarWrapper.height();
							sidebarWrapper.scrollTop(itemTop - (sidebarHeight / 3));
						}
					}
				}, 100);
			})();
		});
	</script>
	@auth
		@include('components.notification-scripts')
	@endauth
</body>
</html>
