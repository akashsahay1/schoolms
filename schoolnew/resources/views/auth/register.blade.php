@extends('layouts.auth')

@section('title', 'Register')

@section('content')
<div class="col-xl-7 order-1">
	<img class="bg-img-cover bg-center" src="{{ asset('assets/images/login/2.jpg') }}" alt="register background">
</div>
<div class="col-xl-5 p-0">
	<div class="login-card login-dark">
		<div>
			<div>
				<a class="logo text-center" href="{{ url('/') }}">
					<img class="img-fluid for-light" src="{{ asset('assets/images/logo/logo.png') }}" alt="School Management">
					<img class="img-fluid for-dark" src="{{ asset('assets/images/logo/logo_dark.png') }}" alt="School Management">
				</a>
			</div>
			<div class="login-main">
				<form class="theme-form" method="POST" action="{{ route('register') }}">
					@csrf
					<h2 class="text-center">Create your account</h2>
					<p class="text-center">Enter your personal details to create account</p>

					<!-- Validation Errors -->
					@if ($errors->any())
						<div class="alert alert-danger alert-dismissible fade show" role="alert">
							<strong>Whoops!</strong> There were some problems with your input.
							<ul class="mb-0 mt-2">
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
						</div>
					@endif

					<div class="form-group">
						<label class="col-form-label">Full Name</label>
						<input class="form-control @error('name') is-invalid @enderror" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Enter your full name">
						@error('name')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>

					<div class="form-group">
						<label class="col-form-label">Email Address</label>
						<input class="form-control @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="your@email.com">
						@error('email')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>

					<div class="form-group">
						<label class="col-form-label">Password</label>
						<div class="form-input position-relative">
							<input class="form-control @error('password') is-invalid @enderror" type="password" name="password" required autocomplete="new-password" placeholder="*********">
							@error('password')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
							<div class="show-hide">
								<span class="show"></span>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-form-label">Confirm Password</label>
						<div class="form-input position-relative">
							<input class="form-control" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="*********">
						</div>
					</div>

					<div class="form-group mb-0 checkbox-checked">
						<div class="form-check checkbox-solid-info">
							<input class="form-check-input" id="terms" type="checkbox" required>
							<label class="form-check-label" for="terms">
								I agree to all <a href="#" class="ms-2">Terms & Conditions</a>
							</label>
						</div>
						<div class="text-end mt-3">
							<button class="btn btn-primary btn-block w-100" type="submit">Create Account</button>
						</div>
					</div>

					<div class="login-social-title">
						<h6>Or Sign up with</h6>
					</div>
					<div class="form-group">
						<ul class="login-social">
							<li><a href="#" target="_blank"><i class="icon-linkedin"></i></a></li>
							<li><a href="#" target="_blank"><i class="icon-twitter"></i></a></li>
							<li><a href="#" target="_blank"><i class="icon-facebook"></i></a></li>
							<li><a href="#" target="_blank"><i class="icon-instagram"></i></a></li>
						</ul>
					</div>

					<p class="mt-4 mb-0 text-center">
						Already have an account?
						<a class="ms-2" href="{{ route('login') }}">Sign in</a>
					</p>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
	// Show/Hide password toggle
	jQuery(document).ready(function() {
		jQuery(".show-hide").show();
		jQuery(".show-hide span").addClass("show");

		jQuery(".show-hide span").click(function() {
			if (jQuery(this).hasClass("show")) {
				jQuery(this).closest('.form-input').find('input').attr("type", "text");
				jQuery(this).removeClass("show");
			} else {
				jQuery(this).closest('.form-input').find('input').attr("type", "password");
				jQuery(this).addClass("show");
			}
		});
	});
</script>
@endpush
