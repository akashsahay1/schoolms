@extends('layouts.app')

@section('title', 'Add Fee Type')

@section('page-title', 'Add Fee Type')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.fees.types.index') }}">Fee Types</a></li>
    <li class="breadcrumb-item active">Add Fee Type</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5>Add New Fee Type</h5>
            </div>
            <div class="card-body">
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

                <form action="{{ route('admin.fees.types.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Fee Type Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}"
                                   placeholder="e.g., Tuition Fee, Transport Fee" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="code" class="form-label">Fee Code <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('code') is-invalid @enderror"
                                       id="code" name="code" value="{{ old('code') }}"
                                       placeholder="Auto-generated" style="text-transform: uppercase;" required>
                                <button type="button" class="btn btn-outline-secondary" id="edit-code-btn" title="Edit code manually">
                                    <i data-feather="edit-2"></i>
                                </button>
                            </div>
                            <small class="text-muted">Auto-generated from name. Click edit to change.</small>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3"
                                      placeholder="Enter description (optional)">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active
                                </label>
                                <small class="text-muted d-block">Inactive fee types won't appear in fee structure creation</small>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-between">
                        <a href="{{ route('admin.fees.types.index') }}" class="btn btn-light">
                            <i data-feather="arrow-left" class="me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="me-1"></i> Save Fee Type
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
	var autoGenerate = true;

	jQuery('#name').on('input', function() {
		if (!autoGenerate) return;
		var name = jQuery(this).val().trim();
		if (name) {
			var words = name.split(/\s+/);
			var code;
			if (words.length >= 2) {
				code = words.slice(0, 5).map(function(w) { return w.charAt(0); }).join('');
			} else {
				code = name.substring(0, 4);
			}
			jQuery('#code').val(code.toUpperCase());
		} else {
			jQuery('#code').val('');
		}
	});

	jQuery('#edit-code-btn').on('click', function() {
		autoGenerate = !autoGenerate;
		if (autoGenerate) {
			jQuery(this).removeClass('btn-primary').addClass('btn-outline-secondary');
			jQuery('#name').trigger('input');
		} else {
			jQuery(this).removeClass('btn-outline-secondary').addClass('btn-primary');
		}
		jQuery('#code').focus();
	});
});
</script>
@endpush
