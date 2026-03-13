<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="{{ config('app.name') }} - School Management System">
	<meta name="author" content="School Management System">
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<link rel="icon" href="{{ asset('assets/images/favicon.png') }}" type="image/x-icon">
	<link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" type="image/x-icon">

	<title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>

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
	
	<!-- Custom Styles -->
	<style>
		/* Logo size fix */
		.logo-custom {
			max-height: 45px !important;
			max-width: 200px !important;
			object-fit: contain;
		}
		
		/* Sidebar spacing improvements */
		.sidebar-main-title {
			margin-bottom: 5px !important;
		}
		
		.sidebar-list:not(:first-child) {
			margin-top: 2px;
		}
		
		/* Better logo wrapper spacing */
		.logo-wrapper {
			padding: 15px 20px !important;
			margin-bottom: 10px;
		}
		
		/* Ensure proper spacing between sections */
		.sidebar-main-title + .sidebar-list {
			margin-top: 8px !important;
		}

		/* Profile dropdown fix */
		.profile-dropdown.onhover-show-div {
			padding: 8px 0 !important;
			width: 220px !important;
			border-radius: 8px !important;
			box-shadow: 0 4px 20px rgba(0,0,0,0.12) !important;
			border: 1px solid #eee !important;
			overflow: visible !important;
		}
		.profile-dropdown.onhover-show-div li {
			padding: 0 !important;
			display: block !important;
			margin-top: 0 !important;
		}
		.profile-dropdown.onhover-show-div li + li {
			margin-top: 0 !important;
		}
		.profile-dropdown.onhover-show-div li a {
			display: flex !important;
			align-items: center !important;
			gap: 8px;
			text-decoration: none;
			color: #3f475a;
			padding: 8px 16px !important;
			transition: background 0.2s;
			font-size: 13px;
			text-transform: none !important;
			letter-spacing: 0 !important;
		}
		.profile-dropdown.onhover-show-div li a:hover {
			background: #f5f6fa;
		}
		.profile-dropdown.onhover-show-div li a svg,
		.profile-dropdown.onhover-show-div li a i {
			width: 16px !important;
			height: 16px !important;
			min-width: 16px !important;
			flex-shrink: 0 !important;
			stroke: #3f475a;
			margin-right: 0 !important;
		}
		.profile-dropdown.onhover-show-div li a span {
			color: #3f475a;
		}

		/* Page title and breadcrumb text fix */
		.page-title h3 {
			color: #2c323f !important;
		}
		.page-title .breadcrumb-item,
		.page-title .breadcrumb-item a,
		.page-title .breadcrumb-item.active {
			color: #2c323f !important;
		}
		.page-title .breadcrumb-item a svg {
			stroke: #2c323f !important;
		}

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
		/* White text on ALL colored backgrounds (cards, card-headers, card-bodies, theads) */
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
		/* Dark text on white/light backgrounds (overrides any parent white text rules) */
		.bg-white, .bg-white *,
		.bg-light, .bg-light * {
			color: #000 !important;
		}
		.card-header-right-icon .dropdown-toggle {
			border: none !important;
		}
		.card-header-right-icon .dropdown-toggle:hover {
			background-color: transparent !important;
			color: #89899B !important;
		}
		.bg-white svg, .bg-white [data-feather],
		.bg-light svg, .bg-light [data-feather] {
			stroke: #000 !important;
		}

		/* ============================================ */
		/* BG-OPACITY SUPPORT (not in this Bootstrap) */
		/* ============================================ */
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

		/* ============================================ */
		/* RESPONSIVE MENU FIXES */
		/* ============================================ */

		/* Sidebar toggle - only visible on tablet/phone */
		#sidebar-toggle-btn {
			align-items: center;
			cursor: pointer;
			padding: 8px;
		}

		/* Desktop: sidebar always open, never collapsed */
		@media (min-width: 992px) {
			.sidebar-wrapper {
				left: 0 !important;
				transform: none !important;
			}
			.sidebar-wrapper.close_icon {
				width: 280px !important;
			}
			.sidebar-wrapper.close_icon .sidebar-main .sidebar-links .sidebar-list .sidebar-link span,
			.sidebar-wrapper.close_icon .sidebar-main-title {
				display: block !important;
				opacity: 1 !important;
			}
			.sidebar-wrapper.close_icon .logo-wrapper {
				display: flex !important;
			}
			.sidebar-wrapper.close_icon .logo-icon-wrapper {
				display: none !important;
			}
			.sidebar-wrapper.close_icon ~ .page-body,
			.page-body {
				margin-left: 280px !important;
			}
			.sidebar-wrapper.close_icon .sidebar-main .sidebar-links .sidebar-list .sidebar-link.sidebar-title::after {
				display: block !important;
			}
			.sidebar-wrapper.close_icon .sidebar-main .sidebar-links .sidebar-list .sidebar-submenu {
				position: static !important;
				box-shadow: none !important;
				background: transparent !important;
			}
		}

		/* Tablet and phone responsive */
		@media (max-width: 991.98px) {
			/* Hide left header on small screens */
			.left-header {
				display: none !important;
			}

			/* Hide some nav items on very small screens */
			.nav-menus .fullscreen-body {
				display: none !important;
			}

			/* Profile name on small screens */
			.profile-media .flex-grow-1 {
				display: none;
			}

			/* Header wrapper adjustments */
			.header-wrapper {
				padding: 10px 15px !important;
			}

			/* Logo wrapper in header */
			.header-logo-wrapper {
				display: flex !important;
				align-items: center;
				gap: 10px;
			}

			.header-logo-wrapper .logo-wrapper {
				padding: 0 !important;
				margin: 0 !important;
			}

			.header-logo-wrapper .logo-wrapper img {
				max-height: 35px !important;
			}

			/* Logo in sidebar on mobile */
			.sidebar-wrapper .logo-wrapper {
				display: flex !important;
				align-items: center;
				justify-content: space-between;
				padding: 15px 20px !important;
				border-bottom: 1px solid #eee;
			}

			.sidebar-wrapper .logo-wrapper .back-btn {
				display: flex !important;
				align-items: center;
				justify-content: center;
				width: 30px;
				height: 30px;
				border-radius: 5px;
				background: #f5f5f5;
				cursor: pointer;
			}

			/* Sidebar scrollbar */
			.sidebar-main {
				height: calc(100vh - 80px);
				overflow-y: auto;
			}

			/* Page body adjustment */
			.page-body-wrapper .page-body {
				margin-left: 0 !important;
				padding: 15px !important;
			}

			/* Page title responsive */
			.page-title h3 {
				font-size: 18px !important;
			}

			.page-title .breadcrumb {
				justify-content: flex-start !important;
				margin-top: 10px;
			}
		}

		/* Very small screens */
		@media (max-width: 575.98px) {
			.nav-menus > li {
				padding: 0 5px !important;
			}

			.nav-menus .mode {
				display: none !important;
			}

			.page-title .row {
				flex-direction: column;
			}

			.page-title .col-sm-6:last-child {
				text-align: left !important;
			}

			/* Cards responsive */
			.card {
				margin-bottom: 15px;
			}

			.card-body {
				padding: 15px !important;
			}

			/* Tables responsive */
			.table-responsive {
				font-size: 13px;
			}

			/* Form controls */
			.form-control, .form-select {
				font-size: 14px;
			}
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
		@include('components.header')

		<!-- Page Body -->
		<div class="page-body-wrapper">
			<!-- Page Sidebar -->
			@include('components.sidebar')

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
										<a href="{{ route('admin.dashboard') }}">
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
				<!-- Container-fluid starts -->
				@yield('content')
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

				// Check submenu links first
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

				// Check direct link-nav items (no submenu)
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

			function isMobileView() {
				return window.innerWidth < 992;
			}

			function initSidebarState() {
				if (isMobileView()) {
					jQuery('.sidebar-wrapper').addClass('close_icon');
					jQuery('.bg-overlay').remove();
				} else {
					// Desktop: always open
					jQuery('.sidebar-wrapper').removeClass('close_icon');
					jQuery('.page-header').removeClass('close_icon');
					jQuery('.bg-overlay').remove();
				}
			}

			initSidebarState();

			// Toggle sidebar (only on tablet/phone)
			jQuery(document).on('click', '#sidebar-toggle-btn', function(e) {
				e.preventDefault();
				e.stopPropagation();
				if (!isMobileView()) return;

				var sidebar = jQuery('.sidebar-wrapper');
				if (sidebar.hasClass('close_icon')) {
					sidebar.removeClass('close_icon');
					jQuery('body').append('<div class="bg-overlay active"></div>');
				} else {
					sidebar.addClass('close_icon');
					jQuery('.bg-overlay').remove();
				}
			});

			// Close sidebar when clicking on overlay
			jQuery(document).on('click', '.bg-overlay', function() {
				jQuery('.sidebar-wrapper').addClass('close_icon');
				jQuery(this).remove();
			});

			// Close sidebar when clicking back/close button in mobile
			jQuery(document).on('click', '.sidebar-wrapper .back-btn', function(e) {
				e.preventDefault();
				e.stopPropagation();
				jQuery('.sidebar-wrapper').addClass('close_icon');
				jQuery('.bg-overlay').remove();
			});

			// Close sidebar when clicking a direct link on mobile
			jQuery(document).on('click', '.sidebar-wrapper .sidebar-link.link-nav', function() {
				if (isMobileView()) {
					setTimeout(function() {
						jQuery('.sidebar-wrapper').addClass('close_icon');
						jQuery('.bg-overlay').remove();
					}, 100);
				}
			});

			// Handle window resize
			var resizeTimer;
			jQuery(window).on('resize', function() {
				clearTimeout(resizeTimer);
				resizeTimer = setTimeout(function() {
					initSidebarState();
				}, 250);
			});

			// Override sidebar-menu.js: prevent collapse on desktop
			jQuery(document).on('click', '.toggle-sidebar', function(e) {
				if (!isMobileView()) {
					e.preventDefault();
					e.stopImmediatePropagation();
					jQuery('.sidebar-wrapper').removeClass('close_icon');
					jQuery('.page-header').removeClass('close_icon');
				}
			});

			// Delete Confirmation Modal using SweetAlert2
			jQuery(document).on('click', '.delete-confirm', function(e) {
				e.preventDefault();
				var form = jQuery(this).closest('form');
				var itemName = jQuery(this).data('name') || 'this item';

				Swal.fire({
					title: 'Are you sure?',
					text: 'You are about to delete ' + itemName + '. This action cannot be undone!',
					icon: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#FC4438',
					cancelButtonColor: '#6c757d',
					confirmButtonText: 'Yes, delete it!',
					cancelButtonText: 'No, cancel',
					reverseButtons: true
				}).then(function(result) {
					if (result.isConfirmed) {
						form.submit();
					}
				});
			});
		});
	</script>
	@auth
		@include('components.notification-scripts')
	@endauth
</body>
</html>
