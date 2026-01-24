@extends('layouts.app')

@section('title', 'Edit Page')

@section('page-title', 'Website - Edit Page')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.website.index') }}">Website</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.website.pages') }}">Pages</a></li>
    <li class="breadcrumb-item active">Edit {{ ucfirst($page->slug) }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Edit {{ ucfirst($page->slug) }} Page</h5>
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

                <form action="{{ route('admin.website.pages.update', $page) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Page Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" value="{{ old('title', $page->title) }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Page Content</label>
                                <textarea name="content" class="form-control" rows="15" id="editor">{{ old('content', $page->content) }}</textarea>
                                <small class="text-muted">Use HTML formatting for rich content</small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Banner Image</label>
                                @if($page->banner_image)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $page->banner_image) }}" alt="{{ $page->title }}" class="img-thumbnail w-100">
                                    </div>
                                @endif
                                <input type="file" name="banner_image" class="form-control" accept="image/*">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Meta Description</label>
                                <textarea name="meta_description" class="form-control" rows="3">{{ old('meta_description', $page->meta_description) }}</textarea>
                                <small class="text-muted">For SEO (max 160 characters)</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Meta Keywords</label>
                                <input type="text" name="meta_keywords" class="form-control" value="{{ old('meta_keywords', $page->meta_keywords) }}">
                                <small class="text-muted">Comma separated keywords</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <div class="form-check form-switch mt-2">
                                    <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ old('is_active', $page->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="me-1"></i> Update Page
                        </button>
                        <a href="{{ route('admin.website.pages') }}" class="btn btn-secondary">
                            <i data-feather="x" class="me-1"></i> Cancel
                        </a>
                        <a href="{{ route('website.' . ($page->slug === 'home' ? 'home' : $page->slug)) }}" target="_blank" class="btn btn-outline-primary ms-auto">
                            <i data-feather="external-link" class="me-1"></i> View Page
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
