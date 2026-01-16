@extends('layouts.app')

@section('title', 'School Settings')

@section('page-title', 'School Settings')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">School Settings</li>
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

        <form action="{{ route('admin.settings.school.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <!-- School Logo -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>School Logo</h5>
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-3">
                                @if($settings['school_logo'] ?? null)
                                    <img src="{{ asset('storage/' . $settings['school_logo']) }}" alt="School Logo" class="img-fluid rounded" style="max-height: 150px;" id="logo-preview">
                                @else
                                    <div class="border rounded p-4 bg-light" id="logo-placeholder">
                                        <i data-feather="image" style="width: 64px; height: 64px;" class="text-muted"></i>
                                        <p class="text-muted mt-2 mb-0">No logo uploaded</p>
                                    </div>
                                    <img src="" alt="School Logo" class="img-fluid rounded d-none" style="max-height: 150px;" id="logo-preview">
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="school_logo" class="form-label">Upload Logo</label>
                                <input type="file" class="form-control @error('school_logo') is-invalid @enderror" id="school_logo" name="school_logo" accept="image/*">
                                @error('school_logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Recommended: 200x200px, PNG or JPG</small>
                            </div>
                            @if($settings['school_logo'] ?? null)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remove_logo" name="remove_logo" value="1">
                                    <label class="form-check-label text-danger" for="remove_logo">
                                        Remove current logo
                                    </label>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- School Information -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5>School Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="school_name" class="form-label">School Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('school_name') is-invalid @enderror" id="school_name" name="school_name" value="{{ old('school_name', $settings['school_name'] ?? '') }}" required>
                                        @error('school_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="school_tagline" class="form-label">Tagline / Motto</label>
                                        <input type="text" class="form-control @error('school_tagline') is-invalid @enderror" id="school_tagline" name="school_tagline" value="{{ old('school_tagline', $settings['school_tagline'] ?? '') }}" placeholder="e.g., Excellence in Education">
                                        @error('school_tagline')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="school_address" class="form-label">Address <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('school_address') is-invalid @enderror" id="school_address" name="school_address" rows="2" required>{{ old('school_address', $settings['school_address'] ?? '') }}</textarea>
                                @error('school_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="school_phone" class="form-label">Phone <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('school_phone') is-invalid @enderror" id="school_phone" name="school_phone" value="{{ old('school_phone', $settings['school_phone'] ?? '') }}" required>
                                        @error('school_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="school_email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('school_email') is-invalid @enderror" id="school_email" name="school_email" value="{{ old('school_email', $settings['school_email'] ?? '') }}" required>
                                        @error('school_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="school_website" class="form-label">Website</label>
                                        <input type="text" class="form-control @error('school_website') is-invalid @enderror" id="school_website" name="school_website" value="{{ old('school_website', $settings['school_website'] ?? '') }}" placeholder="www.school.edu">
                                        @error('school_website')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview Card -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5>Print Preview</h5>
                </div>
                <div class="card-body">
                    <div class="border rounded p-3 bg-white">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                @if($settings['school_logo'] ?? null)
                                    <img src="{{ asset('storage/' . $settings['school_logo']) }}" alt="Logo" style="width: 60px; height: 60px; object-fit: contain;">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <i data-feather="image" class="text-muted"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="text-center flex-grow-1">
                                <h4 class="mb-0 text-uppercase fw-bold">{{ $settings['school_name'] ?? 'School Name' }}</h4>
                                @if($settings['school_tagline'] ?? null)
                                    <small class="text-muted fst-italic">{{ $settings['school_tagline'] }}</small><br>
                                @endif
                                <small>{{ $settings['school_address'] ?? 'School Address' }}</small><br>
                                <small>Phone: {{ $settings['school_phone'] ?? 'Phone' }} | Email: {{ $settings['school_email'] ?? 'Email' }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i data-feather="save" class="icon-xs"></i> Save Settings
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
    // Preview logo before upload
    jQuery('#school_logo').on('change', function() {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                jQuery('#logo-preview').attr('src', e.target.result).removeClass('d-none');
                jQuery('#logo-placeholder').addClass('d-none');
            }
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endpush
