@extends('layouts.auth')

@section('title', 'Login')

@push('styles')
<style>
.show-hide {
	cursor: pointer;
}
.show-hide i {
	color: #999;
	font-size: 14px;
}
</style>
@endpush

@section('content')
<div class="login-card login-dark">
	<div>
		<div>
			<a class="logo" href="{{ url('/') }}">
				<img class="img-fluid for-light" src="{{ asset('assets/images/logo/logo.png') }}" alt="School Management">
				<img class="img-fluid for-dark" src="{{ asset('assets/images/logo/logo_dark.png') }}" alt="School Management">
			</a>
		</div>
		<div class="login-main">
			<form class="theme-form" method="POST" action="{{ route('login') }}">
				@csrf
				<h4>Sign in to account</h4>
				<p>Enter your email & password to login</p>

				<!-- Session Status -->
				@if (session('status'))
					<div class="alert alert-success alert-dismissible fade show" role="alert">
						{{ session('status') }}
						<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					</div>
				@endif

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
					<label class="col-form-label">Email Address</label>
					<input class="form-control @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="">
					@error('email')
						<div class="invalid-feedback">{{ $message }}</div>
					@enderror
				</div>

				<div class="form-group">
					<label class="col-form-label">Password</label>
					<div class="form-input position-relative">
						<input class="form-control @error('password') is-invalid @enderror" type="password" name="password" required placeholder="">
						@error('password')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
						<div class="show-hide">
							<i class="fa fa-eye"></i>
						</div>
					</div>
				</div>

				<div class="form-group mb-0">
					<div class="checkbox p-0">
						<input class="checkbox_animated" id="remember_me" type="checkbox" name="remember">
						<label class="text-muted" for="remember_me">Remember password</label>
					</div>
					<div class="text-end mt-3">
						<button class="btn btn-primary btn-block w-100" type="submit">Sign in</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
	jQuery(document).ready(function() {
		jQuery(".show-hide").click(function() {
			var passwordField = jQuery("input[name='password']");
			var icon = jQuery(this).find("i");
			if (passwordField.attr("type") === "password") {
				passwordField.attr("type", "text");
				icon.removeClass("fa-eye").addClass("fa-eye-slash");
			} else {
				passwordField.attr("type", "password");
				icon.removeClass("fa-eye-slash").addClass("fa-eye");
			}
		});
	});
</script>
@endpush
