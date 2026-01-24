@extends('layouts.app')

@section('title', 'Homepage Sliders')

@section('page-title', 'Website - Homepage Sliders')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.website.index') }}">Website</a></li>
    <li class="breadcrumb-item active">Sliders</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>Homepage Sliders</h5>
                    <a href="{{ route('admin.website.sliders.create') }}" class="btn btn-primary">
                        <i data-feather="plus" class="me-1"></i> Add Slider
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Order</th>
                                <th style="width: 120px;">Image</th>
                                <th>Title</th>
                                <th>Subtitle</th>
                                <th>Button</th>
                                <th>Status</th>
                                <th style="width: 120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sliders as $slider)
                                <tr>
                                    <td>{{ $slider->sort_order }}</td>
                                    <td>
                                        @if($slider->image)
                                            <img src="{{ asset('storage/' . $slider->image) }}" alt="Slider" class="img-thumbnail" style="width: 100px; height: 60px; object-fit: cover;">
                                        @else
                                            <span class="text-muted">No image</span>
                                        @endif
                                    </td>
                                    <td>{{ $slider->title ?? '-' }}</td>
                                    <td>{{ Str::limit($slider->subtitle, 50) ?? '-' }}</td>
                                    <td>
                                        @if($slider->button_text)
                                            <span class="badge badge-light-info">{{ $slider->button_text }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($slider->is_active)
                                            <span class="badge badge-light-success">Active</span>
                                        @else
                                            <span class="badge badge-light-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.website.sliders.edit', $slider) }}" class="btn btn-sm btn-outline-primary">
                                                <i data-feather="edit-2" style="width: 14px;"></i>
                                            </a>
                                            <form action="{{ route('admin.website.sliders.destroy', $slider) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-confirm" data-name="this slider">
                                                    <i data-feather="trash-2" style="width: 14px;"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <p class="text-muted mb-0">No sliders found. <a href="{{ route('admin.website.sliders.create') }}">Add one now</a></p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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
