<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="{{ config('app.name') }} - Staff Portal">
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<link rel="icon" href="{{ asset('assets/images/favicon.png') }}" type="image/x-icon">
	<title>@yield('title', 'Staff Portal') - {{ config('app.name') }}</title>

	<!-- Google Fonts -->
	<link href="https://fonts.googleapis.com/css?family=Rubik:400,400i,500,500i,700,700i&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900&display=swap" rel="stylesheet">

	<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/fontawesome.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/icofont.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/themify.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/feather-icon.css') }}">
	@stack('styles')
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/bootstrap.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}">
	<link id="color" rel="stylesheet" href="{{ asset('assets/css/color-1.css') }}" media="screen">
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive.css') }}">

	<style>
		.quick-action-card {
			transition: transform 0.2s, box-shadow 0.2s;
			cursor: pointer;
			text-decoration: none !important;
		}
		.quick-action-card:hover {
			transform: translateY(-5px);
			box-shadow: 0 8px 25px rgba(0,0,0,0.15);
		}
		.quick-action-icon {
			width: 60px;
			height: 60px;
			display: flex;
			align-items: center;
			justify-content: center;
			border-radius: 12px;
			margin-bottom: 15px;
		}
		.welcome-card {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			border-radius: 15px;
		}
		.stat-card {
			border-radius: 12px;
			border: none;
			box-shadow: 0 2px 10px rgba(0,0,0,0.08);
		}
		.help-tip {
			background: #f8f9fa;
			border-left: 4px solid #7366ff;
			padding: 15px;
			border-radius: 0 8px 8px 0;
			margin-bottom: 20px;
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
		<!-- Header -->
		@include('teacher.components.header')

		<!-- Page Body -->
		<div class="page-body-wrapper">
			<!-- Sidebar -->
			@include('teacher.components.sidebar')

			<!-- Page Body -->
			<div class="page-body">
				@if(!empty($breadcrumbs) || isset($pageTitle))
				<div class="container-fluid">
					<div class="page-title">
						<div class="row">
							<div class="col-6">
								<h3>@yield('page-title', 'Staff Dashboard')</h3>
							</div>
							<div class="col-6">
								<ol class="breadcrumb">
									<li class="breadcrumb-item">
										<a href="{{ route('teacher.dashboard') }}">
											<svg class="stroke-icon"><use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use></svg>
										</a>
									</li>
									@yield('breadcrumb')
								</ol>
							</div>
						</div>
					</div>
				</div>
				@endif

				<div class="container-fluid">
					<!-- Flash Messages -->
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
			</div>

			@include('components.footer')
		</div>
	</div>

	<!-- Scripts -->
	<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
	<script src="{{ asset('assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
	<script src="{{ asset('assets/js/icons/feather-icon/feather.min.js') }}"></script>
	<script src="{{ asset('assets/js/icons/feather-icon/feather-icon.js') }}"></script>
	<script src="{{ asset('assets/js/scrollbar/simplebar.js') }}"></script>
	<script src="{{ asset('assets/js/scrollbar/custom.js') }}"></script>
	<script src="{{ asset('assets/js/config.js') }}"></script>
	<script src="{{ asset('assets/js/sidebar-menu.js') }}"></script>
	@stack('scripts')
	<script src="{{ asset('assets/js/script.js') }}"></script>
	<script>
		if (typeof feather !== 'undefined') { feather.replace(); }
		jQuery.ajaxSetup({ headers: { 'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content') } });
	</script>
</body>
</html>
