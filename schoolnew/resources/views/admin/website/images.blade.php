@extends('layouts.app')

@section('title', 'Website Images')

@section('page-title', 'Website Images')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.website.index') }}">Website</a></li>
	<li class="breadcrumb-item active">Images</li>
@endsection

@push('styles')
<style>
	.img-card {
		border-radius: 12px;
		border: 2px solid #eef0f6;
		overflow: hidden;
		background: #fff;
		transition: all 0.3s ease;
	}
	.img-card:hover {
		border-color: #7366ff;
		box-shadow: 0 6px 20px rgba(115, 102, 255, 0.12);
	}
	.img-card .thumb {
		width: 100%;
		height: 180px;
		background: linear-gradient(135deg, #f5f7fb, #e8ecf4);
		display: flex;
		align-items: center;
		justify-content: center;
		overflow: hidden;
	}
	.img-card.big .thumb { height: 220px; }
	.img-card .thumb img {
		width: 100%;
		height: 100%;
		object-fit: cover;
	}
	.img-card .thumb .empty {
		text-align: center;
		color: #b4bcc8;
	}
	.img-card .thumb .empty i { font-size: 42px; display: block; margin-bottom: 6px; }
	.img-card .info { padding: 14px 16px 6px; }
	.img-card .info h6 { font-weight: 600; margin-bottom: 3px; }
	.img-card .info .pill {
		display: inline-block;
		padding: 2px 10px;
		border-radius: 20px;
		font-size: 11px;
		font-weight: 500;
	}
	.pill-on { background: #d4edda; color: #155724; }
	.pill-off { background: #fff3cd; color: #856404; }
	.img-card .actions {
		padding: 10px 16px 16px;
		display: flex;
		gap: 8px;
	}
	.img-card .actions label {
		cursor: pointer;
		margin: 0;
	}
	.img-card .upload-bar {
		padding: 6px 16px 10px;
	}
	.img-card .actions .btn-primary i,
	.img-card .actions .btn-outline-danger i {
		color: inherit;
	}
	.sec-header {
		display: flex;
		align-items: center;
		gap: 12px;
		margin-bottom: 20px;
	}
	.sec-header .icon-box {
		width: 42px;
		height: 42px;
		border-radius: 10px;
		display: flex;
		align-items: center;
		justify-content: center;
		color: #fff !important;
		font-size: 20px;
		flex-shrink: 0;
	}
	.sec-header .icon-box i {
		color: #fff !important;
	}
	.sec-header p { margin-bottom: 0; font-size: 13px; color: #8492a6; }
	.sec-header h5 { margin-bottom: 0; }
</style>
@endpush

@section('content')
<!-- Page Banners -->
<div class="sec-header">
	<div class="icon-box" style="background: linear-gradient(135deg, #7366ff, #a389ff);">
		<i class="icon-layers"></i>
	</div>
	<div>
		<h5>Page Banner Images</h5>
		<p>Set a unique banner for each page. Pages without a banner use the default.</p>
	</div>
</div>

<div class="row g-4 mb-5">
	@php $defaultBanner = \App\Models\Setting::get('default_banner_image'); @endphp

	<!-- Default Banner -->
	<div class="col-xl-4 col-md-6">
		<div class="img-card big" data-type="setting" data-key="default_banner_image">
			<div class="thumb">
				@if($defaultBanner)
					<img src="{{ asset('storage/' . $defaultBanner) }}" alt="Default Banner">
				@else
					<div class="empty">
						<i class="icon-image"></i>
						<span>No default banner</span>
					</div>
				@endif
			</div>
			<div class="info">
				<h6>Default Banner <span class="pill pill-{{ $defaultBanner ? 'on' : 'off' }}">{{ $defaultBanner ? 'Set' : 'Not Set' }}</span></h6>
				<small class="text-muted">Fallback for all pages &bull; 1920 x 400px</small>
			</div>
			<div class="actions">
				<label class="btn btn-primary btn-sm">
					<i class="icon-cloud-up me-1"></i> {{ $defaultBanner ? 'Replace' : 'Upload' }}
					<input type="file" class="d-none img-file-input" accept="image/jpeg,image/png,image/jpg,image/webp">
				</label>
				@if($defaultBanner)
					<button type="button" class="btn btn-outline-danger btn-sm img-delete-btn">
						<i class="icon-trash me-1"></i> Remove
					</button>
				@endif
			</div>
			<div class="upload-bar d-none">
				<div class="progress" style="height: 3px;">
					<div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div>
				</div>
				<small class="text-muted">Uploading...</small>
			</div>
		</div>
	</div>

	<!-- Per-Page Banners -->
	@foreach($pages as $page)
		<div class="col-xl-4 col-md-6">
			<div class="img-card" data-type="page_banner" data-id="{{ $page->id }}">
				<div class="thumb">
					@if($page->banner_image)
						<img src="{{ asset('storage/' . $page->banner_image) }}" alt="{{ $page->title }}">
					@else
						<div class="empty">
							<i class="icon-image"></i>
							<span>Uses default banner</span>
						</div>
					@endif
				</div>
				<div class="info">
					<h6>{{ $page->title }} <span class="pill pill-{{ $page->banner_image ? 'on' : 'off' }}">{{ $page->banner_image ? 'Custom' : 'Default' }}</span></h6>
					<small class="text-muted">/{{ $page->slug === 'home' ? '' : $page->slug }} &bull; 1920 x 400px</small>
				</div>
				<div class="actions">
					<label class="btn btn-primary btn-sm">
						<i class="icon-cloud-up me-1"></i> {{ $page->banner_image ? 'Replace' : 'Upload' }}
						<input type="file" class="d-none img-file-input" accept="image/jpeg,image/png,image/jpg,image/webp">
					</label>
					@if($page->banner_image)
						<button type="button" class="btn btn-outline-danger btn-sm img-delete-btn">
							<i class="icon-trash me-1"></i> Remove
						</button>
					@endif
				</div>
				<div class="upload-bar d-none">
					<div class="progress" style="height: 3px;">
						<div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div>
					</div>
					<small class="text-muted">Uploading...</small>
				</div>
			</div>
		</div>
	@endforeach
</div>

<!-- Homepage Section Images -->
<div class="sec-header">
	<div class="icon-box" style="background: linear-gradient(135deg, #f73164, #ff6b8a);">
		<i class="icon-home"></i>
	</div>
	<div>
		<h5>Homepage Section Images</h5>
		<p>Images used in various sections of the homepage.</p>
	</div>
</div>

<div class="row g-4 mb-4">
	@php $aboutImage = \App\Models\Setting::get('homepage_about_image'); @endphp
	<div class="col-xl-4 col-md-6">
		<div class="img-card" data-type="setting" data-key="homepage_about_image">
			<div class="thumb">
				@if($aboutImage)
					<img src="{{ asset('storage/' . $aboutImage) }}" alt="About Section">
				@else
					<div class="empty">
						<i class="icon-image"></i>
						<span>No image set</span>
					</div>
				@endif
			</div>
			<div class="info">
				<h6>About Section <span class="pill pill-{{ $aboutImage ? 'on' : 'off' }}">{{ $aboutImage ? 'Set' : 'Not Set' }}</span></h6>
				<small class="text-muted">Homepage "About Us" &bull; 600 x 500px</small>
			</div>
			<div class="actions">
				<label class="btn btn-primary btn-sm">
					<i class="icon-cloud-up me-1"></i> {{ $aboutImage ? 'Replace' : 'Upload' }}
					<input type="file" class="d-none img-file-input" accept="image/jpeg,image/png,image/jpg,image/webp">
				</label>
				@if($aboutImage)
					<button type="button" class="btn btn-outline-danger btn-sm img-delete-btn">
						<i class="icon-trash me-1"></i> Remove
					</button>
				@endif
			</div>
			<div class="upload-bar d-none">
				<div class="progress" style="height: 3px;">
					<div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div>
				</div>
				<small class="text-muted">Uploading...</small>
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {

	// Upload image
	jQuery(document).on('change', '.img-file-input', function() {
		var fileInput = jQuery(this);
		var card = fileInput.closest('.img-card');
		var file = this.files[0];
		if (!file) return;

		// Validate
		if (['image/jpeg','image/png','image/jpg','image/webp'].indexOf(file.type) === -1) {
			Swal.fire('Invalid File', 'Please select JPEG, PNG, or WEBP image.', 'error');
			fileInput.val('');
			return;
		}
		if (file.size > 3 * 1024 * 1024) {
			Swal.fire('Too Large', 'Max file size is 3MB.', 'error');
			fileInput.val('');
			return;
		}

		// Instant preview
		var reader = new FileReader();
		reader.onload = function(e) {
			card.find('.thumb').html('<img src="' + e.target.result + '" alt="Preview">');
		};
		reader.readAsDataURL(file);

		// Upload via AJAX
		var formData = new FormData();
		formData.append('image', file);
		formData.append('_token', '{{ csrf_token() }}');
		formData.append('type', card.data('type'));

		if (card.data('type') === 'page_banner') {
			formData.append('id', card.data('id'));
		} else {
			formData.append('key', card.data('key'));
		}

		card.find('.upload-bar').removeClass('d-none');

		jQuery.ajax({
			url: '{{ route("admin.website.images.upload") }}',
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			success: function(resp) {
				// Update UI
				card.find('.thumb').html('<img src="' + resp.path + '" alt="Uploaded">');
				card.find('.pill').removeClass('pill-off').addClass('pill-on').text(card.data('type') === 'page_banner' ? 'Custom' : 'Set');

				// Update button text
				card.find('.actions label .icon-cloud-up').parent().parent().find('.icon-cloud-up').next().remove();
				card.find('.actions label').html('<i class="icon-cloud-up me-1"></i> Replace<input type="file" class="d-none img-file-input" accept="image/jpeg,image/png,image/jpg,image/webp">');

				// Show delete button if not already
				if (card.find('.img-delete-btn').length === 0) {
					card.find('.actions').append('<button type="button" class="btn btn-outline-danger btn-sm img-delete-btn"><i class="icon-trash me-1"></i> Remove</button>');
				}

				Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Image saved!', showConfirmButton: false, timer: 2000 });
			},
			error: function(xhr) {
				Swal.fire('Upload Failed', xhr.responseJSON?.message || 'Try again.', 'error');
			},
			complete: function() {
				card.find('.upload-bar').addClass('d-none');
				fileInput.val('');
			}
		});
	});

	// Delete image
	jQuery(document).on('click', '.img-delete-btn', function() {
		var btn = jQuery(this);
		var card = btn.closest('.img-card');

		Swal.fire({
			title: 'Remove Image?',
			text: 'The default banner will be used instead.',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#d33',
			confirmButtonText: 'Yes, remove',
			reverseButtons: true
		}).then(function(result) {
			if (!result.isConfirmed) return;

			var data = { _token: '{{ csrf_token() }}', type: card.data('type') };
			if (card.data('type') === 'page_banner') {
				data.id = card.data('id');
			} else {
				data.key = card.data('key');
			}

			jQuery.ajax({
				url: '{{ route("admin.website.images.delete") }}',
				type: 'POST',
				data: data,
				success: function() {
					var label = card.data('type') === 'page_banner' ? 'Uses default banner' : 'No image set';
					card.find('.thumb').html('<div class="empty"><i class="icon-image" style="font-size:42px;display:block;margin-bottom:6px;"></i><span>' + label + '</span></div>');
					card.find('.pill').removeClass('pill-on').addClass('pill-off').text(card.data('type') === 'page_banner' ? 'Default' : 'Not Set');
					card.find('.actions label').html('<i class="icon-cloud-up me-1"></i> Upload<input type="file" class="d-none img-file-input" accept="image/jpeg,image/png,image/jpg,image/webp">');
					btn.remove();
					Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Image removed!', showConfirmButton: false, timer: 2000 });
				},
				error: function() {
					Swal.fire('Error', 'Failed to remove. Try again.', 'error');
				}
			});
		});
	});
});
</script>
@endpush
