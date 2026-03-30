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
.page-editor-container {
	margin: -15px -15px 0;
}
/* Top toolbar */
.editor-toolbar {
	background: #1e1e2d;
	padding: 10px 20px;
	display: flex;
	justify-content: space-between;
	align-items: center;
	position: sticky;
	top: 60px;
	z-index: 100;
	border-radius: 0;
}
.editor-toolbar .page-info {
	display: flex;
	align-items: center;
	gap: 10px;
	color: #fff;
}
.editor-toolbar .page-info h6 {
	margin: 0;
	font-size: 14px;
	color: #fff;
}
.editor-toolbar .toolbar-actions {
	display: flex;
	gap: 8px;
	align-items: center;
}
.toolbar-btn {
	padding: 6px 14px;
	border-radius: 6px;
	font-size: 12px;
	font-weight: 600;
	border: none;
	cursor: pointer;
	display: inline-flex;
	align-items: center;
	gap: 5px;
	text-decoration: none;
	transition: all 0.2s;
}
.toolbar-btn-outline {
	background: transparent;
	border: 1px solid rgba(255,255,255,0.3);
	color: #fff;
}
.toolbar-btn-outline:hover { background: rgba(255,255,255,0.1); color: #fff; }
.toolbar-btn-primary { background: var(--theme-default); color: #fff; }
.toolbar-btn-primary:hover { background: #5a4fd4; color: #fff; }

/* Section edit strips */
.section-edit-strip {
	background: rgba(115, 102, 255, 0.95);
	padding: 6px 16px;
	display: flex;
	justify-content: space-between;
	align-items: center;
	color: #fff;
	font-size: 12px;
	font-weight: 600;
}
.section-edit-strip .section-name {
	display: flex;
	align-items: center;
	gap: 8px;
}
.section-edit-strip .section-name .num {
	background: rgba(255,255,255,0.25);
	width: 22px;
	height: 22px;
	border-radius: 50%;
	display: inline-flex;
	align-items: center;
	justify-content: center;
	font-size: 11px;
}
.section-edit-strip a {
	color: #fff;
	background: rgba(255,255,255,0.2);
	padding: 3px 12px;
	border-radius: 4px;
	font-size: 11px;
	text-decoration: none;
	transition: background 0.2s;
}
.section-edit-strip a:hover {
	background: rgba(255,255,255,0.35);
	color: #fff;
}

/* Website iframe */
.website-frame {
	width: 100%;
	border: none;
	min-height: 80vh;
}

/* Non-home page editor */
.content-editor-wrapper {
	max-width: 900px;
	margin: 0 auto;
	padding: 20px;
}
.editable-section {
	position: relative;
	transition: box-shadow 0.2s;
}
.editable-section:hover {
	box-shadow: 0 0 0 3px var(--theme-default);
	border-radius: 8px;
}
.editable-section:hover .edit-btn { opacity: 1; }
.edit-btn {
	position: absolute;
	top: 10px;
	right: 10px;
	z-index: 10;
	opacity: 0;
	transition: opacity 0.2s;
	background: var(--theme-default);
	color: #fff;
	border: none;
	border-radius: 6px;
	padding: 6px 14px;
	font-size: 12px;
	font-weight: 600;
	cursor: pointer;
	display: flex;
	align-items: center;
	gap: 5px;
	box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}
.edit-btn:hover { background: #5a4fd4; }
.section-label-tag {
	position: absolute;
	top: 10px;
	left: 10px;
	z-index: 10;
	background: rgba(0,0,0,0.6);
	color: #fff;
	padding: 3px 10px;
	border-radius: 4px;
	font-size: 10px;
	font-weight: 600;
	text-transform: uppercase;
	letter-spacing: 0.5px;
	opacity: 0;
	transition: opacity 0.2s;
}
.editable-section:hover .section-label-tag { opacity: 1; }
.preview-banner {
	background: url('{{ $page->banner_image ? asset("storage/" . $page->banner_image) : asset("assets/images/banner/4.jpg") }}') center/cover no-repeat;
	padding: 80px 30px 60px;
	color: #fff;
	text-align: center;
	position: relative;
	overflow: hidden;
	border-radius: 8px 8px 0 0;
}
.preview-banner::before {
	content: '';
	position: absolute;
	inset: 0;
	background: {{ $page->banner_color ?? '#6065f2' }};
	opacity: 0.85;
}
.preview-banner * { position: relative; z-index: 2; }
.preview-banner h1 { font-size: 1.8rem; font-weight: 700; margin: 0 0 6px; }
.preview-banner .breadcrumb-preview { font-size: 13px; opacity: 0.8; }
.preview-body {
	background: #fff;
	padding: 40px;
	border-radius: 0 0 8px 8px;
	box-shadow: 0 2px 20px rgba(0,0,0,0.06);
	min-height: 200px;
	font-size: 14px;
	line-height: 1.8;
	color: #555;
}
.preview-body h1,.preview-body h2,.preview-body h3 { color: #2c323f; font-weight: 600; }
.preview-body p { margin-bottom: 12px; }
.preview-body blockquote { border-left: 4px solid #7366ff; padding: 14px 20px; background: #f5f4ff; border-radius: 0 8px 8px 0; margin: 16px 0; }
.preview-body img { max-width: 100%; border-radius: 8px; }
.preview-body table { width: 100%; border-collapse: collapse; margin: 16px 0; }
.preview-body table th,.preview-body table td { border: 1px solid #e9ecef; padding: 10px 14px; }
.preview-body table th { background: #f5f4ff; font-weight: 600; }
.edit-drawer { display: none; background: #fff; border: 2px solid var(--theme-default); border-radius: 10px; padding: 20px; margin: 12px 0; box-shadow: 0 4px 20px rgba(115,102,255,0.15); }
.edit-drawer.open { display: block; }
.edit-drawer-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px solid #eee; }
.edit-drawer-header h6 { margin: 0; font-weight: 700; color: var(--theme-default); }
.close-drawer { background: none; border: none; font-size: 18px; color: #999; cursor: pointer; }
.close-drawer:hover { color: #333; }
.empty-content { text-align: center; padding: 60px 20px; color: #aaa; }
</style>
@endpush

@section('content')
@if(session('success'))
	<div class="alert alert-success alert-dismissible fade show py-2 px-3 mb-3" role="alert" style="font-size: 13px; border-radius: 8px;">
		<i class="icon-check me-1"></i> {{ session('success') }}
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

@if($isHomePage)
{{-- ==================== HOME PAGE EDITOR ==================== --}}
<div class="page-editor-container">
	<!-- Top Toolbar -->
	<div class="editor-toolbar">
		<div class="page-info">
			<a href="{{ route('admin.website.pages') }}" class="toolbar-btn toolbar-btn-outline"><i class="icon-arrow-left"></i></a>
			<h6>Editing: {{ $page->title }}</h6>
			<span class="badge bg-success" style="font-size: 10px;">LIVE</span>
		</div>
		<div class="toolbar-actions">
			<a href="{{ route('admin.website.homepage-sections') }}" class="toolbar-btn toolbar-btn-outline"><i class="icon-settings"></i> Homepage Sections</a>
			<a href="{{ $viewUrl }}" target="_blank" class="toolbar-btn toolbar-btn-outline"><i class="icon-eye"></i> View Live</a>
		</div>
	</div>

	<!-- Section: Hero Slider -->
	<div class="section-edit-strip">
		<div class="section-name"><span class="num">1</span> Hero Slider</div>
		<a href="{{ route('admin.website.sliders') }}"><i class="icon-pencil-alt me-1"></i> Edit Sliders</a>
	</div>

	<!-- Section: Why Choose Us -->
	<div class="section-edit-strip" style="background: rgba(84, 186, 74, 0.9);">
		<div class="section-name"><span class="num">2</span> Why Choose Us</div>
		<a href="{{ route('admin.website.homepage-sections') }}#section-why"><i class="icon-pencil-alt me-1"></i> Edit Section</a>
	</div>

	<!-- Section: About Us -->
	<div class="section-edit-strip" style="background: rgba(255, 170, 5, 0.9);">
		<div class="section-name"><span class="num">3</span> About Us</div>
		<a href="{{ route('admin.website.homepage-sections') }}#section-about"><i class="icon-pencil-alt me-1"></i> Edit Section</a>
	</div>

	<!-- Section: Statistics -->
	<div class="section-edit-strip" style="background: rgba(255, 51, 100, 0.9);">
		<div class="section-name"><span class="num">4</span> Statistics Counters</div>
		<a href="{{ route('admin.website.homepage-sections') }}#section-stats"><i class="icon-pencil-alt me-1"></i> Edit Section</a>
	</div>

	<!-- Section: Facilities -->
	<div class="section-edit-strip" style="background: rgba(0, 176, 155, 0.9);">
		<div class="section-name"><span class="num">5</span> Facilities</div>
		<a href="{{ route('admin.website.facilities') }}"><i class="icon-pencil-alt me-1"></i> Edit Facilities</a>
	</div>

	<!-- Section: Events & News -->
	<div class="section-edit-strip" style="background: rgba(108, 117, 125, 0.9);">
		<div class="section-name"><span class="num">6</span> Events & News</div>
		<div class="d-flex gap-2">
			<a href="{{ route('admin.events.index') }}"><i class="icon-pencil-alt me-1"></i> Events</a>
			<a href="{{ route('admin.notices.index') }}"><i class="icon-pencil-alt me-1"></i> Notices</a>
		</div>
	</div>

	<!-- Section: Gallery -->
	<div class="section-edit-strip" style="background: rgba(23, 162, 184, 0.9);">
		<div class="section-name"><span class="num">7</span> Photo Gallery</div>
		<a href="{{ route('admin.website.gallery') }}"><i class="icon-pencil-alt me-1"></i> Edit Gallery</a>
	</div>

	<!-- Section: Testimonials -->
	<div class="section-edit-strip" style="background: rgba(220, 53, 69, 0.9);">
		<div class="section-name"><span class="num">8</span> Testimonials</div>
		<a href="{{ route('admin.website.testimonials') }}"><i class="icon-pencil-alt me-1"></i> Edit Testimonials</a>
	</div>

	<!-- Section: CTA -->
	<div class="section-edit-strip" style="background: rgba(115, 102, 255, 0.9);">
		<div class="section-name"><span class="num">9</span> Call to Action Banner</div>
		<a href="{{ route('admin.website.homepage-sections') }}#section-cta"><i class="icon-pencil-alt me-1"></i> Edit Section</a>
	</div>

	<!-- Actual Website Page in iframe -->
	<iframe src="{{ $viewUrl }}" class="website-frame" id="websiteFrame"></iframe>

	<!-- Bottom bar: SEO & Settings -->
	<div style="padding: 20px; background: #f8f9fa;">
		<form action="{{ route('admin.website.pages.update', $page) }}" method="POST" enctype="multipart/form-data">
			@csrf
			@method('PUT')
			<input type="hidden" name="title" value="{{ $page->title }}">
			<input type="hidden" name="is_active" value="{{ $page->is_active ? '1' : '0' }}">

			<div class="row g-3" style="max-width: 900px; margin: 0 auto;">
				<div class="col-md-4">
					<label class="form-label fw-bold">Browser Tab Title</label>
					<input type="text" name="title" class="form-control" value="{{ $page->title }}">
				</div>
				<div class="col-md-4">
					<label class="form-label fw-bold">SEO Description</label>
					<input type="text" name="meta_description" class="form-control" value="{{ $page->meta_description }}" maxlength="160" placeholder="For Google search results">
				</div>
				<div class="col-md-4">
					<label class="form-label fw-bold">SEO Keywords</label>
					<input type="text" name="meta_keywords" class="form-control" value="{{ $page->meta_keywords }}" placeholder="school, education">
				</div>
				<div class="col-12 text-end">
					<button type="submit" class="btn btn-primary"><i class="icon-check me-1"></i> Save SEO Settings</button>
				</div>
			</div>
		</form>
	</div>
</div>

@else
{{-- ==================== REGULAR PAGE EDITOR (Privacy, About, etc.) ==================== --}}

<!-- Top Toolbar -->
<div class="page-editor-container">
	<div class="editor-toolbar">
		<div class="page-info">
			<a href="{{ route('admin.website.pages') }}" class="toolbar-btn toolbar-btn-outline"><i class="icon-arrow-left"></i></a>
			<h6>Editing: {{ $page->title }}</h6>
			<span class="badge {{ $page->is_active ? 'bg-success' : 'bg-danger' }}" style="font-size: 10px;">{{ $page->is_active ? 'LIVE' : 'HIDDEN' }}</span>
		</div>
		<div class="toolbar-actions">
			<a href="{{ $viewUrl }}" target="_blank" class="toolbar-btn toolbar-btn-outline"><i class="icon-eye"></i> View Live</a>
			<button type="submit" form="pageForm" class="toolbar-btn toolbar-btn-primary"><i class="icon-check"></i> Save Changes</button>
		</div>
	</div>
</div>

<form action="{{ route('admin.website.pages.update', $page) }}" method="POST" enctype="multipart/form-data" id="pageForm">
	@csrf
	@method('PUT')

	<div class="content-editor-wrapper">
		<!-- Banner Section -->
		<div class="editable-section" id="bannerSection">
			<span class="section-label-tag">Banner</span>
			<button type="button" class="edit-btn" data-target="bannerDrawer"><i class="icon-pencil-alt"></i> Edit Banner</button>

			<div class="preview-banner" id="previewBanner">
				<h1 id="previewTitle">{{ $page->title }}</h1>
				<div class="breadcrumb-preview">Home / <strong>{{ $page->title }}</strong></div>
			</div>

			<div class="edit-drawer" id="bannerDrawer">
				<div class="edit-drawer-header">
					<h6><i class="icon-image me-2"></i> Edit Banner</h6>
					<button type="button" class="close-drawer" data-close="bannerDrawer">&times;</button>
				</div>
				<div class="row g-3">
					<div class="col-md-6">
						<label class="form-label fw-bold">Page Title</label>
						<input type="text" name="title" id="titleInput" class="form-control" value="{{ old('title', $page->title) }}" required>
					</div>
					<div class="col-md-3">
						<label class="form-label fw-bold">Overlay Color</label>
						<div class="d-flex gap-2">
							<input type="color" name="banner_color" id="bannerColor" class="form-control form-control-color" value="{{ old('banner_color', $page->banner_color ?? '#6065f2') }}" style="width: 50px;">
							<input type="text" id="bannerColorText" class="form-control" value="{{ old('banner_color', $page->banner_color ?? '#6065f2') }}" maxlength="7" style="max-width: 90px; font-family: monospace;">
						</div>
					</div>
					<div class="col-md-3">
						<label class="form-label fw-bold">Banner Image</label>
						<input type="file" name="banner_image" class="form-control form-control-sm" accept="image/*" id="bannerFileInput">
						@if($page->banner_image)
							<div class="form-check mt-1">
								<input type="checkbox" name="remove_banner_image" value="1" class="form-check-input" id="removeBanner">
								<label class="form-check-label text-danger" for="removeBanner" style="font-size: 11px;">Remove</label>
							</div>
						@endif
					</div>
				</div>
			</div>
		</div>

		<!-- Content Section -->
		<div class="editable-section" id="contentSection">
			<span class="section-label-tag">Main Content</span>
			<button type="button" class="edit-btn" data-target="contentDrawer"><i class="icon-pencil-alt"></i> Edit Content</button>

			<div class="preview-body" id="previewContent">
				@if($page->content)
					{!! $page->content !!}
				@else
					<div class="empty-content">
						<i class="icon-note" style="font-size: 40px;"></i>
						<p>No content yet. Click <strong>"Edit Content"</strong> to start writing.</p>
					</div>
				@endif
			</div>

			<div class="edit-drawer" id="contentDrawer">
				<div class="edit-drawer-header">
					<h6><i class="icon-pencil-alt me-2"></i> Edit Content</h6>
					<button type="button" class="close-drawer" data-close="contentDrawer">&times;</button>
				</div>
				<textarea name="content" id="pageEditor">{{ old('content', $page->content) }}</textarea>
				<p class="text-muted mt-2 mb-0" style="font-size: 11px;"><i class="icon-info-alt me-1"></i> Changes appear above when you close this editor.</p>
			</div>
		</div>
	</div>
</form>

<!-- ==================== CUSTOM SECTIONS ==================== -->
<div class="content-editor-wrapper">
	<!-- Existing Sections -->
	@foreach($sections ?? [] as $section)
	<div class="card mt-3 mb-0" style="border-left: 4px solid {{ $section->bg_color ?? '#7366ff' }}; border-radius: 8px;">
		<div class="card-body p-0">
			<!-- Section Preview -->
			<div class="p-3" style="background: {{ $section->bg_color ? $section->bg_color . '10' : '#f8f9fa' }};">
				@if($section->layout === 'image-left' || $section->layout === 'image-right')
				<div class="row align-items-center g-0">
					@if($section->layout === 'image-left')
					<div class="col-md-5">
						@if($section->image)
							<img src="{{ asset('storage/' . $section->image) }}" alt="" class="w-100" style="max-height: 200px; object-fit: cover; border-radius: 8px;">
						@else
							<div class="bg-light d-flex align-items-center justify-content-center" style="height: 150px; border-radius: 8px;"><i class="icon-image" style="font-size: 30px; color: #ccc;"></i></div>
						@endif
					</div>
					<div class="col-md-7 ps-3">
						@if($section->title)<h5 class="mb-1">{{ $section->title }}</h5>@endif
						@if($section->subtitle)<p class="text-muted mb-1" style="font-size: 13px;">{{ $section->subtitle }}</p>@endif
						@if($section->content)<div style="font-size: 13px; color: #555;">{!! Str::limit(strip_tags($section->content), 200) !!}</div>@endif
					</div>
					@else
					<div class="col-md-7 pe-3">
						@if($section->title)<h5 class="mb-1">{{ $section->title }}</h5>@endif
						@if($section->subtitle)<p class="text-muted mb-1" style="font-size: 13px;">{{ $section->subtitle }}</p>@endif
						@if($section->content)<div style="font-size: 13px; color: #555;">{!! Str::limit(strip_tags($section->content), 200) !!}</div>@endif
					</div>
					<div class="col-md-5">
						@if($section->image)
							<img src="{{ asset('storage/' . $section->image) }}" alt="" class="w-100" style="max-height: 200px; object-fit: cover; border-radius: 8px;">
						@else
							<div class="bg-light d-flex align-items-center justify-content-center" style="height: 150px; border-radius: 8px;"><i class="icon-image" style="font-size: 30px; color: #ccc;"></i></div>
						@endif
					</div>
					@endif
				</div>
				@elseif($section->layout === 'full-image')
				<div class="position-relative" style="border-radius: 8px; overflow: hidden;">
					@if($section->image)
						<img src="{{ asset('storage/' . $section->image) }}" alt="" class="w-100" style="max-height: 200px; object-fit: cover;">
						<div class="position-absolute bottom-0 start-0 end-0 p-3" style="background: linear-gradient(transparent, rgba(0,0,0,0.7));">
							@if($section->title)<h5 class="text-white mb-0">{{ $section->title }}</h5>@endif
						</div>
					@endif
				</div>
				@else
				<div class="{{ $section->layout === 'content-center' ? 'text-center' : '' }}">
					@if($section->title)<h5 class="mb-1">{{ $section->title }}</h5>@endif
					@if($section->subtitle)<p class="text-muted mb-1" style="font-size: 13px;">{{ $section->subtitle }}</p>@endif
					@if($section->content)<div style="font-size: 13px; color: #555;">{!! Str::limit(strip_tags($section->content), 300) !!}</div>@endif
				</div>
				@endif
			</div>

			<!-- Section Actions -->
			<div class="d-flex justify-content-between align-items-center px-3 py-2" style="border-top: 1px solid #eee;">
				<div class="d-flex align-items-center gap-2">
					<span class="badge badge-light-primary" style="font-size: 10px;">{{ \App\Models\WebsiteSection::LAYOUTS[$section->layout] ?? $section->layout }}</span>
					<span class="text-muted" style="font-size: 11px;">Order: {{ $section->sort_order }}</span>
				</div>
				<div class="d-flex gap-2">
					<button type="button" class="btn btn-outline-primary btn-sm edit-section-btn" data-id="{{ $section->id }}" style="font-size: 11px;"><i class="icon-pencil-alt me-1"></i> Edit</button>
					<form action="{{ route('admin.website.sections.destroy', $section) }}" method="POST" class="d-inline delete-section-form">
						@csrf
						@method('DELETE')
						<button type="button" class="btn btn-outline-danger btn-sm delete-section-btn" data-name="{{ $section->title ?? 'this section' }}" style="font-size: 11px;"><i class="icon-trash me-1"></i> Delete</button>
					</form>
				</div>
			</div>

			<!-- Edit Form (hidden, shown on click) -->
			<div class="section-edit-form d-none p-3" id="editForm-{{ $section->id }}" style="border-top: 2px solid var(--theme-default); background: #fafbff;">
				<form action="{{ route('admin.website.sections.update', $section) }}" method="POST" enctype="multipart/form-data">
					@csrf
					@method('PUT')
					<div class="row g-3">
						<div class="col-md-4">
							<label class="form-label fw-bold">Layout</label>
							<select name="layout" class="form-select form-select-sm">
								@foreach(\App\Models\WebsiteSection::LAYOUTS as $key => $label)
									<option value="{{ $key }}" {{ $section->layout === $key ? 'selected' : '' }}>{{ $label }}</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-4">
							<label class="form-label fw-bold">Title</label>
							<input type="text" name="title" class="form-control form-control-sm" value="{{ $section->title }}">
						</div>
						<div class="col-md-4">
							<label class="form-label fw-bold">Subtitle</label>
							<input type="text" name="subtitle" class="form-control form-control-sm" value="{{ $section->subtitle }}">
						</div>
						<div class="col-md-8">
							<label class="form-label fw-bold">Content</label>
							<textarea name="content" class="form-control form-control-sm" rows="3">{{ $section->content }}</textarea>
						</div>
						<div class="col-md-4">
							<label class="form-label fw-bold">Image</label>
							@if($section->image)
								<div class="mb-1"><img src="{{ asset('storage/' . $section->image) }}" class="rounded" style="max-height: 60px;"></div>
								<div class="form-check mb-1"><input type="checkbox" name="remove_image" value="1" class="form-check-input"><label class="form-check-label text-danger" style="font-size: 11px;">Remove</label></div>
							@endif
							<input type="file" name="image" class="form-control form-control-sm" accept="image/*">
						</div>
						<div class="col-md-4">
							<label class="form-label fw-bold">Button Text</label>
							<input type="text" name="link_text" class="form-control form-control-sm" value="{{ $section->link_text }}" placeholder="e.g. Learn More">
						</div>
						<div class="col-md-4">
							<label class="form-label fw-bold">Button Link</label>
							<input type="text" name="link" class="form-control form-control-sm" value="{{ $section->link }}" placeholder="e.g. /about">
						</div>
						<div class="col-md-4">
							<label class="form-label fw-bold">Background Color</label>
							<input type="color" name="bg_color" class="form-control form-control-color" value="{{ $section->bg_color ?? '#ffffff' }}" style="width: 50px; height: 34px;">
						</div>
					</div>
					<div class="d-flex justify-content-end gap-2 mt-3">
						<button type="button" class="btn btn-secondary btn-sm cancel-edit-btn" data-id="{{ $section->id }}">Cancel</button>
						<button type="submit" class="btn btn-primary btn-sm"><i class="icon-check me-1"></i> Update Section</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	@endforeach

	<!-- Add New Section -->
	<div class="card mt-3" style="border: 2px dashed #dee2e6; border-radius: 8px;">
		<div class="card-header py-2" style="cursor: pointer; background: #f8f9fa;" id="addSectionToggle">
			<div class="d-flex justify-content-between align-items-center">
				<h6 class="mb-0" style="font-size: 14px;"><i class="icon-plus me-2" style="color: var(--theme-default);"></i> Add New Section</h6>
				<i class="icon-angle-down" id="addSectionArrow"></i>
			</div>
		</div>
		<div class="card-body d-none" id="addSectionForm">
			<form action="{{ route('admin.website.pages.sections.store', $page) }}" method="POST" enctype="multipart/form-data">
				@csrf
				<div class="row g-3">
					<div class="col-md-6">
						<label class="form-label fw-bold">Layout <span class="text-danger">*</span></label>
						<select name="layout" class="form-select" required>
							@foreach($layouts as $key => $label)
								<option value="{{ $key }}">{{ $label }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-6">
						<label class="form-label fw-bold">Section Title</label>
						<input type="text" name="title" class="form-control" placeholder="e.g. Our Mission">
					</div>
					<div class="col-md-12">
						<label class="form-label fw-bold">Subtitle</label>
						<input type="text" name="subtitle" class="form-control" placeholder="Short description line (optional)">
					</div>
					<div class="col-md-8">
						<label class="form-label fw-bold">Content</label>
						<textarea name="content" class="form-control" rows="4" placeholder="Write your section content here..."></textarea>
					</div>
					<div class="col-md-4">
						<label class="form-label fw-bold">Image</label>
						<input type="file" name="image" class="form-control" accept="image/*">
						<small class="text-muted">Used in image-left, image-right, full-image layouts</small>
					</div>
					<div class="col-md-4">
						<label class="form-label fw-bold">Button Text</label>
						<input type="text" name="link_text" class="form-control" placeholder="e.g. Read More">
					</div>
					<div class="col-md-4">
						<label class="form-label fw-bold">Button Link</label>
						<input type="text" name="link" class="form-control" placeholder="e.g. /about or full URL">
					</div>
					<div class="col-md-4">
						<label class="form-label fw-bold">Background Color</label>
						<div class="d-flex gap-2 align-items-center">
							<input type="color" name="bg_color" class="form-control form-control-color" value="#ffffff" style="width: 50px; height: 38px;">
							<small class="text-muted">Optional</small>
						</div>
					</div>
				</div>
				<div class="d-flex justify-content-end mt-3">
					<button type="submit" class="btn btn-success"><i class="icon-plus me-1"></i> Add Section</button>
				</div>
			</form>
		</div>
	</div>

	<!-- SEO & Settings -->
	<form action="{{ route('admin.website.pages.update', $page) }}" method="POST" enctype="multipart/form-data" id="seoForm">
		@csrf
		@method('PUT')
		<input type="hidden" name="title" value="{{ $page->title }}">
		<input type="hidden" name="content" value="{{ $page->content }}">
		<input type="hidden" name="is_active" value="{{ $page->is_active ? '1' : '0' }}">
		<div class="row mt-4 g-3">

		<!-- SEO & Settings -->
		<div class="row mt-4 g-3">
			<div class="col-md-6">
				<div class="card mb-0">
					<div class="card-header py-2"><h6 class="mb-0"><i class="icon-search me-2"></i> SEO</h6></div>
					<div class="card-body">
						<div class="mb-2">
							<label class="form-label" style="font-size: 12px;">Meta Description <small class="text-muted">(max 160)</small></label>
							<textarea name="meta_description" class="form-control form-control-sm" rows="2" maxlength="160">{{ old('meta_description', $page->meta_description) }}</textarea>
						</div>
						<div>
							<label class="form-label" style="font-size: 12px;">Keywords</label>
							<input type="text" name="meta_keywords" class="form-control form-control-sm" value="{{ old('meta_keywords', $page->meta_keywords) }}" placeholder="school, education">
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="card mb-0">
					<div class="card-header py-2"><h6 class="mb-0"><i class="icon-settings me-2"></i> Settings</h6></div>
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center mb-2">
							<span style="font-size: 13px;">Visibility</span>
							<div class="form-check form-switch mb-0">
								<input type="checkbox" name="is_active" class="form-check-input" value="1" {{ $page->is_active ? 'checked' : '' }}>
							</div>
						</div>
						<div class="d-flex justify-content-between align-items-center mb-2">
							<span class="text-muted" style="font-size: 13px;">URL</span>
							<code style="font-size: 11px;">{{ url('/') }}/{{ $page->slug }}</code>
						</div>
						<div class="d-flex justify-content-between align-items-center">
							<span class="text-muted" style="font-size: 13px;">Updated</span>
							<span style="font-size: 12px;">{{ $page->updated_at->format('d M Y, h:i A') }}</span>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Save -->
		<div class="d-flex justify-content-between align-items-center mt-3 mb-4">
			<a href="{{ route('admin.website.pages') }}" class="btn btn-outline-secondary"><i class="icon-arrow-left me-1"></i> Back</a>
			<button type="submit" form="seoForm" class="btn btn-primary px-4"><i class="icon-check me-1"></i> Save SEO & Settings</button>
		</div>
	</form>
</div>
@endif
@endsection

@push('scripts')
@if($isHomePage ?? false)
<script>
jQuery(document).ready(function() {
	// Auto-resize iframe to content height
	jQuery('#websiteFrame').on('load', function() {
		try {
			this.style.height = this.contentWindow.document.body.scrollHeight + 'px';
		} catch(e) {
			this.style.height = '3000px';
		}
	});
});
</script>
@else
<script src="{{ asset('assets/js/editor/ckeditor/ckeditor.js') }}"></script>
<script>
jQuery(document).ready(function() {
	// Edit drawer toggle
	jQuery(document).on('click', '.edit-btn', function(e) {
		e.preventDefault();
		var id = jQuery(this).data('target');
		jQuery('.edit-drawer.open').not('#' + id).removeClass('open');
		jQuery('#' + id).toggleClass('open');
		if (jQuery('#' + id).hasClass('open')) {
			jQuery('html, body').animate({ scrollTop: jQuery('#' + id).offset().top - 100 }, 300);
		}
	});
	jQuery(document).on('click', '.close-drawer', function() {
		var id = jQuery(this).data('close');
		jQuery('#' + id).removeClass('open');
		if (id === 'contentDrawer' && CKEDITOR.instances.pageEditor) {
			var content = CKEDITOR.instances.pageEditor.getData();
			jQuery('#previewContent').html(content || '<div class="empty-content"><i class="icon-note" style="font-size:40px;"></i><p>No content yet.</p></div>');
		}
	});

	// CKEditor
	CKEDITOR.replace('pageEditor', {
		height: 350,
		removeButtons: 'Save,NewPage,Preview,Print,Templates,PasteFromWord,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,CreateDiv,BidiLtr,BidiRtl,Language,Flash,Smiley,SpecialChar,PageBreak,Iframe,About',
		toolbar: [
			{ name: 'clipboard', items: ['Undo', 'Redo', '-', 'Cut', 'Copy', 'Paste', 'PasteText'] },
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

	// Live title
	jQuery('#titleInput').on('input', function() {
		jQuery('#previewTitle').text(jQuery(this).val() || 'Untitled');
	});
	// Live color
	jQuery('#bannerColor').on('input', function() {
		jQuery('#bannerColorText').val(jQuery(this).val());
		jQuery('<style>#previewBanner::before{background:' + jQuery(this).val() + '!important}</style>').appendTo('head');
	});
	jQuery('#bannerColorText').on('input', function() {
		if (/^#[0-9A-Fa-f]{6}$/.test(jQuery(this).val())) {
			jQuery('#bannerColor').val(jQuery(this).val());
			jQuery('<style>#previewBanner::before{background:' + jQuery(this).val() + '!important}</style>').appendTo('head');
		}
	});
	// Live banner image
	jQuery('#bannerFileInput').on('change', function() {
		if (this.files[0]) {
			var r = new FileReader();
			r.onload = function(e) { jQuery('#previewBanner').css('background-image', 'url(' + e.target.result + ')'); };
			r.readAsDataURL(this.files[0]);
		}
	});

	// Add Section toggle
	jQuery('#addSectionToggle').on('click', function() {
		jQuery('#addSectionForm').toggleClass('d-none');
		jQuery('#addSectionArrow').toggleClass('icon-angle-down icon-angle-up');
	});

	// Edit Section toggle
	jQuery(document).on('click', '.edit-section-btn', function() {
		var id = jQuery(this).data('id');
		jQuery('#editForm-' + id).toggleClass('d-none');
	});
	jQuery(document).on('click', '.cancel-edit-btn', function() {
		var id = jQuery(this).data('id');
		jQuery('#editForm-' + id).addClass('d-none');
	});

	// Delete Section
	jQuery(document).on('click', '.delete-section-btn', function(e) {
		e.preventDefault();
		var form = jQuery(this).closest('form');
		var name = jQuery(this).data('name');
		Swal.fire({
			title: 'Delete Section?',
			html: 'Delete <strong>' + name + '</strong>? This cannot be undone.',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#FC4438',
			confirmButtonText: 'Yes, delete',
			cancelButtonText: 'Cancel',
			reverseButtons: true
		}).then(function(result) {
			if (result.isConfirmed) form.submit();
		});
	});

	@if(session('success'))
		Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: '{{ session("success") }}', showConfirmButton: false, timer: 3000, timerProgressBar: true });
	@endif
});
</script>
@endif
@endpush
