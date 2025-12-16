@extends('layouts.app')

@section('title', 'Duplicate Fee Structure')

@section('page-title', 'Duplicate Fee Structure')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.fees.structure') }}">Fee Structure</a></li>
    <li class="breadcrumb-item active">Duplicate</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5>Duplicate Fee Structure</h5>
            </div>
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="alert alert-info">
                    <h6>Source Fee Structure:</h6>
                    <p class="mb-1"><strong>Fee Type:</strong> {{ $feeStructure->feeType->name }}</p>
                    <p class="mb-1"><strong>Academic Year:</strong> {{ $feeStructure->academicYear->name }}</p>
                    <p class="mb-1"><strong>Class:</strong> {{ $feeStructure->schoolClass->name }}</p>
                    <p class="mb-0"><strong>Amount:</strong> ₹{{ number_format($feeStructure->amount, 2) }}</p>
                </div>

                <form action="{{ route('admin.fees.structure.duplicate.store', $feeStructure) }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    
                    <div class="mb-3">
                        <label for="target_academic_year_id" class="form-label">Target Academic Year <span class="text-danger">*</span></label>
                        <select class="form-select @error('target_academic_year_id') is-invalid @enderror" 
                                id="target_academic_year_id" name="target_academic_year_id" required>
                            <option value="">Select Academic Year</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ old('target_academic_year_id') == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }}
                                    @if($year->is_active)
                                        (Active)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('target_academic_year_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="target_class_id" class="form-label">Target Class <span class="text-danger">*</span></label>
                        <select class="form-select @error('target_class_id') is-invalid @enderror" 
                                id="target_class_id" name="target_class_id" required>
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('target_class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('target_class_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-warning">
                        <h6><svg class="icon-xs me-1">
                            <use href="{{ asset('assets/svg/icon-sprite.svg#alert-triangle') }}"></use>
                        </svg> Important Notes:</h6>
                        <ul class="mb-0">
                            <li>This will create a copy of the fee structure for the selected academic year and class.</li>
                            <li>All details (amount, due date, fine settings) will be copied exactly.</li>
                            <li>If a fee structure for this fee type already exists for the target class and academic year, duplication will fail.</li>
                            <li>You can modify the duplicated fee structure after creation if needed.</li>
                        </ul>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('admin.fees.structure') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="copy" class="icon-xs"></i> Duplicate Fee Structure
                        </button>
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