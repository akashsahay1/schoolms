@extends('layouts.app')

@section('title', 'Edit Page - ' . $page->title)

@section('page-title', 'Edit Page')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.website.index') }}">Website</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.website.pages') }}">Pages</a></li>
	<li class="breadcrumb-item active">{{ $page->title }}</li>
@endsection

@section('content')
@if(session('success'))
	<div class="alert alert-success alert-dismissible fade show" role="alert">
		<i class="icon-check me-2"></i> {{ session('success') }}
		<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
	</div>
@endif

@if($errors->any())
	<div class="alert alert-danger alert-dismissible fade show" role="alert">
		<ul class="mb-0">
			@foreach($errors->all() as $error)
				<li>{{ $error }}</li>
			@endforeach
		</ul>
		<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
	</div>
@endif

<form action="{{ route('admin.website.pages.update', $page) }}" method="POST" enctype="multipart/form-data" id="pageForm">
	@csrf
	@method('PUT')

	<div class="row">
		<!-- Main Content -->
		<div class="col-lg-8">
			<div class="card">
				<div class="card-header">
					<h5>Page Content</h5>
				</div>
				<div class="card-body">
					<div class="mb-3">
						<label class="form-label">Page Title <span class="text-danger">*</span></label>
						<input type="text" name="title" class="form-control form-control-lg" value="{{ old('title', $page->title) }}" required placeholder="Enter page title">
					</div>

					<div class="mb-3">
						<label class="form-label">Page Content</label>
						<textarea name="content" id="pageEditor">{{ old('content', $page->content) }}</textarea>
					</div>
				</div>
			</div>
		</div>

		<!-- Sidebar -->
		<div class="col-lg-4">
			<!-- Actions -->
			<div class="card">
				<div class="card-header">
					<h5>Publish</h5>
				</div>
				<div class="card-body">
					<div class="d-flex justify-content-between align-items-center mb-3">
						<span class="text-muted">Status</span>
						<div class="form-check form-switch">
							<input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ old('is_active', $page->is_active) ? 'checked' : '' }}>
							<label class="form-check-label" for="is_active">Visible</label>
						</div>
					</div>
					<div class="d-flex justify-content-between align-items-center mb-3">
						<span class="text-muted">Last Updated</span>
						<span>{{ $page->updated_at->format('d M Y, h:i A') }}</span>
					</div>
					<div class="d-grid gap-2">
						<button type="submit" class="btn btn-primary">
							<i class="icon-save me-1"></i> Update Page
						</button>
						<button type="button" class="btn btn-outline-info" id="previewBtn">
							<i class="icon-eye me-1"></i> Preview
						</button>
						@php
							$knownRoutes = ['home','about','academics','facilities','gallery','news','events','contact'];
							$viewUrl = in_array($page->slug, $knownRoutes)
								? route('website.' . ($page->slug === 'home' ? 'home' : $page->slug))
								: route('website.page', $page->slug);
						@endphp
						<a href="{{ $viewUrl }}" target="_blank" class="btn btn-outline-secondary">
							<i class="icon-link me-1"></i> View Live Page
						</a>
					</div>
				</div>
			</div>

			<!-- Banner Image -->
			<div class="card">
				<div class="card-header">
					<h5>Banner Image</h5>
				</div>
				<div class="card-body">
					<div class="mb-3">
						@if($page->banner_image)
							<div class="mb-2 position-relative">
								<img src="{{ asset('storage/' . $page->banner_image) }}" alt="{{ $page->title }}" class="img-thumbnail w-100" style="max-height: 150px; object-fit: cover;">
								<label class="text-danger small mt-1 d-block" style="cursor: pointer;">
									<input type="checkbox" name="remove_banner_image" value="1" class="me-1"> Remove image
								</label>
							</div>
						@endif
						<input type="file" name="banner_image" class="form-control" accept="image/*">
						<small class="text-muted">1920 x 400px recommended</small>
					</div>

					<div class="mb-0">
						<label class="form-label">Overlay Color</label>
						<div class="d-flex align-items-center gap-2">
							<input type="color" name="banner_color" class="form-control form-control-color" id="bannerColor" value="{{ old('banner_color', $page->banner_color ?? '#6065f2') }}" style="width: 50px; height: 38px;">
							<input type="text" class="form-control" id="bannerColorText" value="{{ old('banner_color', $page->banner_color ?? '#6065f2') }}" maxlength="7" style="max-width: 100px;">
							<button type="button" class="btn btn-sm btn-outline-secondary" id="resetBannerColor">Reset</button>
						</div>
					</div>
				</div>
			</div>

			<!-- SEO -->
			<div class="card">
				<div class="card-header">
					<h5>SEO Settings</h5>
				</div>
				<div class="card-body">
					<div class="mb-3">
						<label class="form-label">Meta Description</label>
						<textarea name="meta_description" class="form-control" rows="3" maxlength="160">{{ old('meta_description', $page->meta_description) }}</textarea>
						<small class="text-muted">Max 160 characters</small>
					</div>
					<div class="mb-0">
						<label class="form-label">Meta Keywords</label>
						<input type="text" name="meta_keywords" class="form-control" value="{{ old('meta_keywords', $page->meta_keywords) }}" placeholder="keyword1, keyword2">
					</div>
				</div>
			</div>
		</div>
	</div>
</form>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
	<div class="modal-dialog modal-xl modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Page Preview</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body p-0">
				<div style="background: #7366ff; color: #fff; text-align: center; padding: 40px 20px;">
					<h2 id="previewTitle" style="margin: 0;">{{ $page->title }}</h2>
				</div>
				<div class="p-4 p-md-5" id="previewContent" style="min-height: 300px;">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/editor/ckeditor/ckeditor.js') }}"></script>
<script>
jQuery(document).ready(function() {
	// Initialize CKEditor
	CKEDITOR.replace('pageEditor', {
		height: 400,
		removeButtons: 'Save,NewPage,Preview,Print,Templates,PasteFromWord,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,CreateDiv,BidiLtr,BidiRtl,Language,Flash,Smiley,SpecialChar,PageBreak,Iframe,About',
		toolbar: [
			{ name: 'clipboard', items: ['Undo', 'Redo', '-', 'Cut', 'Copy', 'Paste', 'PasteText'] },
			{ name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat'] },
			{ name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote'] },
			{ name: 'alignment', items: ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
			{ name: 'links', items: ['Link', 'Unlink', 'Anchor'] },
			{ name: 'insert', items: ['Image', 'Table', 'HorizontalRule'] },
			'/',
			{ name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
			{ name: 'colors', items: ['TextColor', 'BGColor'] },
			{ name: 'tools', items: ['Maximize', 'Source'] }
		],
		contentsCss: [
			'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap'
		],
		font_names: 'Poppins/Poppins, sans-serif;Arial/Arial, Helvetica, sans-serif;Times New Roman/Times New Roman, Times, serif;Verdana/Verdana, Geneva, sans-serif',
		font_defaultLabel: 'Poppins',
		fontSize_sizes: '12/12px;14/14px;16/16px;18/18px;20/20px;24/24px;28/28px;32/32px;36/36px',
		bodyClass: 'page-content',
		allowedContent: true
	});

	// Preview button
	jQuery('#previewBtn').on('click', function() {
		var title = jQuery('input[name="title"]').val();
		var content = CKEDITOR.instances.pageEditor.getData();

		jQuery('#previewTitle').text(title || 'Untitled Page');
		jQuery('#previewContent').html(content || '<p class="text-muted text-center py-5">No content yet.</p>');

		var modal = new bootstrap.Modal(document.getElementById('previewModal'));
		modal.show();
	});

	// Banner color sync
	jQuery('#bannerColor').on('input', function() {
		jQuery('#bannerColorText').val(jQuery(this).val());
	});
	jQuery('#bannerColorText').on('input', function() {
		var val = jQuery(this).val();
		if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
			jQuery('#bannerColor').val(val);
		}
	});
	jQuery('#resetBannerColor').on('click', function() {
		jQuery('#bannerColor').val('#6065f2');
		jQuery('#bannerColorText').val('#6065f2');
	});

	// Success toast
	@if(session('success'))
		Swal.fire({
			toast: true,
			position: 'top-end',
			icon: 'success',
			title: '{{ session('success') }}',
			showConfirmButton: false,
			timer: 3000,
			timerProgressBar: true
		});
	@endif
});
</script>
@endpush
