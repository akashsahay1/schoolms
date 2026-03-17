@extends('layouts.app')

@section('title', 'Add New Student')

@section('page-title', 'Add New Student')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}">Students</a></li>
    <li class="breadcrumb-item active">Add New</li>
@endsection

@push('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/flatpickr/flatpickr.min.css') }}">
@endpush

@section('content')
@if(!$academicYear)
    <div class="alert alert-warning">
        <h5 class="alert-heading">No Active Academic Year</h5>
        <p class="mb-0">Please set up an active academic year before registering students.</p>
    </div>
@else
@php
    $fs = $fieldSettings ?? [];
    // Helper: check if field is visible (default true)
    $isVisible = function($field) use ($fs) {
        return ($fs[$field]['visible'] ?? true);
    };
    // Helper: check if field is required by settings
    $isRequired = function($field) use ($fs) {
        return ($fs[$field]['required'] ?? false) && ($fs[$field]['visible'] ?? true);
    };
@endphp
<form method="POST" action="{{ route('admin.students.store') }}" enctype="multipart/form-data">
    @csrf

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
                            <input type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name') }}" required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @if($isVisible('last_name'))
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Name @if($isRequired('last_name'))<span class="text-danger">*</span>@endif</label>
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name') }}" {{ $isRequired('last_name') ? 'required' : '' }}>
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
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                            <input type="text" class="form-control flatpickr-input @error('date_of_birth') is-invalid @enderror" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" placeholder="Select Date" required>
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
                                    <option value="{{ $bg }}" {{ old('blood_group') == $bg ? 'selected' : '' }}>{{ $bg }}</option>
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
                                <option value="Hindu" {{ old('religion') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                <option value="Muslim" {{ old('religion') == 'Muslim' ? 'selected' : '' }}>Muslim</option>
                                <option value="Christian" {{ old('religion') == 'Christian' ? 'selected' : '' }}>Christian</option>
                                <option value="Sikh" {{ old('religion') == 'Sikh' ? 'selected' : '' }}>Sikh</option>
                                <option value="Buddhist" {{ old('religion') == 'Buddhist' ? 'selected' : '' }}>Buddhist</option>
                                <option value="Jain" {{ old('religion') == 'Jain' ? 'selected' : '' }}>Jain</option>
                                <option value="Other" {{ old('religion') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        @endif
                        @if($isVisible('nationality'))
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Nationality @if($isRequired('nationality'))<span class="text-danger">*</span>@endif</label>
                            <input type="text" class="form-control @error('nationality') is-invalid @enderror" name="nationality" value="{{ old('nationality', 'Indian') }}" {{ $isRequired('nationality') ? 'required' : '' }}>
                        </div>
                        @endif
                        @if($isVisible('mother_tongue'))
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Mother Tongue @if($isRequired('mother_tongue'))<span class="text-danger">*</span>@endif</label>
                            <input type="text" class="form-control @error('mother_tongue') is-invalid @enderror" name="mother_tongue" value="{{ old('mother_tongue') }}" {{ $isRequired('mother_tongue') ? 'required' : '' }}>
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
                                    <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
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
                            <input type="text" class="form-control @error('roll_no') is-invalid @enderror" name="roll_no" value="{{ old('roll_no') }}" {{ $isRequired('roll_no') ? 'required' : '' }}>
                        </div>
                        @endif
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Admission Date <span class="text-danger">*</span></label>
                            <input type="text" class="form-control flatpickr-input @error('admission_date') is-invalid @enderror" id="admission_date" name="admission_date" value="{{ old('admission_date', date('d-m-Y')) }}" placeholder="Select Date" required>
                            @error('admission_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @if($isVisible('previous_school'))
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Previous School @if($isRequired('previous_school'))<span class="text-danger">*</span>@endif</label>
                            <input type="text" class="form-control @error('previous_school') is-invalid @enderror" name="previous_school" value="{{ old('previous_school') }}" {{ $isRequired('previous_school') ? 'required' : '' }}>
                        </div>
                        @endif
                    </div>
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
                            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" {{ $isRequired('email') ? 'required' : '' }}>
                        </div>
                        @endif
                        @if($isVisible('phone'))
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone @if($isRequired('phone'))<span class="text-danger">*</span>@endif</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" {{ $isRequired('phone') ? 'required' : '' }}>
                        </div>
                        @endif
                    </div>

                    <div class="row">
                        @if($isVisible('current_address'))
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Current Address @if($isRequired('current_address'))<span class="text-danger">*</span>@endif</label>
                            <textarea class="form-control @error('current_address') is-invalid @enderror" name="current_address" rows="3" {{ $isRequired('current_address') ? 'required' : '' }}>{{ old('current_address') }}</textarea>
                        </div>
                        @endif
                        @if($isVisible('permanent_address'))
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Permanent Address @if($isRequired('permanent_address'))<span class="text-danger">*</span>@endif</label>
                            <textarea class="form-control @error('permanent_address') is-invalid @enderror" name="permanent_address" rows="3" {{ $isRequired('permanent_address') ? 'required' : '' }}>{{ old('permanent_address') }}</textarea>
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
                            <input type="text" class="form-control @error('aadhaar_number') is-invalid @enderror" name="aadhaar_number" value="{{ old('aadhaar_number') }}" placeholder="Enter 12-digit Aadhaar number" maxlength="12" {{ $isRequired('aadhaar_number') ? 'required' : '' }}>
                            @error('aadhaar_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif
                        @if($isVisible('aadhaar_front'))
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Aadhaar Card Front @if($isRequired('aadhaar_front'))<span class="text-danger">*</span>@endif</label>
                            <input type="file" class="form-control @error('aadhaar_front') is-invalid @enderror" name="aadhaar_front" accept="image/*,.pdf" {{ $isRequired('aadhaar_front') ? 'required' : '' }}>
                            @error('aadhaar_front')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">JPG, PNG or PDF (max 2MB)</small>
                        </div>
                        @endif
                        @if($isVisible('aadhaar_back'))
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Aadhaar Card Back @if($isRequired('aadhaar_back'))<span class="text-danger">*</span>@endif</label>
                            <input type="file" class="form-control @error('aadhaar_back') is-invalid @enderror" name="aadhaar_back" accept="image/*,.pdf" {{ $isRequired('aadhaar_back') ? 'required' : '' }}>
                            @error('aadhaar_back')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">JPG, PNG or PDF (max 2MB)</small>
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
                            <input type="text" class="form-control @error('father_name') is-invalid @enderror" name="father_name" value="{{ old('father_name') }}" required>
                            @error('father_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @if($isVisible('father_phone'))
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Father's Phone @if($isRequired('father_phone'))<span class="text-danger">*</span>@endif</label>
                            <input type="text" class="form-control @error('father_phone') is-invalid @enderror" name="father_phone" value="{{ old('father_phone') }}" {{ $isRequired('father_phone') ? 'required' : '' }}>
                        </div>
                        @endif
                    </div>
                    <div class="row">
                        @if($isVisible('father_email'))
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Father's Email @if($isRequired('father_email'))<span class="text-danger">*</span>@endif</label>
                            <input type="email" class="form-control @error('father_email') is-invalid @enderror" name="father_email" value="{{ old('father_email') }}" {{ $isRequired('father_email') ? 'required' : '' }}>
                        </div>
                        @endif
                        @if($isVisible('father_occupation'))
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Father's Occupation @if($isRequired('father_occupation'))<span class="text-danger">*</span>@endif</label>
                            <input type="text" class="form-control @error('father_occupation') is-invalid @enderror" name="father_occupation" value="{{ old('father_occupation') }}" {{ $isRequired('father_occupation') ? 'required' : '' }}>
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
                            <input type="text" class="form-control @error('mother_name') is-invalid @enderror" name="mother_name" value="{{ old('mother_name') }}" {{ $isRequired('mother_name') ? 'required' : '' }}>
                        </div>
                        @endif
                        @if($isVisible('mother_phone'))
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mother's Phone @if($isRequired('mother_phone'))<span class="text-danger">*</span>@endif</label>
                            <input type="text" class="form-control @error('mother_phone') is-invalid @enderror" name="mother_phone" value="{{ old('mother_phone') }}" {{ $isRequired('mother_phone') ? 'required' : '' }}>
                        </div>
                        @endif
                    </div>
                    <div class="row">
                        @if($isVisible('mother_email'))
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mother's Email @if($isRequired('mother_email'))<span class="text-danger">*</span>@endif</label>
                            <input type="email" class="form-control @error('mother_email') is-invalid @enderror" name="mother_email" value="{{ old('mother_email') }}" {{ $isRequired('mother_email') ? 'required' : '' }}>
                        </div>
                        @endif
                        @if($isVisible('mother_occupation'))
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mother's Occupation @if($isRequired('mother_occupation'))<span class="text-danger">*</span>@endif</label>
                            <input type="text" class="form-control @error('mother_occupation') is-invalid @enderror" name="mother_occupation" value="{{ old('mother_occupation') }}" {{ $isRequired('mother_occupation') ? 'required' : '' }}>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Custom Fields -->
            @include('admin.custom-fields._form-fields', [
                'customFields' => $customFields,
                'customFieldValues' => [],
                'formContext' => 'create'
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
                        <img id="photoPreview" src="{{ asset('assets/images/user/user.png') }}" alt="Student Photo" class="img-thumbnail" style="width: 200px; height: 200px; object-fit: cover;">
                    </div>
                    <input type="file" class="form-control @error('photo') is-invalid @enderror" name="photo" id="photoInput" accept="image/*">
                    @error('photo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Max size: 2MB. Formats: JPG, PNG, GIF</small>
                </div>
            </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5>Academic Year</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-0">
                        <strong>{{ $academicYear->name }}</strong><br>
                        <small>{{ $academicYear->start_date->format('M d, Y') }} - {{ $academicYear->end_date->format('M d, Y') }}</small>
                    </div>
                </div>
            </div>

            <!-- Login Credentials -->
            <div class="card">
                <div class="card-header">
                    <h5>Login Credentials</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="student_password" class="form-label">Student Password</label>
                        <input type="text" class="form-control @error('student_password') is-invalid @enderror" id="student_password" name="student_password" value="{{ old('student_password') }}" placeholder="Leave empty for auto-generate">
                        @error('student_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Min 6 characters.</small>
                    </div>
                    <div class="mb-3">
                        <label for="parent_password" class="form-label">Parent Password</label>
                        <input type="text" class="form-control @error('parent_password') is-invalid @enderror" id="parent_password" name="parent_password" value="{{ old('parent_password') }}" placeholder="Leave empty for auto-generate">
                        @error('parent_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Min 6 characters. Only used if parent email is provided.</small>
                    </div>
                    <div class="alert alert-info mb-0">
                        <small><i data-feather="info" style="width: 14px; height: 14px;"></i> Leave password fields empty to auto-generate secure passwords.</small>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="me-1"></i> Register Student
                        </button>
                        <a href="{{ route('admin.students.index') }}" class="btn btn-light">
                            <i data-feather="arrow-left" class="me-1"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endif
@endsection

@push('scripts')
<script src="{{ asset('assets/js/flat-pickr/flatpickr.js') }}"></script>
<script>
    // Initialize Flatpickr for date fields
    flatpickr("#date_of_birth", {
        dateFormat: "d-m-Y",
        maxDate: "today"
    });

    flatpickr("#admission_date", {
        dateFormat: "d-m-Y",
        defaultDate: "today"
    });

    // Class-Section dependency
    const classesData = @json($classes);

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
                    sectionSelect.appendChild(option);
                });
            }
        }
    });

    // Photo preview
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

    // Trigger class change if value exists (for old input)
    @if(old('class_id'))
        document.getElementById('classSelect').dispatchEvent(new Event('change'));
        setTimeout(() => {
            document.getElementById('sectionSelect').value = '{{ old('section_id') }}';
        }, 100);
    @endif
</script>
@endpush
