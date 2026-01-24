@extends('layouts.app')

@section('title', 'Edit Driver')

@section('page-title', 'Transport - Edit Driver')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.drivers.index') }}">Drivers</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <form action="{{ route('admin.drivers.update', $driver) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-header">
                    <h5>Personal Information</h5>
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

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Employee ID <span class="text-danger">*</span></label>
                            <input type="text" name="employee_id" class="form-control @error('employee_id') is-invalid @enderror" value="{{ old('employee_id', $driver->employee_id) }}" required>
                            @error('employee_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $driver->first_name) }}" required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $driver->last_name) }}" required>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                <option value="male" {{ old('gender', $driver->gender) === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $driver->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender', $driver->gender) === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Phone <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $driver->phone) }}" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Alternate Phone</label>
                            <input type="text" name="alternate_phone" class="form-control @error('alternate_phone') is-invalid @enderror" value="{{ old('alternate_phone', $driver->alternate_phone) }}">
                            @error('alternate_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $driver->email) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth', $driver->date_of_birth?->format('Y-m-d')) }}">
                            @error('date_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Blood Group</label>
                            <select name="blood_group" class="form-select @error('blood_group') is-invalid @enderror">
                                <option value="">Select Blood Group</option>
                                <option value="A+" {{ old('blood_group', $driver->blood_group) === 'A+' ? 'selected' : '' }}>A+</option>
                                <option value="A-" {{ old('blood_group', $driver->blood_group) === 'A-' ? 'selected' : '' }}>A-</option>
                                <option value="B+" {{ old('blood_group', $driver->blood_group) === 'B+' ? 'selected' : '' }}>B+</option>
                                <option value="B-" {{ old('blood_group', $driver->blood_group) === 'B-' ? 'selected' : '' }}>B-</option>
                                <option value="AB+" {{ old('blood_group', $driver->blood_group) === 'AB+' ? 'selected' : '' }}>AB+</option>
                                <option value="AB-" {{ old('blood_group', $driver->blood_group) === 'AB-' ? 'selected' : '' }}>AB-</option>
                                <option value="O+" {{ old('blood_group', $driver->blood_group) === 'O+' ? 'selected' : '' }}>O+</option>
                                <option value="O-" {{ old('blood_group', $driver->blood_group) === 'O-' ? 'selected' : '' }}>O-</option>
                            </select>
                            @error('blood_group')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-9 mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address', $driver->address) }}">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>License & Employment</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">License Number <span class="text-danger">*</span></label>
                            <input type="text" name="license_number" class="form-control @error('license_number') is-invalid @enderror" value="{{ old('license_number', $driver->license_number) }}" required>
                            @error('license_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">License Type</label>
                            <select name="license_type" class="form-select @error('license_type') is-invalid @enderror">
                                <option value="">Select Type</option>
                                <option value="LMV" {{ old('license_type', $driver->license_type) === 'LMV' ? 'selected' : '' }}>LMV (Light Motor Vehicle)</option>
                                <option value="HMV" {{ old('license_type', $driver->license_type) === 'HMV' ? 'selected' : '' }}>HMV (Heavy Motor Vehicle)</option>
                                <option value="Transport" {{ old('license_type', $driver->license_type) === 'Transport' ? 'selected' : '' }}>Transport</option>
                                <option value="Commercial" {{ old('license_type', $driver->license_type) === 'Commercial' ? 'selected' : '' }}>Commercial</option>
                            </select>
                            @error('license_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">License Expiry <span class="text-danger">*</span></label>
                            <input type="date" name="license_expiry" class="form-control @error('license_expiry') is-invalid @enderror" value="{{ old('license_expiry', $driver->license_expiry->format('Y-m-d')) }}" required>
                            @error('license_expiry')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($driver->isLicenseExpired())
                                <small class="text-danger">License has expired!</small>
                            @elseif($driver->isLicenseExpiringSoon())
                                <small class="text-warning">License expiring soon!</small>
                            @endif
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Joining Date <span class="text-danger">*</span></label>
                            <input type="date" name="joining_date" class="form-control @error('joining_date') is-invalid @enderror" value="{{ old('joining_date', $driver->joining_date->format('Y-m-d')) }}" required>
                            @error('joining_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Salary</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="salary" class="form-control @error('salary') is-invalid @enderror" value="{{ old('salary', $driver->salary) }}" step="0.01" min="0">
                            </div>
                            @error('salary')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Emergency Contact Name</label>
                            <input type="text" name="emergency_contact_name" class="form-control @error('emergency_contact_name') is-invalid @enderror" value="{{ old('emergency_contact_name', $driver->emergency_contact_name) }}">
                            @error('emergency_contact_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Emergency Contact Phone</label>
                            <input type="text" name="emergency_contact_phone" class="form-control @error('emergency_contact_phone') is-invalid @enderror" value="{{ old('emergency_contact_phone', $driver->emergency_contact_phone) }}">
                            @error('emergency_contact_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3 d-flex align-items-end">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ old('is_active', $driver->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>Documents & Notes</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Photo</label>
                            @if($driver->photo)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $driver->photo) }}" alt="Driver Photo" class="rounded" style="max-height: 80px;">
                                </div>
                            @endif
                            <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/*">
                            <small class="text-muted">Accepted: JPEG, PNG. Max 2MB. Leave empty to keep current.</small>
                            @error('photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">License Document</label>
                            @if($driver->license_document)
                                <div class="mb-2">
                                    <a href="{{ asset('storage/' . $driver->license_document) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                        <i data-feather="file" class="me-1" style="width: 14px;"></i> View Current
                                    </a>
                                </div>
                            @endif
                            <input type="file" name="license_document" class="form-control @error('license_document') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">Accepted: PDF, JPEG, PNG. Max 5MB.</small>
                            @error('license_document')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">ID Proof Document</label>
                            @if($driver->id_proof_document)
                                <div class="mb-2">
                                    <a href="{{ asset('storage/' . $driver->id_proof_document) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                        <i data-feather="file" class="me-1" style="width: 14px;"></i> View Current
                                    </a>
                                </div>
                            @endif
                            <input type="file" name="id_proof_document" class="form-control @error('id_proof_document') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">Accepted: PDF, JPEG, PNG. Max 5MB.</small>
                            @error('id_proof_document')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $driver->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="me-1"></i> Update Driver
                        </button>
                        <a href="{{ route('admin.drivers.index') }}" class="btn btn-secondary">
                            <i data-feather="x" class="me-1"></i> Cancel
                        </a>
                    </div>
                </div>
            </div>
        </form>
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
