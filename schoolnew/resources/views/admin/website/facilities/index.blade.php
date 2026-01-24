@extends('layouts.app')

@section('title', 'Facilities')

@section('page-title', 'Website - Facilities')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.website.index') }}">Website</a></li>
    <li class="breadcrumb-item active">Facilities</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>School Facilities</h5>
                    <a href="{{ route('admin.website.facilities.create') }}" class="btn btn-primary">
                        <i data-feather="plus" class="me-1"></i> Add Facility
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
                                <th style="width: 60px;">Order</th>
                                <th>Icon/Image</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th style="width: 120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($facilities as $facility)
                                <tr>
                                    <td>{{ $facility->sort_order }}</td>
                                    <td>
                                        @if($facility->image)
                                            <img src="{{ asset('storage/' . $facility->image) }}" alt="{{ $facility->title }}" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                        @elseif($facility->icon)
                                            <i data-feather="{{ $facility->icon }}" style="width: 32px; height: 32px;"></i>
                                        @else
                                            <i data-feather="star" style="width: 32px; height: 32px;"></i>
                                        @endif
                                    </td>
                                    <td>{{ $facility->title }}</td>
                                    <td>{{ Str::limit($facility->description, 60) }}</td>
                                    <td>
                                        @if($facility->is_active)
                                            <span class="badge badge-light-success">Active</span>
                                        @else
                                            <span class="badge badge-light-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.website.facilities.edit', $facility) }}" class="btn btn-sm btn-outline-primary">
                                                <i data-feather="edit-2" style="width: 14px;"></i>
                                            </a>
                                            <form action="{{ route('admin.website.facilities.destroy', $facility) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-confirm" data-name="{{ $facility->title }}">
                                                    <i data-feather="trash-2" style="width: 14px;"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <p class="text-muted mb-0">No facilities found. <a href="{{ route('admin.website.facilities.create') }}">Add one now</a></p>
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
