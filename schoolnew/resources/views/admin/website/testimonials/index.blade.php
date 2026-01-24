@extends('layouts.app')

@section('title', 'Testimonials')

@section('page-title', 'Website - Testimonials')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.website.index') }}">Website</a></li>
    <li class="breadcrumb-item active">Testimonials</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>Testimonials</h5>
                    <a href="{{ route('admin.website.testimonials.create') }}" class="btn btn-primary">
                        <i data-feather="plus" class="me-1"></i> Add Testimonial
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
                                <th style="width: 60px;">Photo</th>
                                <th>Name</th>
                                <th>Designation</th>
                                <th>Content</th>
                                <th>Rating</th>
                                <th>Status</th>
                                <th style="width: 120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($testimonials as $testimonial)
                                <tr>
                                    <td>
                                        @if($testimonial->photo)
                                            <img src="{{ asset('storage/' . $testimonial->photo) }}" alt="{{ $testimonial->name }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                {{ substr($testimonial->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $testimonial->name }}</td>
                                    <td>{{ $testimonial->designation ?? '-' }}</td>
                                    <td>{{ Str::limit($testimonial->content, 60) }}</td>
                                    <td>
                                        @for($i = 1; $i <= 5; $i++)
                                            <i data-feather="star" class="{{ $i <= $testimonial->rating ? 'text-warning' : 'text-muted' }}" style="width: 14px; fill: {{ $i <= $testimonial->rating ? 'currentColor' : 'none' }};"></i>
                                        @endfor
                                    </td>
                                    <td>
                                        @if($testimonial->is_active)
                                            <span class="badge badge-light-success">Active</span>
                                        @else
                                            <span class="badge badge-light-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.website.testimonials.edit', $testimonial) }}" class="btn btn-sm btn-outline-primary">
                                                <i data-feather="edit-2" style="width: 14px;"></i>
                                            </a>
                                            <form action="{{ route('admin.website.testimonials.destroy', $testimonial) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-confirm" data-name="{{ $testimonial->name }}">
                                                    <i data-feather="trash-2" style="width: 14px;"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <p class="text-muted mb-0">No testimonials found. <a href="{{ route('admin.website.testimonials.create') }}">Add one now</a></p>
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
