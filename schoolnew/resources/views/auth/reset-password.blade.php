@extends('layouts.auth')

@section('title', 'Reset Password')

@push('styles')
<style>
.show-hide {
	cursor: pointer;
}
.show-hide i {
	color: #999;
	font-size: 14px;
}
.login-error-box {
	background: linear-gradient(135deg, #fff5f5 0%, #fee2e2 100%);
	border: 1px solid #fca5a5;
	border-left: 4px solid #ef4444;
	border-radius: 8px;
	padding: 16px 20px;
	margin-bottom: 20px;
	position: relative;
}
.login-error-box .error-icon {
	width: 22px;
	height: 22px;
	background: #ef4444;
	border-radius: 50%;
	display: inline-flex;
	align-items: center;
	justify-content: center;
	color: #fff;
	font-size: 13px;
	font-weight: 700;
	flex-shrink: 0;
}
.login-error-box .error-title {
	font-weight: 600;
	color: #991b1b;
	font-size: 14px;
	margin: 0;
}
.login-error-box .error-text {
	color: #b91c1c;
	font-size: 13px;
	margin: 6px 0 0 0;
	list-style: none;
	padding: 0;
}
.login-error-box .btn-close {
	position: absolute;
	top: 12px;
	right: 12px;
	font-size: 10px;
	opacity: 0.5;
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
			<form class="theme-form" method="POST" action="{{ route('password.update') }}">
				@csrf
				<input type="hidden" name="token" value="{{ $request->route('token') }}">

				<h4>Reset Password</h4>
				<p>Enter your new password below</p>

				<!-- Validation Errors -->
				@if ($errors->any())
					<div class="login-error-box">
						<button type="button" class="btn-close" aria-label="Close" onclick="this.parentElement.style.display='none'"></button>
						<div class="d-flex align-items-center gap-2">
							<span class="error-icon">!</span>
							<p class="error-title">Error</p>
						</div>
						<ul class="error-text">
							@foreach ($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
				@endif

				<div class="form-group">
					<label class="col-form-label">Email Address</label>
					<input class="form-control @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email', $request->email) }}" required autocomplete="email" placeholder="">
				</div>

				<div class="form-group">
					<label class="col-form-label">New Password</label>
					<div class="form-input position-relative">
						<input class="form-control @error('password') is-invalid @enderror" type="password" name="password" required autocomplete="new-password" placeholder="">
						<div class="show-hide">
							<i class="fa fa-eye"></i>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label class="col-form-label">Confirm Password</label>
					<div class="form-input position-relative">
						<input class="form-control @error('password_confirmation') is-invalid @enderror" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="">
						<div class="show-hide">
							<i class="fa fa-eye"></i>
						</div>
					</div>
				</div>

				<div class="form-group mb-0">
					<div class="text-end mt-3">
						<button class="btn btn-primary btn-block w-100" type="submit">Reset Password</button>
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
@endsection

@push('scripts')
<script>
	jQuery(document).ready(function() {
		jQuery(".show-hide").click(function() {
			var input = jQuery(this).closest('.form-input').find('input');
			var icon = jQuery(this).find("i");
			if (input.attr("type") === "password") {
				input.attr("type", "text");
				icon.removeClass("fa-eye").addClass("fa-eye-slash");
			} else {
				input.attr("type", "password");
				icon.removeClass("fa-eye-slash").addClass("fa-eye");
			}
		});
	});
</script>
@endpush
