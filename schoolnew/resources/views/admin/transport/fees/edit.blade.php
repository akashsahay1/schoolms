@extends('layouts.app')

@section('title', 'Edit Transport Fee')

@section('page-title', 'Transport - Edit Fee')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.transport.fees.index') }}">Transport Fees</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Edit Transport Fee</h5>
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

                <form action="{{ route('admin.transport.fees.update', $fee) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                            <select name="academic_year_id" class="form-select @error('academic_year_id') is-invalid @enderror" required>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ old('academic_year_id', $fee->academic_year_id) == $year->id ? 'selected' : '' }}>
                                        {{ $year->name }} {{ $year->is_current ? '(Current)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('academic_year_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Route <span class="text-danger">*</span></label>
                            <select name="transport_route_id" class="form-select @error('transport_route_id') is-invalid @enderror" required>
                                <option value="">Select Route</option>
                                @foreach($routes as $route)
                                    <option value="{{ $route->id }}" {{ old('transport_route_id', $fee->transport_route_id) == $route->id ? 'selected' : '' }}>
                                        {{ $route->title }} ({{ $route->start_point }} - {{ $route->end_point }})
                                    </option>
                                @endforeach
                            </select>
                            @error('transport_route_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fee Type <span class="text-danger">*</span></label>
                            <select name="fee_type" class="form-select @error('fee_type') is-invalid @enderror" required>
                                <option value="monthly" {{ old('fee_type', $fee->fee_type) === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="quarterly" {{ old('fee_type', $fee->fee_type) === 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                <option value="half_yearly" {{ old('fee_type', $fee->fee_type) === 'half_yearly' ? 'selected' : '' }}>Half Yearly</option>
                                <option value="yearly" {{ old('fee_type', $fee->fee_type) === 'yearly' ? 'selected' : '' }}>Yearly</option>
                                <option value="one_time" {{ old('fee_type', $fee->fee_type) === 'one_time' ? 'selected' : '' }}>One Time</option>
                            </select>
                            @error('fee_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $fee->amount) }}" step="0.01" min="0" required>
                            </div>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fine Per Day</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="fine_per_day" class="form-control @error('fine_per_day') is-invalid @enderror" value="{{ old('fine_per_day', $fee->fine_per_day) }}" step="0.01" min="0">
                            </div>
                            @error('fine_per_day')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Grace Days</label>
                            <input type="number" name="fine_grace_days" class="form-control @error('fine_grace_days') is-invalid @enderror" value="{{ old('fine_grace_days', $fee->fine_grace_days) }}" min="0">
                            @error('fine_grace_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Due Date</label>
                            <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date', $fee->due_date?->format('Y-m-d')) }}">
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="2">{{ old('description', $fee->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ old('is_active', $fee->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="me-1"></i> Update Fee
                        </button>
                        <a href="{{ route('admin.transport.fees.index') }}" class="btn btn-secondary">
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
