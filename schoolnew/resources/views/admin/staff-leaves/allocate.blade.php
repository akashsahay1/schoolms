@extends('layouts.app')

@section('title', 'Allocate Leave Balance')

@section('page-title', 'Staff Leaves - Allocate Balance')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.staff-leaves.balances') }}">Leave Balances</a></li>
    <li class="breadcrumb-item active">Allocate</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Allocate Leave Balance</h5>
            </div>
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('admin.staff-leaves.balances.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                            <select name="academic_year_id" class="form-select @error('academic_year_id') is-invalid @enderror" required>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ old('academic_year_id', $currentAcademicYear?->id) == $year->id ? 'selected' : '' }}>
                                        {{ $year->name }} {{ $year->is_current ? '(Current)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('academic_year_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Leave Type <span class="text-danger">*</span></label>
                            <select name="leave_type_id" id="leave_type_id" class="form-select @error('leave_type_id') is-invalid @enderror" required>
                                <option value="">Select Leave Type</option>
                                @foreach($leaveTypes as $type)
                                    <option value="{{ $type->id }}" data-days="{{ $type->allowed_days }}" {{ old('leave_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }} ({{ $type->allowed_days }} days)
                                    </option>
                                @endforeach
                            </select>
                            @error('leave_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Allocated Days <span class="text-danger">*</span></label>
                            <input type="number" name="allocated_days" id="allocated_days" class="form-control @error('allocated_days') is-invalid @enderror" value="{{ old('allocated_days', 0) }}" min="0" max="365" required>
                            @error('allocated_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Carried Forward Days</label>
                            <input type="number" name="carried_forward" class="form-control @error('carried_forward') is-invalid @enderror" value="{{ old('carried_forward', 0) }}" min="0" max="365">
                            @error('carried_forward')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Days carried from previous year</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Select Staff Members <span class="text-danger">*</span></label>
                        <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                            <div class="mb-2">
                                <input type="checkbox" id="selectAllStaff" class="form-check-input">
                                <label class="form-check-label fw-bold" for="selectAllStaff">Select All</label>
                            </div>
                            <hr>
                            @foreach($staff as $member)
                                <div class="form-check mb-2">
                                    <input type="checkbox" name="staff_ids[]" value="{{ $member->id }}" class="form-check-input staff-checkbox" id="staff_{{ $member->id }}" {{ in_array($member->id, old('staff_ids', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="staff_{{ $member->id }}">
                                        {{ $member->first_name }} {{ $member->last_name }}
                                        @if($member->department) <span class="text-muted">({{ $member->department->name }})</span> @endif
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('staff_ids')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="me-1"></i> Allocate Leave
                        </button>
                        <a href="{{ route('admin.staff-leaves.balances') }}" class="btn btn-secondary">
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
                <h5>Instructions</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><i data-feather="info" class="text-info me-2" style="width: 16px; height: 16px;"></i> Select leave type first</li>
                    <li class="mb-2"><i data-feather="info" class="text-info me-2" style="width: 16px; height: 16px;"></i> Enter days to allocate</li>
                    <li class="mb-2"><i data-feather="info" class="text-info me-2" style="width: 16px; height: 16px;"></i> Select one or more staff</li>
                    <li class="mb-2"><i data-feather="info" class="text-info me-2" style="width: 16px; height: 16px;"></i> Existing balances will be updated</li>
                    <li><i data-feather="alert-circle" class="text-warning me-2" style="width: 16px; height: 16px;"></i> Used days won't be affected</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
    // Select all checkbox
    jQuery('#selectAllStaff').on('change', function() {
        jQuery('.staff-checkbox').prop('checked', jQuery(this).is(':checked'));
    });

    // Update allocated days based on leave type
    jQuery('#leave_type_id').on('change', function() {
        var days = jQuery(this).find('option:selected').data('days');
        if (days) {
            jQuery('#allocated_days').val(days);
        }
    });

    if (typeof feather !== 'undefined') feather.replace();
});
</script>
@endpush
