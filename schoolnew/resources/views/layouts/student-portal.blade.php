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

	<title>@yield('title', 'Student Portal') - {{ config('app.name') }}</title>

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
	@stack('styles')

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/bootstrap.css') }}">
	<!-- App CSS -->
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}">
	<link id="color" rel="stylesheet" href="{{ asset('assets/css/color-1.css') }}" media="screen">
	<!-- Responsive CSS -->
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive.css') }}">
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
								<h3>@yield('page-title', 'Student Dashboard')</h3>
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

	@stack('scripts')

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
	</script>
</body>
</html>
