@extends('layouts.app')

@section('title', 'Edit Fee Structure')

@section('page-title', 'Edit Fee Structure')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.fees.structure') }}">Fee Structure</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Edit Fee Structure</h5>
            </div>
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('admin.fees.structure.update', $feeStructure) }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Academic Year</label>
                                <input type="text" class="form-control" value="{{ $feeStructure->academicYear->name }}" readonly>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Class</label>
                                <input type="text" class="form-control" value="{{ $feeStructure->schoolClass->name }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Fee Type</label>
                                <input type="text" class="form-control" value="{{ $feeStructure->feeType->name }} ({{ $feeStructure->feeType->code }})" readonly>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Fee Group</label>
                                <input type="text" class="form-control" value="{{ $feeStructure->feeGroup->name }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                       id="amount" name="amount" value="{{ old('amount', $feeStructure->amount) }}" 
                                       min="0" step="0.01" required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="due_date" class="form-label">Due Date</label>
                                <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                       id="due_date" name="due_date" 
                                       value="{{ old('due_date', $feeStructure->due_date ? $feeStructure->due_date->format('Y-m-d') : '') }}">
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fine_type" class="form-label">Fine Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('fine_type') is-invalid @enderror" 
                                        id="fine_type" name="fine_type" required>
                                    <option value="none" {{ old('fine_type', $feeStructure->fine_type) == 'none' ? 'selected' : '' }}>No Fine</option>
                                    <option value="percentage" {{ old('fine_type', $feeStructure->fine_type) == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                    <option value="fixed" {{ old('fine_type', $feeStructure->fine_type) == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                </select>
                                @error('fine_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fine_amount" class="form-label">Fine Amount</label>
                                <input type="number" class="form-control @error('fine_amount') is-invalid @enderror" 
                                       id="fine_amount" name="fine_amount" value="{{ old('fine_amount', $feeStructure->fine_amount) }}" 
                                       min="0" step="0.01" {{ $feeStructure->fine_type == 'none' ? 'disabled' : '' }}>
                                @error('fine_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Enter percentage or fixed amount based on fine type</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description', $feeStructure->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                   value="1" {{ old('is_active', $feeStructure->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('admin.fees.structure') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Fee Structure</button>
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
        // Enable/disable fine amount based on fine type
        jQuery('#fine_type').on('change', function() {
            if (jQuery(this).val() === 'none') {
                jQuery('#fine_amount').val(0).prop('disabled', true);
            } else {
                jQuery('#fine_amount').prop('disabled', false);
            }
        });

        // Form validation
        jQuery('.needs-validation').on('submit', function(e) {
            if (this.checkValidity() === false) {
                e.preventDefault();
                e.stopPropagation();
            }
            jQuery(this).addClass('was-validated');
        });
    });
</script>
@endpush