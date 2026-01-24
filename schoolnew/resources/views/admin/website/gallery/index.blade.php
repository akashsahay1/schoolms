@extends('layouts.app')

@section('title', 'Gallery')

@section('page-title', 'Website - Gallery')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.website.index') }}">Website</a></li>
    <li class="breadcrumb-item active">Gallery</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>Photo Gallery</h5>
                    <a href="{{ route('admin.website.gallery.create') }}" class="btn btn-primary">
                        <i data-feather="plus" class="me-1"></i> Add Image
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="row g-4">
                    @forelse($gallery as $item)
                        <div class="col-lg-3 col-md-4 col-6">
                            <div class="card border h-100">
                                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}" class="card-img-top" style="height: 150px; object-fit: cover;">
                                <div class="card-body p-2">
                                    <h6 class="card-title mb-1">{{ Str::limit($item->title, 25) }}</h6>
                                    @if($item->category)
                                        <small class="text-muted">{{ $item->category }}</small>
                                    @endif
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        @if($item->is_active)
                                            <span class="badge badge-light-success">Active</span>
                                        @else
                                            <span class="badge badge-light-danger">Inactive</span>
                                        @endif
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('admin.website.gallery.edit', $item) }}" class="btn btn-sm btn-outline-primary p-1">
                                                <i data-feather="edit-2" style="width: 12px;"></i>
                                            </a>
                                            <form action="{{ route('admin.website.gallery.destroy', $item) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-outline-danger p-1 delete-confirm" data-name="{{ $item->title }}">
                                                    <i data-feather="trash-2" style="width: 12px;"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i data-feather="image" class="text-muted mb-3" style="width: 64px; height: 64px;"></i>
                                <h5 class="text-muted">No gallery images found</h5>
                                <a href="{{ route('admin.website.gallery.create') }}" class="btn btn-primary mt-3">Add Image</a>
                            </div>
                        </div>
                    @endforelse
                </div>
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
