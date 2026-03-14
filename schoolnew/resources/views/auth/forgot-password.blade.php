@extends('layouts.auth')

@section('title', 'Forgot Password')

@push('styles')
<style>
.login-success-box {
	background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
	border: 1px solid #86efac;
	border-left: 4px solid #22c55e;
	border-radius: 8px;
	padding: 16px 20px;
	margin-bottom: 20px;
	color: #166534;
	font-size: 14px;
	position: relative;
}
.login-success-box .btn-close {
	position: absolute;
	top: 12px;
	right: 12px;
	font-size: 10px;
	opacity: 0.5;
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
			@php $__logo = \App\Models\Setting::get('school_logo'); @endphp
			<a class="logo" href="{{ url('/') }}">
				@if($__logo)
					<img class="img-fluid for-light" src="{{ asset('storage/' . $__logo) }}" alt="School Management">
					<img class="img-fluid for-dark" src="{{ asset('storage/' . $__logo) }}" alt="School Management">
				@else
					<h4 class="mb-0" style="font-weight: 600;">{{ \App\Models\Setting::get('school_name', config('app.name')) }}</h4>
				@endif
			</a>
		</div>
		<div class="login-main">
			<form class="theme-form" method="POST" action="{{ route('password.email') }}">
				@csrf
				<h4>Forgot Password?</h4>
				<p>Enter your email to receive a password reset link</p>

				<!-- Session Status -->
				@if (session('status'))
					<div class="login-success-box">
						{{ session('status') }}
						<button type="button" class="btn-close" aria-label="Close" onclick="this.parentElement.style.display='none'"></button>
					</div>
				@endif

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
					<input class="form-control @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="">
				</div>

				<div class="form-group mb-0">
					<div class="text-end mt-3">
						<button class="btn btn-primary btn-block w-100" type="submit">Send Password Reset Link</button>
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
