@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
<div class="col-xl-7 order-1">
	<img class="bg-img-cover bg-center" src="{{ asset('assets/images/login/1.jpg') }}" alt="login background">
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
				<form class="theme-form" method="POST" action="{{ route('password.update') }}">
					@csrf
					<input type="hidden" name="token" value="{{ $request->route('token') }}">

					<h2 class="text-center">Reset Password</h2>
					<p class="text-center">Enter your new password below</p>

					<!-- Validation Errors -->
					@if ($errors->any())
						<div class="alert alert-danger alert-dismissible fade show" role="alert">
							<ul class="mb-0">
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
						</div>
					@endif

					<div class="form-group">
						<label class="col-form-label">Email Address</label>
						<input class="form-control @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email', $request->email) }}" required autocomplete="email" placeholder="Enter your email address">
						@error('email')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>

					<div class="form-group">
						<label class="col-form-label">New Password</label>
						<div class="form-input position-relative">
							<input class="form-control @error('password') is-invalid @enderror" type="password" name="password" required autocomplete="new-password" placeholder="Enter new password">
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
							<input class="form-control @error('password_confirmation') is-invalid @enderror" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm new password">
							@error('password_confirmation')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
							<div class="show-hide">
								<span class="show"></span>
							</div>
						</div>
					</div>

					<div class="form-group mb-0">
						<div class="text-end mt-3">
							<button class="btn btn-primary btn-block w-100" type="submit">
								Reset Password
							</button>
						</div>
					</div>

					<p class="mt-4 mb-0 text-center">
						Remember your password?
						<a class="ms-2" href="{{ route('login') }}">Back to Login</a>
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
			var input = jQuery(this).closest('.form-input').find('input');
			if (jQuery(this).hasClass("show")) {
				input.attr("type", "text");
				jQuery(this).removeClass("show");
			} else {
				input.attr("type", "password");
				jQuery(this).addClass("show");
			}
		});
	});
</script>
@endpush
