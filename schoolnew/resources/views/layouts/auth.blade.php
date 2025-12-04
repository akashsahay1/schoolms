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

	<title>@yield('title', 'Login') - {{ config('app.name') }}</title>

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

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/bootstrap.css') }}">
	<!-- App CSS -->
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}">
	<link id="color" rel="stylesheet" href="{{ asset('assets/css/color-1.css') }}" media="screen">
	<!-- Responsive CSS -->
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive.css') }}">

	@stack('styles')
</head>
<body>
	<!-- Login Page Start -->
	<div class="container-fluid p-0">
		<div class="row m-0">
			<div class="col-12 p-0">
				@yield('content')
			</div>
		</div>
	</div>

	<!-- jQuery -->
	<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
	<!-- Bootstrap JS -->
	<script src="{{ asset('assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
	<!-- Feather Icon JS -->
	<script src="{{ asset('assets/js/icons/feather-icon/feather.min.js') }}"></script>
	<script src="{{ asset('assets/js/icons/feather-icon/feather-icon.js') }}"></script>
	<!-- Config JS -->
	<script src="{{ asset('assets/js/config.js') }}"></script>
	<!-- Theme JS -->
	<script src="{{ asset('assets/js/script.js') }}"></script>

	@stack('scripts')

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
