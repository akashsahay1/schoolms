@extends('layouts.app')

@php
	// Use provided module name or automatically determine from route
	$moduleTitle = $module ?? (function() {
		$routeName = request()->route()->getName();
		$routeParts = explode('.', str_replace('admin.', '', $routeName));
		return ucwords(str_replace(['-', '_'], ' ', implode(' - ', $routeParts)));
	})();
	$moduleDescription = $description ?? 'This feature is currently under development and will be available soon.';
@endphp

@section('title', $moduleTitle)

@section('page-title', $moduleTitle)

@section('breadcrumb')
	<li class="breadcrumb-item active">{{ $moduleTitle }}</li>
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
					<h3 class="text-muted mb-3">{{ $moduleTitle }}</h3>
					<p class="text-muted mb-4">{{ $moduleDescription }}</p>
					<div class="alert alert-info d-inline-block">
						<i class="fas fa-info-circle me-2"></i>
						This module will be available in the next phase of development.
					</div>
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
