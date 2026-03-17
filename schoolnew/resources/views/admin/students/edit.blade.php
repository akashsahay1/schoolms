@extends('layouts.app')

@section('title', 'Edit Student')

@section('page-title', 'Edit Student')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}">Students</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@push('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/flatpickr/flatpickr.min.css') }}">
@endpush

@section('content')
@php
    $fs = $fieldSettings ?? [];
    $isVisible = function($field) use ($fs) {
        return ($fs[$field]['visible'] ?? true);
    };
    $isRequired = function($field) use ($fs) {
        return ($fs[$field]['required'] ?? false) && ($fs[$field]['visible'] ?? true);
    };
@endphp
<form method="POST" action="{{ route('admin.students.update', $student) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row">
        <!-- Basic Information -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5>Student Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name', $student->first_name) }}" required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @if($isVisible('last_name'))
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Name @if($isRequired('last_name'))<span class="text-danger">*</span>@endif</label>
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name', $student->last_name) }}" {{ $isRequired('last_name') ? 'required' : '' }}>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Gender <span class="text-danger">*</span></label>
                            <select class="form-select @error('gender') is-invalid @enderror" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender', $student->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $student->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender', $student->gender) == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                            <input type="text" class="form-control flatpickr-input @error('date_of_birth') is-invalid @enderror" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $student->date_of_birth?->format('d-m-Y')) }}" placeholder="Select Date" required>
                            @error('date_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @if($isVisible('blood_group'))
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Blood Group @if($isRequired('blood_group'))<span class="text-danger">*</span>@endif</label>
                            <select class="form-select @error('blood_group') is-invalid @enderror" name="blood_group" {{ $isRequired('blood_group') ? 'required' : '' }}>
                                <option value="">Select</option>
                                @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                    <option value="{{ $bg }}" {{ old('blood_group', $student->blood_group) == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    </div>

                    <div class="row">
                        @if($isVisible('religion'))
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Religion @if($isRequired('religion'))<span class="text-danger">*</span>@endif</label>
                            <select class="form-select @error('religion') is-invalid @enderror" name="religion" {{ $isRequired('religion') ? 'required' : '' }}>
                                <option value="">Select Religion</option>
                                <option value="Hindu" {{ old('religion', $student->religion) == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                <option value="Muslim" {{ old('religion', $student->religion) == 'Muslim' ? 'selected' : '' }}>Muslim</option>
                                <option value="Christian" {{ old('religion', $student->religion) == 'Christian' ? 'selected' : '' }}>Christian</option>
                                <option value="Sikh" {{ old('religion', $student->religion) == 'Sikh' ? 'selected' : '' }}>Sikh</option>
                                <option value="Buddhist" {{ old('religion', $student->religion) == 'Buddhist' ? 'selected' : '' }}>Buddhist</option>
                                <option value="Jain" {{ old('religion', $student->religion) == 'Jain' ? 'selected' : '' }}>Jain</option>
                                <option value="Other" {{ old('religion', $student->religion) == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        @endif
                        @if($isVisible('nationality'))
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Nationality @if($isRequired('nationality'))<span class="text-danger">*</span>@endif</label>
                            <input type="text" class="form-control @error('nationality') is-invalid @enderror" name="nationality" value="{{ old('nationality', $student->nationality) }}" {{ $isRequired('nationality') ? 'required' : '' }}>
                        </div>
                        @endif
                        @if($isVisible('mother_tongue'))
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Mother Tongue @if($isRequired('mother_tongue'))<span class="text-danger">*</span>@endif</label>
                            <input type="text" class="form-control @error('mother_tongue') is-invalid @enderror" name="mother_tongue" value="{{ old('mother_tongue', $student->mother_tongue) }}" {{ $isRequired('mother_tongue') ? 'required' : '' }}>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Academic Information -->
            <div class="card">
                <div class="card-header">
                    <h5>Academic Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Class <span class="text-danger">*</span></label>
                            <select class="form-select @error('class_id') is-invalid @enderror" name="class_id" id="classSelect" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id', $student->class_id) == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                @endforeach
                            </select>
                            @error('class_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Section <span class="text-danger">*</span></label>
                            <select class="form-select @error('section_id') is-invalid @enderror" name="section_id" id="sectionSelect" required>
                                <option value="">Select Section</option>
                            </select>
                            @error('section_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @if($isVisible('roll_no'))
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Roll No @if($isRequired('roll_no'))<span class="text-danger">*</span>@endif</label>
                            <input type="text" class="form-control @error('roll_no') is-invalid @enderror" name="roll_no" value="{{ old('roll_no', $student->roll_no) }}" {{ $isRequired('roll_no') ? 'required' : '' }}>
                        </div>
                        @endif
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Admission No</label>
                            <input type="text" class="form-control" value="{{ $student->admission_no }}" disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                <option value="active" {{ old('status', $student->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $student->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="graduated" {{ old('status', $student->status) == 'graduated' ? 'selected' : '' }}>Graduated</option>
                                <option value="transferred" {{ old('status', $student->status) == 'transferred' ? 'selected' : '' }}>Transferred</option>
                                <option value="expelled" {{ old('status', $student->status) == 'expelled' ? 'selected' : '' }}>Expelled</option>
                            </select>
                        </div>
                    </div>

                    @if($isVisible('previous_school'))
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Previous School @if($isRequired('previous_school'))<span class="text-danger">*</span>@endif</label>
                            <input type="text" class="form-control @error('previous_school') is-invalid @enderror" name="previous_school" value="{{ old('previous_school', $student->previous_school) }}" {{ $isRequired('previous_school') ? 'required' : '' }}>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Contact Information -->
            @if($isVisible('email') || $isVisible('phone') || $isVisible('current_address') || $isVisible('permanent_address'))
            <div class="card">
                <div class="card-header">
                    <h5>Contact Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($isVisible('email'))
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email @if($isRequired('email'))<span class="text-danger">*</span>@endif</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $student->email) }}" {{ $isRequired('email') ? 'required' : '' }}>
                        </div>
                        @endif
                        @if($isVisible('phone'))
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone @if($isRequired('phone'))<span class="text-danger">*</span>@endif</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', $student->phone) }}" {{ $isRequired('phone') ? 'required' : '' }}>
                        </div>
                        @endif
                    </div>

                    <div class="row">
                        @if($isVisible('current_address'))
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Current Address @if($isRequired('current_address'))<span class="text-danger">*</span>@endif</label>
                            <textarea class="form-control @error('current_address') is-invalid @enderror" name="current_address" rows="3" {{ $isRequired('current_address') ? 'required' : '' }}>{{ old('current_address', $student->current_address) }}</textarea>
                        </div>
                        @endif
                        @if($isVisible('permanent_address'))
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Permanent Address @if($isRequired('permanent_address'))<span class="text-danger">*</span>@endif</label>
                            <textarea class="form-control @error('permanent_address') is-invalid @enderror" name="permanent_address" rows="3" {{ $isRequired('permanent_address') ? 'required' : '' }}>{{ old('permanent_address', $student->permanent_address) }}</textarea>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Aadhaar Card Details -->
            @if($isVisible('aadhaar_number') || $isVisible('aadhaar_front') || $isVisible('aadhaar_back'))
            <div class="card">
                <div class="card-header">
                    <h5>Aadhaar Card Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($isVisible('aadhaar_number'))
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Aadhaar Card Number @if($isRequired('aadhaar_number'))<span class="text-danger">*</span>@endif</label>
                            <input type="text" class="form-control @error('aadhaar_number') is-invalid @enderror" name="aadhaar_number" value="{{ old('aadhaar_number', $student->aadhaar_number) }}" placeholder="Enter 12-digit Aadhaar number" maxlength="12" {{ $isRequired('aadhaar_number') ? 'required' : '' }}>
                            @error('aadhaar_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif
                        @if($isVisible('aadhaar_front'))
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Aadhaar Card Front @if($isRequired('aadhaar_front') && !$student->aadhaar_front)<span class="text-danger">*</span>@endif</label>
                            <input type="file" class="form-control @error('aadhaar_front') is-invalid @enderror" name="aadhaar_front" accept="image/*,.pdf" {{ $isRequired('aadhaar_front') && !$student->aadhaar_front ? 'required' : '' }}>
                            @error('aadhaar_front')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($student->aadhaar_front)
                                <small class="text-success"><a href="{{ asset('storage/' . $student->aadhaar_front) }}" target="_blank">View current file</a></small>
                            @else
                                <small class="text-muted">JPG, PNG or PDF (max 2MB)</small>
                            @endif
                        </div>
                        @endif
                        @if($isVisible('aadhaar_back'))
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Aadhaar Card Back @if($isRequired('aadhaar_back') && !$student->aadhaar_back)<span class="text-danger">*</span>@endif</label>
                            <input type="file" class="form-control @error('aadhaar_back') is-invalid @enderror" name="aadhaar_back" accept="image/*,.pdf" {{ $isRequired('aadhaar_back') && !$student->aadhaar_back ? 'required' : '' }}>
                            @error('aadhaar_back')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($student->aadhaar_back)
                                <small class="text-success"><a href="{{ asset('storage/' . $student->aadhaar_back) }}" target="_blank">View current file</a></small>
                            @else
                                <small class="text-muted">JPG, PNG or PDF (max 2MB)</small>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Parent Information -->
            <div class="card">
                <div class="card-header">
                    <h5>Parent/Guardian Information</h5>
                </div>
                <div class="card-body">
                    <h6 class="mb-3 text-primary">Father's Details</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Father's Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('father_name') is-invalid @enderror" name="father_name" value="{{ old('father_name', $student->parent?->father_name) }}" required>
                            @error('father_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @if($isVisible('father_phone'))
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Father's Phone @if($isRequired('father_phone'))<span class="text-danger">*</span>@endif</label>
                            <input type="text" class="form-control @error('father_phone') is-invalid @enderror" name="father_phone" value="{{ old('father_phone', $student->parent?->father_phone) }}" {{ $isRequired('father_phone') ? 'required' : '' }}>
                        </div>
                        @endif
                    </div>
                    <div class="row">
                        @if($isVisible('father_email'))
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Father's Email @if($isRequired('father_email'))<span class="text-danger">*</span>@endif</label>
                            <input type="email" class="form-control @error('father_email') is-invalid @enderror" name="father_email" value="{{ old('father_email', $student->parent?->father_email) }}" {{ $isRequired('father_email') ? 'required' : '' }}>
                        </div>
                        @endif
                        @if($isVisible('father_occupation'))
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Father's Occupation @if($isRequired('father_occupation'))<span class="text-danger">*</span>@endif</label>
                            <input type="text" class="form-control @error('father_occupation') is-invalid @enderror" name="father_occupation" value="{{ old('father_occupation', $student->parent?->father_occupation) }}" {{ $isRequired('father_occupation') ? 'required' : '' }}>
                        </div>
                        @endif
                    </div>

                    @if($isVisible('mother_name') || $isVisible('mother_phone') || $isVisible('mother_email') || $isVisible('mother_occupation'))
                    <hr class="my-4">

                    <h6 class="mb-3 text-danger">Mother's Details</h6>
                    <div class="row">
                        @if($isVisible('mother_name'))
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mother's Name @if($isRequired('mother_name'))<span class="text-danger">*</span>@endif</label>
                            <input type="text" class="form-control @error('mother_name') is-invalid @enderror" name="mother_name" value="{{ old('mother_name', $student->parent?->mother_name) }}" {{ $isRequired('mother_name') ? 'required' : '' }}>
                        </div>
                        @endif
                        @if($isVisible('mother_phone'))
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mother's Phone @if($isRequired('mother_phone'))<span class="text-danger">*</span>@endif</label>
                            <input type="text" class="form-control @error('mother_phone') is-invalid @enderror" name="mother_phone" value="{{ old('mother_phone', $student->parent?->mother_phone) }}" {{ $isRequired('mother_phone') ? 'required' : '' }}>
                        </div>
                        @endif
                    </div>
                    <div class="row">
                        @if($isVisible('mother_email'))
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mother's Email @if($isRequired('mother_email'))<span class="text-danger">*</span>@endif</label>
                            <input type="email" class="form-control @error('mother_email') is-invalid @enderror" name="mother_email" value="{{ old('mother_email', $student->parent?->mother_email) }}" {{ $isRequired('mother_email') ? 'required' : '' }}>
                        </div>
                        @endif
                        @if($isVisible('mother_occupation'))
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mother's Occupation @if($isRequired('mother_occupation'))<span class="text-danger">*</span>@endif</label>
                            <input type="text" class="form-control @error('mother_occupation') is-invalid @enderror" name="mother_occupation" value="{{ old('mother_occupation', $student->parent?->mother_occupation) }}" {{ $isRequired('mother_occupation') ? 'required' : '' }}>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Custom Fields -->
            @include('admin.custom-fields._form-fields', [
                'customFields' => $customFields,
                'customFieldValues' => $customFieldValues,
                'formContext' => 'edit'
            ])
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            @if($isVisible('photo'))
            <div class="card">
                <div class="card-header">
                    <h5>Student Photo</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <img id="photoPreview" src="{{ $student->photo_url }}" alt="Student Photo" class="img-thumbnail" style="width: 200px; height: 200px; object-fit: cover;">
                    </div>
                    <input type="file" class="form-control @error('photo') is-invalid @enderror" name="photo" id="photoInput" accept="image/*">
                    @error('photo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Leave empty to keep current photo</small>
                </div>
            </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5>Registration Info</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Admission No</span>
                            <strong>{{ $student->admission_no }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Admission Date</span>
                            <strong>{{ $student->admission_date?->format('M d, Y') }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Academic Year</span>
                            <strong>{{ $student->academicYear->name ?? 'N/A' }}</strong>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="me-1"></i> Update Student
                        </button>
                        <a href="{{ route('admin.students.show', $student) }}" class="btn btn-outline-info">
                            <i data-feather="eye" class="me-1"></i> View Profile
                        </a>
                        <a href="{{ route('admin.students.index') }}" class="btn btn-light">
                            <i data-feather="arrow-left" class="me-1"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/flat-pickr/flatpickr.js') }}"></script>
<script>
    // Initialize Flatpickr for date fields
    flatpickr("#date_of_birth", {
        dateFormat: "d-m-Y",
        maxDate: "today"
    });

    // Class-Section dependency
    const classesData = @json($classes);
    const currentSectionId = '{{ old('section_id', $student->section_id) }}';

    document.getElementById('classSelect').addEventListener('change', function() {
        const classId = this.value;
        const sectionSelect = document.getElementById('sectionSelect');

        sectionSelect.innerHTML = '<option value="">Select Section</option>';

        if (classId) {
            const selectedClass = classesData.find(c => c.id == classId);
            if (selectedClass && selectedClass.sections) {
                selectedClass.sections.forEach(section => {
                    const option = document.createElement('option');
                    option.value = section.id;
                    option.textContent = section.name;
                    if (section.id == currentSectionId) {
                        option.selected = true;
                    }
                    sectionSelect.appendChild(option);
                });
            }
        }
    });

    // Trigger class change on page load
    document.getElementById('classSelect').dispatchEvent(new Event('change'));

    // Photo preview
    @if($isVisible('photo'))
    document.getElementById('photoInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('photoPreview').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
    @endif
</script>
@endpush
