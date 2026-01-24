@extends('layouts.app')

@section('title', 'Edit Leave Type')

@section('page-title', 'Staff Leaves - Edit Leave Type')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.staff-leaves.types.index') }}">Leave Types</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Edit Leave Type</h5>
            </div>
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('admin.staff-leaves.types.update', $type) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Leave Type Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $type->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Code <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $type->code) }}" required maxlength="20">
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Allowed Days per Year <span class="text-danger">*</span></label>
                            <input type="number" name="allowed_days" class="form-control @error('allowed_days') is-invalid @enderror" value="{{ old('allowed_days', $type->allowed_days) }}" min="0" max="365" required>
                            @error('allowed_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Applicable To <span class="text-danger">*</span></label>
                            <select name="applicable_to" class="form-select @error('applicable_to') is-invalid @enderror" required>
                                <option value="all" {{ old('applicable_to', $type->applicable_to) == 'all' ? 'selected' : '' }}>Both Staff & Students</option>
                                <option value="staff" {{ old('applicable_to', $type->applicable_to) == 'staff' ? 'selected' : '' }}>Staff Only</option>
                                <option value="students" {{ old('applicable_to', $type->applicable_to) == 'students' ? 'selected' : '' }}>Students Only</option>
                            </select>
                            @error('applicable_to')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $type->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_paid" id="is_paid" class="form-check-input" value="1" {{ old('is_paid', $type->is_paid) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_paid">Paid Leave</label>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="requires_attachment" id="requires_attachment" class="form-check-input" value="1" {{ old('requires_attachment', $type->requires_attachment) ? 'checked' : '' }}>
                                <label class="form-check-label" for="requires_attachment">Requires Attachment</label>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', $type->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="me-1"></i> Update Leave Type
                        </button>
                        <a href="{{ route('admin.staff-leaves.types.index') }}" class="btn btn-secondary">
                            <i data-feather="x" class="me-1"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Information</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-2">Created: {{ $type->created_at->format('M d, Y h:i A') }}</p>
                <p class="text-muted mb-0">Updated: {{ $type->updated_at->format('M d, Y h:i A') }}</p>
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
