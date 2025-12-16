@extends('layouts.app')

@section('title', 'School Settings')

@section('page-title', 'School Settings')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Settings</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- School Information -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5>School Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="school_name" class="form-label">School Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="school_name" name="school_name" 
                                       value="{{ old('school_name', $settings['school_name']) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="school_email" class="form-label">School Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="school_email" name="school_email" 
                                       value="{{ old('school_email', $settings['school_email']) }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="school_phone" class="form-label">School Phone <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="school_phone" name="school_phone" 
                                       value="{{ old('school_phone', $settings['school_phone']) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="school_website" class="form-label">School Website</label>
                                <input type="text" class="form-control" id="school_website" name="school_website"
                                       value="{{ old('school_website', $settings['school_website']) }}"
                                       placeholder="www.example.com">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="school_address" class="form-label">School Address <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="school_address" name="school_address" rows="3" required>{{ old('school_address', $settings['school_address']) }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="school_logo" class="form-label">School Logo</label>
                                <input type="file" class="form-control" id="school_logo" name="school_logo" accept="image/*">
                                @if($settings['school_logo'])
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $settings['school_logo']) }}" alt="School Logo" 
                                             class="img-thumbnail" style="max-height: 100px;">
                                        <p class="small text-muted mt-1">Current logo (upload new to replace)</p>
                                    </div>
                                @endif
                                <small class="text-muted">Recommended size: 200x100px, Max: 2MB</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Principal Information -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5>Principal Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="principal_name" class="form-label">Principal Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="principal_name" name="principal_name" 
                                       value="{{ old('principal_name', $settings['principal_name']) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="principal_signature" class="form-label">Principal Designation</label>
                                <input type="text" class="form-control" id="principal_signature" name="principal_signature" 
                                       value="{{ old('principal_signature', $settings['principal_signature']) }}" 
                                       placeholder="e.g., Principal, Director">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Signature Settings -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5>Signature Settings</h5>
                    <p class="mb-0 small text-muted">Configure how signatures appear on receipts and documents</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="authorized_signature_text" class="form-label">Signature Text</label>
                                <input type="text" class="form-control" id="authorized_signature_text" name="authorized_signature_text" 
                                       value="{{ old('authorized_signature_text', $settings['authorized_signature_text']) }}" 
                                       placeholder="e.g., Authorized Signatory, Accounts Officer">
                                <small class="text-muted">This text will appear below the signature line on receipts</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="signature_image" class="form-label">Signature Image (Optional)</label>
                                <input type="file" class="form-control" id="signature_image" name="signature_image" accept="image/*">
                                @if($settings['signature_image'])
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $settings['signature_image']) }}" alt="Signature" 
                                             class="img-thumbnail" style="max-height: 80px;">
                                        <p class="small text-muted mt-1">Current signature (upload new to replace)</p>
                                    </div>
                                @endif
                                <small class="text-muted">Recommended size: 200x80px, Max: 1MB</small>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-light-primary txt-primary" role="alert">
                        <p class="mb-2"><strong>How signatures work:</strong></p>
                        <ul class="mb-0 ps-3">
                            <li><strong>Text only:</strong> Shows admin name + signature text</li>
                            <li><strong>Image only:</strong> Shows uploaded signature image + admin name</li>
                            <li><strong>Both:</strong> Shows signature image + signature text</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i data-feather="save" class="icon-xs"></i> Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
	jQuery(document).ready(function() {
		// Preview uploaded images
		jQuery('#school_logo').on('change', function() {
			previewImage(this, 'logo-preview');
		});

		jQuery('#signature_image').on('change', function() {
			previewImage(this, 'signature-preview');
		});

		function previewImage(input, previewId) {
			if (input.files && input.files[0]) {
				var reader = new FileReader();
				reader.onload = function(e) {
					var preview = jQuery('#' + previewId);
					if (preview.length === 0) {
						jQuery(input).after('<div id="' + previewId + '" class="mt-2"><img class="img-thumbnail" style="max-height: 100px;"><p class="small text-muted mt-1">Preview</p></div>');
						preview = jQuery('#' + previewId);
					}
					preview.find('img').attr('src', e.target.result);
				};
				reader.readAsDataURL(input.files[0]);
			}
		}
	});
</script>
@endpush