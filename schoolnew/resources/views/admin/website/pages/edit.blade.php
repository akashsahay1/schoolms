@extends('layouts.app')

@section('title', 'Edit - ' . $page->title)

@section('page-title', 'Edit Page')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.website.pages') }}">Pages</a></li>
	<li class="breadcrumb-item active">{{ $page->title }}</li>
@endsection

@push('styles')
<style>
/* Hero banner */
.page-hero {
	background: linear-gradient(135deg, #7366ff 0%, #a389ff 50%, #7366ff 100%);
	border-radius: 14px;
	padding: 24px 28px;
	color: #fff;
	position: relative;
	overflow: hidden;
	margin-bottom: 20px;
}
.page-hero::before {
	content: '';
	position: absolute;
	top: -50%;
	right: -20%;
	width: 300px;
	height: 300px;
	border-radius: 50%;
	background: rgba(255,255,255,0.08);
}
.page-hero * { position: relative; z-index: 2; }
.page-hero h4 { font-weight: 700; margin-bottom: 2px; }
.page-hero p { opacity: 0.85; margin-bottom: 0; font-size: 13px; }

/* Section cards (same as homepage-sections) */
.section-card {
	border-radius: 12px;
	border: 1px solid #eee;
	overflow: hidden;
	margin-bottom: 16px;
	transition: box-shadow 0.25s, border-color 0.25s;
	background: #fff;
}
.section-card:hover {
	box-shadow: 0 4px 20px rgba(0,0,0,0.06);
	border-color: #d0cef7;
}
.section-card .card-top {
	padding: 16px 20px;
	display: flex;
	justify-content: space-between;
	align-items: center;
	cursor: pointer;
	transition: background 0.2s;
	user-select: none;
}
.section-card .card-top:hover { background: #fafafe; }
.section-card .card-top .left { display: flex; align-items: center; gap: 14px; }
.section-card .card-top .num {
	width: 38px; height: 38px; border-radius: 10px; color: #fff;
	display: inline-flex; align-items: center; justify-content: center;
	font-weight: 800; font-size: 15px; flex-shrink: 0;
}
.section-card .card-top h6 { margin: 0; font-size: 15px; font-weight: 600; color: #2c323f; }
.section-card .card-top small { color: #999; font-size: 12px; display: block; margin-top: 1px; }
.section-card .card-top .chevron {
	width: 28px; height: 28px; border-radius: 50%; background: #f3f3f8;
	display: inline-flex; align-items: center; justify-content: center;
	transition: transform 0.3s, background 0.2s;
	color: #999;
}
.section-card .card-top:hover .chevron { background: #e8e6ff; color: #7366ff; }
.section-card .card-top.open .chevron { transform: rotate(180deg); background: #7366ff; color: #fff; }
.section-card .card-body-inner { display: none; padding: 0 20px 20px; }
.section-card .card-body-inner.open { display: block; }

/* Link cards */
.link-card {
	border-radius: 12px;
	border: 1px solid #eee;
	overflow: hidden;
	margin-bottom: 16px;
	transition: box-shadow 0.25s, border-color 0.25s;
	background: #fff;
}
.link-card:hover {
	box-shadow: 0 4px 20px rgba(0,0,0,0.06);
	border-color: #d0cef7;
}
.link-card a {
	padding: 16px 20px;
	display: flex;
	justify-content: space-between;
	align-items: center;
	text-decoration: none;
	color: inherit;
}
.link-card a .left { display: flex; align-items: center; gap: 14px; }
.link-card a .num {
	width: 38px; height: 38px; border-radius: 10px; background: #f3f3f8; color: #888;
	display: inline-flex; align-items: center; justify-content: center;
	font-weight: 800; font-size: 15px; flex-shrink: 0;
	transition: background 0.2s;
}
.link-card:hover a .num { background: #e8e6ff; color: #7366ff; }
.link-card a h6 { margin: 0; font-size: 15px; font-weight: 600; color: #2c323f; }
.link-card a small { color: #999; font-size: 12px; display: block; margin-top: 1px; }
.link-card a .go-icon {
	width: 28px; height: 28px; border-radius: 50%; background: #f3f3f8;
	display: inline-flex; align-items: center; justify-content: center;
	color: #bbb; transition: all 0.2s;
}
.link-card:hover a .go-icon { background: #7366ff; color: #fff; }

/* Custom section card */
.custom-section-card {
	border-radius: 12px;
	border: 1px solid #eee;
	overflow: hidden;
	margin-bottom: 16px;
	background: #fff;
	transition: box-shadow 0.25s;
}
.custom-section-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.06); }

/* Add section dashed */
.add-section-card {
	border: 2px dashed #d0cef7;
	border-radius: 12px;
	overflow: hidden;
	margin-bottom: 16px;
	background: #fafafe;
	transition: border-color 0.2s;
}
.add-section-card:hover { border-color: #7366ff; }
.add-section-card .toggle-header {
	padding: 16px 20px;
	cursor: pointer;
	display: flex;
	justify-content: space-between;
	align-items: center;
}
.add-section-card .toggle-header h6 { margin: 0; font-size: 14px; font-weight: 600; color: #7366ff; }

/* Divider label */
.divider-label {
	display: flex;
	align-items: center;
	gap: 10px;
	margin: 16px 0 10px;
	font-size: 11px;
	font-weight: 700;
	text-transform: uppercase;
	letter-spacing: 1px;
	color: #bbb;
}
.divider-label::after { content: ''; flex: 1; height: 1px; background: #eee; }

/* Preview body */
.preview-banner {
	background: url('{{ $page->banner_image ? asset("storage/" . $page->banner_image) : asset("assets/images/banner/4.jpg") }}') center/cover no-repeat;
	padding: 60px 30px 50px;
	color: #fff;
	text-align: center;
	position: relative;
	overflow: hidden;
	border-radius: 12px;
}
.preview-banner::before {
	content: '';
	position: absolute;
	inset: 0;
	background: {{ $page->banner_color ?? '#6065f2' }};
	opacity: 0.85;
}
.preview-banner * { position: relative; z-index: 2; }
.preview-banner h1 { font-size: 1.6rem; font-weight: 700; margin: 0 0 4px; }
.preview-banner .breadcrumb-preview { font-size: 12px; opacity: 0.8; }
.preview-body {
	background: #fff;
	padding: 30px;
	border-radius: 12px;
	box-shadow: 0 2px 16px rgba(0,0,0,0.05);
	min-height: 120px;
	font-size: 14px;
	line-height: 1.8;
	color: #555;
	margin-top: 16px;
}
.preview-body h1,.preview-body h2,.preview-body h3 { color: #2c323f; font-weight: 600; }
.preview-body p { margin-bottom: 12px; }
.preview-body img { max-width: 100%; border-radius: 8px; }

.empty-content { text-align: center; padding: 40px 20px; color: #bbb; }
.empty-content i { font-size: 36px; margin-bottom: 8px; display: block; }
</style>
@endpush

@section('content')
@if(session('success'))
	<div class="alert alert-success alert-dismissible fade show py-2 px-3" role="alert" style="font-size: 13px; border-radius: 8px;">
		<i class="icon-check me-1"></i> {{ session('success') }}
		<button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" style="padding: 10px;"></button>
	</div>
@endif
@if($errors->any())
	<div class="alert alert-danger alert-dismissible fade show py-2 px-3" role="alert" style="font-size: 13px; border-radius: 8px;">
		<ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
		<button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" style="padding: 10px;"></button>
	</div>
@endif

@php
	$isHomePage = $page->slug === 'home';
	$knownRoutes = ['home','about','academics','facilities','gallery','news','events','contact'];
	$viewUrl = in_array($page->slug, $knownRoutes)
		? route('website.' . ($page->slug === 'home' ? 'home' : $page->slug))
		: route('website.page', $page->slug);
@endphp

<!-- Hero Banner -->
<div class="page-hero">
	<div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
		<div>
			<h4>{{ $page->title }}</h4>
			<p>{{ $isHomePage ? 'Edit homepage sections — slider, about, stats, facilities & more' : 'Edit page content, banner, sections & SEO settings' }}</p>
		</div>
		<div class="d-flex gap-2">
			<a href="{{ route('admin.website.pages') }}" class="btn btn-outline-light btn-sm" style="border-radius: 8px;"><i class="icon-arrow-left me-1"></i> All Pages</a>
			<a href="{{ $viewUrl }}" target="_blank" class="btn btn-light btn-sm" style="font-weight: 600; border-radius: 8px;"><i class="icon-eye me-1"></i> View Live</a>
		</div>
	</div>
</div>

@if($isHomePage)
{{-- ==================== HOME PAGE ==================== --}}

<!-- 1. Hero Slider -->
<div class="link-card">
	<a href="{{ route('admin.website.sliders') }}">
		<div class="left">
			<span class="num" style="background: #6c757d; color: #fff;">1</span>
			<div><h6>Hero Slider</h6><small>Big banner images at the top</small></div>
		</div>
		<span class="go-icon"><i class="icon-arrow-right"></i></span>
	</a>
</div>

<!-- 2-4, 9 → Homepage Sections -->
<div class="link-card">
	<a href="{{ route('admin.website.homepage-sections') }}">
		<div class="left">
			<span class="num" style="background: #7366ff;">2-4</span>
			<div><h6>Why Choose Us / About / Statistics / CTA</h6><small>Edit text, images, counters & call-to-action</small></div>
		</div>
		<span class="go-icon"><i class="icon-arrow-right"></i></span>
	</a>
</div>

<!-- 5. Facilities -->
<div class="link-card">
	<a href="{{ route('admin.website.facilities') }}">
		<div class="left">
			<span class="num">5</span>
			<div><h6>Facilities</h6><small>School facility cards with icons</small></div>
		</div>
		<span class="go-icon"><i class="icon-arrow-right"></i></span>
	</a>
</div>

<!-- 6. Events & News -->
<div class="link-card">
	<div style="padding: 16px 20px; display: flex; justify-content: space-between; align-items: center;">
		<div class="d-flex align-items-center gap-3">
			<span style="width: 38px; height: 38px; border-radius: 10px; background: #f3f3f8; color: #888; display: inline-flex; align-items: center; justify-content: center; font-weight: 800; font-size: 15px;">6</span>
			<div>
				<h6 style="font-size: 15px; margin: 0; font-weight: 600; color: #2c323f;">Events & News</h6>
				<small style="color: #999; font-size: 12px;">Auto-pulled from Events & Notices</small>
			</div>
		</div>
		<div class="d-flex gap-2">
			<a href="{{ route('admin.events.index') }}" class="btn btn-outline-primary btn-sm" style="font-size: 11px; border-radius: 6px;">Events</a>
			<a href="{{ route('admin.notices.index') }}" class="btn btn-outline-primary btn-sm" style="font-size: 11px; border-radius: 6px;">Notices</a>
		</div>
	</div>
</div>

<!-- 7. Gallery -->
<div class="link-card">
	<a href="{{ route('admin.website.gallery') }}">
		<div class="left">
			<span class="num">7</span>
			<div><h6>Photo Gallery</h6><small>Photo grid layout</small></div>
		</div>
		<span class="go-icon"><i class="icon-arrow-right"></i></span>
	</a>
</div>

<!-- 8. Testimonials -->
<div class="link-card">
	<a href="{{ route('admin.website.testimonials') }}">
		<div class="left">
			<span class="num">8</span>
			<div><h6>Testimonials</h6><small>Parent & student reviews</small></div>
		</div>
		<span class="go-icon"><i class="icon-arrow-right"></i></span>
	</a>
</div>

<!-- SEO for Home -->
<div class="section-card">
	<div class="card-top" data-target="body-home-seo">
		<div class="left">
			<span class="num" style="background: #54BA4A;"><i class="icon-search" style="font-size: 14px; color: #fff;"></i></span>
			<div><h6>SEO & Browser Tab Settings</h6><small>Title shown in browser tab & Google results</small></div>
		</div>
		<span class="chevron"><i class="icon-angle-down"></i></span>
	</div>
	<div class="card-body-inner" id="body-home-seo">
		<form action="{{ route('admin.website.pages.update', $page) }}" method="POST">
			@csrf
			@method('PUT')
			<input type="hidden" name="is_active" value="1">
			<div class="row g-3">
				<div class="col-md-4">
					<label class="form-label fw-bold">Browser Tab Title</label>
					<input type="text" name="title" class="form-control" value="{{ $page->title }}">
				</div>
				<div class="col-md-4">
					<label class="form-label fw-bold">Meta Description</label>
					<input type="text" name="meta_description" class="form-control" value="{{ $page->meta_description }}" maxlength="160">
				</div>
				<div class="col-md-4">
					<label class="form-label fw-bold">Meta Keywords</label>
					<input type="text" name="meta_keywords" class="form-control" value="{{ $page->meta_keywords }}" placeholder="school, education">
				</div>
				<div class="col-12 text-end">
					<button type="submit" class="btn btn-primary btn-sm" style="border-radius: 8px;"><i class="icon-check me-1"></i> Save SEO</button>
				</div>
			</div>
		</form>
	</div>
</div>

@else
{{-- ==================== REGULAR PAGE ==================== --}}

<!-- Banner -->
<div class="section-card">
	<div class="card-top" data-target="body-banner">
		<div class="left">
			<span class="num" style="background: #7366ff;"><i class="icon-image" style="font-size: 14px; color: #fff;"></i></span>
			<div><h6>Page Banner</h6><small>Title, background image & overlay color</small></div>
		</div>
		<span class="chevron"><i class="icon-angle-down"></i></span>
	</div>
	<div class="card-body-inner" id="body-banner">
		<!-- Banner Preview -->
		<div class="preview-banner mb-3" id="previewBanner">
			<h1 id="previewTitle">{{ $page->title }}</h1>
			<div class="breadcrumb-preview">Home / <strong>{{ $page->title }}</strong></div>
		</div>

		<form action="{{ route('admin.website.pages.update', $page) }}" method="POST" enctype="multipart/form-data" id="bannerForm">
			@csrf
			@method('PUT')
			<input type="hidden" name="content" value="{{ $page->content }}">
			<input type="hidden" name="meta_description" value="{{ $page->meta_description }}">
			<input type="hidden" name="meta_keywords" value="{{ $page->meta_keywords }}">
			<input type="hidden" name="is_active" value="{{ $page->is_active ? '1' : '0' }}">
			<div class="row g-3">
				<div class="col-md-5">
					<label class="form-label fw-bold">Page Title</label>
					<input type="text" name="title" id="titleInput" class="form-control" value="{{ $page->title }}" required>
				</div>
				<div class="col-md-3">
					<label class="form-label fw-bold">Overlay Color</label>
					<div class="d-flex gap-2">
						<input type="color" name="banner_color" id="bannerColor" class="form-control form-control-color" value="{{ $page->banner_color ?? '#6065f2' }}" style="width: 50px; height: 38px;">
						<input type="text" id="bannerColorText" class="form-control form-control-sm" value="{{ $page->banner_color ?? '#6065f2' }}" maxlength="7" style="max-width: 90px; font-family: monospace;">
					</div>
				</div>
				<div class="col-md-4">
					<label class="form-label fw-bold">Banner Image</label>
					<input type="file" name="banner_image" class="form-control form-control-sm" accept="image/*" id="bannerFileInput">
					@if($page->banner_image)
						<div class="form-check mt-1"><input type="checkbox" name="remove_banner_image" value="1" class="form-check-input" id="removeBanner"><label class="form-check-label text-danger" for="removeBanner" style="font-size: 11px;">Remove image</label></div>
					@endif
				</div>
				<div class="col-12 text-end">
					<button type="submit" class="btn btn-primary btn-sm" style="border-radius: 8px;"><i class="icon-check me-1"></i> Save Banner</button>
				</div>
			</div>
		</form>
	</div>
</div>

<!-- Content -->
<div class="section-card">
	<div class="card-top" data-target="body-content">
		<div class="left">
			<span class="num" style="background: #54BA4A;"><i class="icon-pencil-alt" style="font-size: 14px; color: #fff;"></i></span>
			<div><h6>Page Content</h6><small>Main text, images, tables & formatting</small></div>
		</div>
		<span class="chevron"><i class="icon-angle-down"></i></span>
	</div>
	<div class="card-body-inner" id="body-content">
		<!-- Content Preview -->
		<div class="preview-body mb-3" id="previewContent">
			@if($page->content)
				{!! $page->content !!}
			@else
				<div class="empty-content">
					<i class="icon-note"></i>
					<p>No content yet. Use the editor below to add content.</p>
				</div>
			@endif
		</div>

		<form action="{{ route('admin.website.pages.update', $page) }}" method="POST" id="contentForm">
			@csrf
			@method('PUT')
			<input type="hidden" name="title" value="{{ $page->title }}">
			<input type="hidden" name="meta_description" value="{{ $page->meta_description }}">
			<input type="hidden" name="meta_keywords" value="{{ $page->meta_keywords }}">
			<input type="hidden" name="is_active" value="{{ $page->is_active ? '1' : '0' }}">
			<textarea name="content" id="pageEditor">{{ $page->content }}</textarea>
			<div class="text-end mt-3">
				<button type="submit" class="btn btn-primary btn-sm" style="border-radius: 8px;"><i class="icon-check me-1"></i> Save Content</button>
			</div>
		</form>
	</div>
</div>

<!-- Custom Sections -->
@foreach($sections ?? [] as $section)
<div class="custom-section-card" style="border-left: 4px solid {{ $section->bg_color && $section->bg_color !== '#ffffff' ? $section->bg_color : '#7366ff' }};">
	<!-- Preview -->
	<div class="p-3" style="background: {{ $section->bg_color && $section->bg_color !== '#ffffff' ? $section->bg_color . '10' : '#f8f9fc' }};">
		@if($section->layout === 'image-left' || $section->layout === 'image-right')
		<div class="row align-items-center g-3">
			@if($section->layout === 'image-left')
			<div class="col-md-5">
				@if($section->image)<img src="{{ asset('storage/' . $section->image) }}" class="w-100 rounded" style="max-height: 180px; object-fit: cover;">
				@else <div class="bg-light d-flex align-items-center justify-content-center rounded" style="height: 120px;"><i class="icon-image" style="font-size: 28px; color: #ccc;"></i></div>@endif
			</div>
			<div class="col-md-7">
				@if($section->title)<h5 class="mb-1">{{ $section->title }}</h5>@endif
				@if($section->content)<div style="font-size: 13px; color: #555;">{!! Str::limit(strip_tags($section->content), 150) !!}</div>@endif
			</div>
			@else
			<div class="col-md-7">
				@if($section->title)<h5 class="mb-1">{{ $section->title }}</h5>@endif
				@if($section->content)<div style="font-size: 13px; color: #555;">{!! Str::limit(strip_tags($section->content), 150) !!}</div>@endif
			</div>
			<div class="col-md-5">
				@if($section->image)<img src="{{ asset('storage/' . $section->image) }}" class="w-100 rounded" style="max-height: 180px; object-fit: cover;">
				@else <div class="bg-light d-flex align-items-center justify-content-center rounded" style="height: 120px;"><i class="icon-image" style="font-size: 28px; color: #ccc;"></i></div>@endif
			</div>
			@endif
		</div>
		@else
		<div class="{{ $section->layout === 'content-center' ? 'text-center' : '' }}">
			@if($section->title)<h5 class="mb-1">{{ $section->title }}</h5>@endif
			@if($section->content)<div style="font-size: 13px; color: #555;">{!! Str::limit(strip_tags($section->content), 250) !!}</div>@endif
		</div>
		@endif
	</div>
	<!-- Actions -->
	<div class="d-flex justify-content-between align-items-center px-3 py-2" style="border-top: 1px solid #eee;">
		<span class="badge badge-light-primary" style="font-size: 10px;">{{ \App\Models\WebsiteSection::LAYOUTS[$section->layout] ?? $section->layout }}</span>
		<div class="d-flex gap-2">
			<button type="button" class="btn btn-outline-primary btn-sm edit-section-btn" data-id="{{ $section->id }}" style="font-size: 11px; border-radius: 6px;"><i class="icon-pencil-alt me-1"></i> Edit</button>
			<form action="{{ route('admin.website.sections.destroy', $section) }}" method="POST" class="d-inline">
				@csrf @method('DELETE')
				<button type="button" class="btn btn-outline-danger btn-sm delete-section-btn" data-name="{{ $section->title ?? 'this section' }}" style="font-size: 11px; border-radius: 6px;"><i class="icon-trash me-1"></i> Delete</button>
			</form>
		</div>
	</div>
	<!-- Inline Edit -->
	<div class="d-none p-3" id="editForm-{{ $section->id }}" style="border-top: 2px solid #7366ff; background: #fafbff;">
		<form action="{{ route('admin.website.sections.update', $section) }}" method="POST" enctype="multipart/form-data">
			@csrf @method('PUT')
			<div class="row g-3">
				<div class="col-md-4"><label class="form-label fw-bold">Layout</label><select name="layout" class="form-select form-select-sm">@foreach(\App\Models\WebsiteSection::LAYOUTS as $k => $l)<option value="{{ $k }}" {{ $section->layout === $k ? 'selected' : '' }}>{{ $l }}</option>@endforeach</select></div>
				<div class="col-md-4"><label class="form-label fw-bold">Title</label><input type="text" name="title" class="form-control form-control-sm" value="{{ $section->title }}"></div>
				<div class="col-md-4"><label class="form-label fw-bold">Subtitle</label><input type="text" name="subtitle" class="form-control form-control-sm" value="{{ $section->subtitle }}"></div>
				<div class="col-md-8"><label class="form-label fw-bold">Content</label><textarea name="content" class="form-control form-control-sm" rows="3">{{ $section->content }}</textarea></div>
				<div class="col-md-4">
					<label class="form-label fw-bold">Image</label>
					@if($section->image)<div class="mb-1"><img src="{{ asset('storage/' . $section->image) }}" class="rounded" style="max-height: 50px;"></div><div class="form-check mb-1"><input type="checkbox" name="remove_image" value="1" class="form-check-input"><label class="form-check-label text-danger" style="font-size: 11px;">Remove</label></div>@endif
					<input type="file" name="image" class="form-control form-control-sm" accept="image/*">
				</div>
				<div class="col-md-4"><label class="form-label fw-bold">Button Text</label><input type="text" name="link_text" class="form-control form-control-sm" value="{{ $section->link_text }}"></div>
				<div class="col-md-4"><label class="form-label fw-bold">Button Link</label><input type="text" name="link" class="form-control form-control-sm" value="{{ $section->link }}"></div>
				<div class="col-md-4"><label class="form-label fw-bold">BG Color</label><input type="color" name="bg_color" class="form-control form-control-color" value="{{ $section->bg_color ?? '#ffffff' }}" style="width: 50px; height: 34px;"></div>
			</div>
			<div class="d-flex justify-content-end gap-2 mt-3">
				<button type="button" class="btn btn-secondary btn-sm cancel-edit-btn" data-id="{{ $section->id }}" style="border-radius: 6px;">Cancel</button>
				<button type="submit" class="btn btn-primary btn-sm" style="border-radius: 6px;"><i class="icon-check me-1"></i> Update</button>
			</div>
		</form>
	</div>
</div>
@endforeach

<!-- Add New Section -->
<div class="add-section-card">
	<div class="toggle-header" id="addSectionToggle">
		<h6><i class="icon-plus me-2"></i> Add New Section</h6>
		<i class="icon-angle-down" id="addSectionArrow" style="color: #7366ff;"></i>
	</div>
	<div class="d-none p-3 pt-0" id="addSectionForm">
		<form action="{{ route('admin.website.pages.sections.store', $page) }}" method="POST" enctype="multipart/form-data">
			@csrf
			<div class="row g-3">
				<div class="col-md-6"><label class="form-label fw-bold">Layout <span class="text-danger">*</span></label><select name="layout" class="form-select" required>@foreach($layouts as $k => $l)<option value="{{ $k }}">{{ $l }}</option>@endforeach</select></div>
				<div class="col-md-6"><label class="form-label fw-bold">Title</label><input type="text" name="title" class="form-control" placeholder="e.g. Our Mission"></div>
				<div class="col-12"><label class="form-label fw-bold">Subtitle</label><input type="text" name="subtitle" class="form-control" placeholder="Optional short description"></div>
				<div class="col-md-8"><label class="form-label fw-bold">Content</label><textarea name="content" class="form-control" rows="4" placeholder="Section content..."></textarea></div>
				<div class="col-md-4"><label class="form-label fw-bold">Image</label><input type="file" name="image" class="form-control" accept="image/*"><small class="text-muted">For image-left/right/full layouts</small></div>
				<div class="col-md-4"><label class="form-label fw-bold">Button Text</label><input type="text" name="link_text" class="form-control" placeholder="e.g. Read More"></div>
				<div class="col-md-4"><label class="form-label fw-bold">Button Link</label><input type="text" name="link" class="form-control" placeholder="/about or full URL"></div>
				<div class="col-md-4"><label class="form-label fw-bold">BG Color</label><input type="color" name="bg_color" class="form-control form-control-color" value="#ffffff" style="width: 50px; height: 38px;"></div>
			</div>
			<div class="text-end mt-3"><button type="submit" class="btn btn-success" style="border-radius: 8px;"><i class="icon-plus me-1"></i> Add Section</button></div>
		</form>
	</div>
</div>

<!-- SEO & Settings -->
<div class="section-card">
	<div class="card-top" data-target="body-seo">
		<div class="left">
			<span class="num" style="background: #FFAA05;"><i class="icon-search" style="font-size: 14px; color: #fff;"></i></span>
			<div><h6>SEO & Page Settings</h6><small>Search engine description, keywords & visibility</small></div>
		</div>
		<span class="chevron"><i class="icon-angle-down"></i></span>
	</div>
	<div class="card-body-inner" id="body-seo">
		<form action="{{ route('admin.website.pages.update', $page) }}" method="POST" id="seoForm">
			@csrf
			@method('PUT')
			<input type="hidden" name="title" value="{{ $page->title }}">
			<input type="hidden" name="content" value="{{ $page->content }}">
			<div class="row g-3">
				<div class="col-md-6">
					<label class="form-label fw-bold">Meta Description <small class="text-muted fw-normal">(max 160)</small></label>
					<textarea name="meta_description" class="form-control form-control-sm" rows="2" maxlength="160">{{ $page->meta_description }}</textarea>
				</div>
				<div class="col-md-6">
					<label class="form-label fw-bold">Meta Keywords</label>
					<input type="text" name="meta_keywords" class="form-control form-control-sm" value="{{ $page->meta_keywords }}" placeholder="school, education, policy">
				</div>
				<div class="col-md-6">
					<div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
						<span class="fw-bold">Page Visibility</span>
						<div class="form-check form-switch mb-0">
							<input type="checkbox" name="is_active" class="form-check-input" value="1" {{ $page->is_active ? 'checked' : '' }}>
							<label class="form-check-label fw-bold {{ $page->is_active ? 'text-success' : 'text-danger' }}">{{ $page->is_active ? 'Visible' : 'Hidden' }}</label>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="p-3 bg-light rounded">
						<div class="d-flex justify-content-between mb-1"><span class="text-muted" style="font-size: 12px;">URL</span><code style="font-size: 11px;">{{ url('/') }}/{{ $page->slug }}</code></div>
						<div class="d-flex justify-content-between"><span class="text-muted" style="font-size: 12px;">Updated</span><span style="font-size: 12px;">{{ $page->updated_at->format('d M Y, h:i A') }}</span></div>
					</div>
				</div>
				<div class="col-12 text-end">
					<button type="submit" class="btn btn-primary btn-sm" style="border-radius: 8px;"><i class="icon-check me-1"></i> Save SEO & Settings</button>
				</div>
			</div>
		</form>
	</div>
</div>

@endif

<!-- Back -->
<div class="d-flex justify-content-start mb-4">
	<a href="{{ route('admin.website.pages') }}" class="btn btn-outline-secondary" style="border-radius: 8px;"><i class="icon-arrow-left me-1"></i> Back to Pages</a>
</div>
@endsection

@push('scripts')
@if(!($isHomePage ?? false))
<script src="{{ asset('assets/js/editor/ckeditor/ckeditor.js') }}"></script>
@endif
<script>
jQuery(document).ready(function() {
	// Accordion toggle (for section-card)
	jQuery('.section-card .card-top').on('click', function() {
		var target = jQuery('#' + jQuery(this).data('target'));
		var isOpen = target.hasClass('open');
		jQuery('.card-body-inner').removeClass('open');
		jQuery('.section-card .card-top').removeClass('open');
		if (!isOpen) {
			target.addClass('open');
			jQuery(this).addClass('open');
			setTimeout(function() { jQuery('html, body').animate({ scrollTop: target.closest('.section-card').offset().top - 80 }, 300); }, 50);
		}
	});

	@if(!($isHomePage ?? false))
	// CKEditor
	if (document.getElementById('pageEditor')) {
		CKEDITOR.replace('pageEditor', {
			height: 350,
			removeButtons: 'Save,NewPage,Preview,Print,Templates,PasteFromWord,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,CreateDiv,BidiLtr,BidiRtl,Language,Flash,Smiley,SpecialChar,PageBreak,Iframe,About',
			toolbar: [
				{ name: 'clipboard', items: ['Undo', 'Redo', '-', 'Paste', 'PasteText'] },
				{ name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat'] },
				{ name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote'] },
				{ name: 'alignment', items: ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
				{ name: 'links', items: ['Link', 'Unlink'] },
				{ name: 'insert', items: ['Image', 'Table', 'HorizontalRule'] },
				'/',
				{ name: 'styles', items: ['Format', 'Font', 'FontSize'] },
				{ name: 'colors', items: ['TextColor', 'BGColor'] },
				{ name: 'tools', items: ['Maximize', 'Source'] }
			],
			contentsCss: ['https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap'],
			allowedContent: true
		});
	}

	// Live title update
	jQuery('#titleInput').on('input', function() { jQuery('#previewTitle').text(jQuery(this).val() || 'Untitled'); });
	jQuery('#bannerColor').on('input', function() { jQuery('#bannerColorText').val(jQuery(this).val()); jQuery('<style>#previewBanner::before{background:'+jQuery(this).val()+'!important}</style>').appendTo('head'); });
	jQuery('#bannerColorText').on('input', function() { if (/^#[0-9A-Fa-f]{6}$/.test(jQuery(this).val())) { jQuery('#bannerColor').val(jQuery(this).val()); jQuery('<style>#previewBanner::before{background:'+jQuery(this).val()+'!important}</style>').appendTo('head'); } });
	jQuery('#bannerFileInput').on('change', function() { if(this.files[0]){var r=new FileReader();r.onload=function(e){jQuery('#previewBanner').css('background-image','url('+e.target.result+')')};r.readAsDataURL(this.files[0]);} });
	@endif

	// Add Section toggle
	jQuery('#addSectionToggle').on('click', function() { jQuery('#addSectionForm').toggleClass('d-none'); jQuery('#addSectionArrow').toggleClass('icon-angle-down icon-angle-up'); });
	// Edit/Cancel Section
	jQuery(document).on('click', '.edit-section-btn', function() { jQuery('#editForm-' + jQuery(this).data('id')).toggleClass('d-none'); });
	jQuery(document).on('click', '.cancel-edit-btn', function() { jQuery('#editForm-' + jQuery(this).data('id')).addClass('d-none'); });
	// Delete Section
	jQuery(document).on('click', '.delete-section-btn', function(e) {
		e.preventDefault();
		var form = jQuery(this).closest('form'), name = jQuery(this).data('name');
		Swal.fire({ title: 'Delete Section?', html: 'Delete <strong>'+name+'</strong>? This cannot be undone.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#FC4438', confirmButtonText: 'Yes, delete', reverseButtons: true }).then(function(r){ if(r.isConfirmed) form.submit(); });
	});

	@if(session('success'))
		Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: '{{ session("success") }}', showConfirmButton: false, timer: 3000, timerProgressBar: true });
	@endif
});
</script>
@endpush
