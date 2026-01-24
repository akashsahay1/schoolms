@extends('layouts.app')

@section('title', 'Edit Slider')

@section('page-title', 'Website - Edit Slider')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.website.index') }}">Website</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.website.sliders') }}">Sliders</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Edit Slider</h5>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.website.sliders.update', $slider) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Current Image</label>
                        @if($slider->image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $slider->image) }}" alt="Current Slider" class="img-thumbnail" style="max-height: 150px;">
                            </div>
                        @endif
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted">Leave empty to keep current image. Recommended size: 1920x600px. Max 2MB.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $slider->title) }}" placeholder="Welcome to Our School">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Subtitle</label>
                        <textarea name="subtitle" class="form-control" rows="2" placeholder="Brief description...">{{ old('subtitle', $slider->subtitle) }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Button Text</label>
                            <input type="text" name="button_text" class="form-control" value="{{ old('button_text', $slider->button_text) }}" placeholder="Learn More">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Button Link</label>
                            <input type="text" name="button_link" class="form-control" value="{{ old('button_link', $slider->button_link) }}" placeholder="/about">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $slider->sort_order) }}" min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <div class="form-check form-switch mt-2">
                                <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ old('is_active', $slider->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="me-1"></i> Update Slider
                        </button>
                        <a href="{{ route('admin.website.sliders') }}" class="btn btn-secondary">
                            <i data-feather="x" class="me-1"></i> Cancel
                        </a>
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
    if (typeof feather !== 'undefined') feather.replace();
});
</script>
@endpush
