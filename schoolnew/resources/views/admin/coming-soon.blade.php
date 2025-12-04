@extends('layouts.app')

@php
	// Automatically determine page title from route name
	$routeName = request()->route()->getName();
	$routeParts = explode('.', str_replace('admin.', '', $routeName));
	$pageTitle = ucwords(str_replace(['-', '_'], ' ', implode(' - ', $routeParts)));
@endphp

@section('title', $pageTitle)

@section('page-title', $pageTitle)

@section('breadcrumb')
	<li class="breadcrumb-item active">{{ $pageTitle }}</li>
@endsection

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body text-center py-5">
					<div class="mb-4">
						<svg class="stroke-icon" style="width: 100px; height: 100px; opacity: 0.5;">
							<use href="{{ asset('assets/svg/icon-sprite.svg#stroke-board') }}"></use>
						</svg>
					</div>
					<h3 class="text-muted mb-3">{{ $pageTitle }} - Coming Soon</h3>
					<p class="text-muted mb-4">This feature is currently under development and will be available soon.</p>
					<a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
						<i data-feather="arrow-left" class="me-2"></i>
						Back to Dashboard
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
