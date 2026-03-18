@extends('layouts.website')

@section('title', $page->title)

@section('meta_description', $page->meta_description ?? $page->title)

@push('styles')
<style>
	.legal-page {
		padding: 80px 0;
		background: #f8f9fb;
	}
	.legal-card {
		background: #fff;
		border-radius: 16px;
		box-shadow: 0 2px 20px rgba(0,0,0,0.06);
		overflow: hidden;
	}
	.legal-card .legal-header {
		background: linear-gradient(135deg, #7366ff 0%, #a389ff 100%);
		padding: 5px 40px 50px;
		color: #fff;
		position: relative;
	}
	.legal-card .legal-header::after {
		content: '';
		position: absolute;
		bottom: -1px;
		left: 0;
		right: 0;
		height: 30px;
		background: #fff;
		border-radius: 16px 16px 0 0;
	}
	.legal-card .legal-header h1 {
		font-size: 2rem;
		font-weight: 700;
		margin-bottom: 8px;
	}
	.legal-card .legal-header .meta {
		font-size: 14px;
		opacity: 0.85;
	}
	.legal-card .legal-header .meta i {
		margin-right: 6px;
	}
	.legal-body {
		padding: 30px 50px 50px;
	}
	.legal-body h1, .legal-body h2, .legal-body h3, .legal-body h4, .legal-body h5, .legal-body h6 {
		color: #2c323f;
		font-weight: 600;
		margin-top: 30px;
		margin-bottom: 15px;
	}
	.legal-body h1 { font-size: 1.8rem; }
	.legal-body h2 { font-size: 1.5rem; }
	.legal-body h3 { font-size: 1.25rem; }
	.legal-body p {
		color: #555;
		line-height: 1.9;
		margin-bottom: 16px;
		font-size: 15px;
	}
	.legal-body ul, .legal-body ol {
		color: #555;
		line-height: 1.9;
		margin-bottom: 16px;
		padding-left: 24px;
	}
	.legal-body ul li, .legal-body ol li {
		margin-bottom: 8px;
	}
	.legal-body a {
		color: #7366ff;
		text-decoration: underline;
	}
	.legal-body a:hover {
		color: #5a4fd4;
	}
	.legal-body blockquote {
		border-left: 4px solid #7366ff;
		padding: 16px 24px;
		background: #f5f4ff;
		border-radius: 0 8px 8px 0;
		margin: 20px 0;
		color: #555;
		font-style: italic;
	}
	.legal-body table {
		width: 100%;
		border-collapse: collapse;
		margin: 20px 0;
	}
	.legal-body table th, .legal-body table td {
		border: 1px solid #e9ecef;
		padding: 12px 16px;
		text-align: left;
	}
	.legal-body table th {
		background: #f5f4ff;
		font-weight: 600;
		color: #2c323f;
	}
	.legal-body img {
		max-width: 100%;
		height: auto;
		border-radius: 8px;
	}
	.legal-body hr {
		border: none;
		border-top: 2px solid #eef0f6;
		margin: 30px 0;
	}
	.legal-sidebar {
		position: sticky;
		top: 100px;
	}
	.legal-sidebar .nav-link {
		color: #555;
		padding: 10px 16px;
		border-radius: 8px;
		font-size: 14px;
		transition: all 0.2s;
	}
	.legal-sidebar .nav-link:hover, .legal-sidebar .nav-link.active {
		background: #f5f4ff;
		color: #7366ff;
	}
	.legal-sidebar .nav-link i {
		margin-right: 8px;
		font-size: 16px;
	}
	@media (max-width: 991px) {
		.legal-card .legal-header { padding: 30px 24px; }
		.legal-body { padding: 20px 24px 30px; }
		.legal-card .legal-header h1 { font-size: 1.5rem; }
	}
</style>
@endpush

@section('content')
<!-- Page Banner -->
<section class="page-banner" @if($page) style="{{ $page->banner_image ? 'background-image: url(' . asset('storage/' . $page->banner_image) . ');' : '' }}{{ $page->banner_color ? '--banner-color: ' . $page->banner_color . ';' : '' }}" @endif>
	<div class="container">
		<h1>{{ $page->title }}</h1>
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="{{ route('website.home') }}">Home</a></li>
				<li class="breadcrumb-item active">{{ $page->title }}</li>
			</ol>
		</nav>
	</div>
</section>

<!-- Page Content -->
<section class="legal-page">
	<div class="container">
		<div class="row">
			<!-- Main Content -->
			<div class="col-lg-9">
				<div class="legal-card">
					<div class="legal-header">
						<h1>{{ $page->title }}</h1>
					</div>
					<div class="legal-body">
						{!! $page->content !!}
					</div>
				</div>
			</div>

			<!-- Sidebar -->
			<div class="col-lg-3 mt-4 mt-lg-0">
				<div class="legal-sidebar">
					<div class="card border-0 shadow-sm" style="border-radius: 12px;">
						<div class="card-body p-4">
							<h6 class="fw-bold mb-3" style="color: #2c323f;">Legal Pages</h6>
							<nav class="nav flex-column">
								@php
									$legalPages = [
										'privacy-policy' => ['title' => 'Privacy Policy', 'icon' => 'shield'],
										'cookies-policy' => ['title' => 'Cookies Policy', 'icon' => 'info'],
										'terms-conditions' => ['title' => 'Terms & Conditions', 'icon' => 'file-text'],
										'refund-policy' => ['title' => 'Refund Policy', 'icon' => 'refresh-cw'],
									];
								@endphp
								@foreach($legalPages as $slug => $info)
									<a href="{{ route('website.page', $slug) }}" class="nav-link {{ $page->slug === $slug ? 'active' : '' }}">
										<i data-feather="{{ $info['icon'] }}" style="width: 16px; height: 16px;"></i> {{ $info['title'] }}
									</a>
								@endforeach
							</nav>
						</div>
					</div>

					<div class="card border-0 shadow-sm mt-3" style="border-radius: 12px;">
						<div class="card-body p-4 text-center">
							<i data-feather="help-circle" style="width: 40px; height: 40px; color: #7366ff;" class="mb-3"></i>
							<h6 class="fw-bold">Have Questions?</h6>
							<p class="text-muted small mb-3">If you have any questions about our policies, feel free to contact us.</p>
							<a href="{{ route('website.contact') }}" class="btn btn-primary btn-sm">Contact Us</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
	if (typeof feather !== 'undefined') feather.replace();
});
</script>
@endpush
