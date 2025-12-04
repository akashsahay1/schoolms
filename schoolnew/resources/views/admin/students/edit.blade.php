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
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name', $student->last_name) }}">
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
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
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Blood Group</label>
                            <select class="form-select @error('blood_group') is-invalid @enderror" name="blood_group">
                                <option value="">Select</option>
                                @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                    <option value="{{ $bg }}" {{ old('blood_group', $student->blood_group) == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Religion</label>
                            <input type="text" class="form-control @error('religion') is-invalid @enderror" name="religion" value="{{ old('religion', $student->religion) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Nationality</label>
                            <input type="text" class="form-control @error('nationality') is-invalid @enderror" name="nationality" value="{{ old('nationality', $student->nationality) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Mother Tongue</label>
                            <input type="text" class="form-control @error('mother_tongue') is-invalid @enderror" name="mother_tongue" value="{{ old('mother_tongue', $student->mother_tongue) }}">
                        </div>
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
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Roll No</label>
                            <input type="text" class="form-control @error('roll_no') is-invalid @enderror" name="roll_no" value="{{ old('roll_no', $student->roll_no) }}">
                        </div>
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
                </div>
            </div>

            <!-- Contact Information -->
            <div class="card">
                <div class="card-header">
                    <h5>Contact Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $student->email) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', $student->phone) }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Current Address</label>
                            <textarea class="form-control @error('current_address') is-invalid @enderror" name="current_address" rows="3">{{ old('current_address', $student->current_address) }}</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Permanent Address</label>
                            <textarea class="form-control @error('permanent_address') is-invalid @enderror" name="permanent_address" rows="3">{{ old('permanent_address', $student->permanent_address) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

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
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Father's Phone</label>
                            <input type="text" class="form-control @error('father_phone') is-invalid @enderror" name="father_phone" value="{{ old('father_phone', $student->parent?->father_phone) }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Father's Email</label>
                            <input type="email" class="form-control @error('father_email') is-invalid @enderror" name="father_email" value="{{ old('father_email', $student->parent?->father_email) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Father's Occupation</label>
                            <input type="text" class="form-control @error('father_occupation') is-invalid @enderror" name="father_occupation" value="{{ old('father_occupation', $student->parent?->father_occupation) }}">
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="mb-3 text-danger">Mother's Details</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mother's Name</label>
                            <input type="text" class="form-control @error('mother_name') is-invalid @enderror" name="mother_name" value="{{ old('mother_name', $student->parent?->mother_name) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mother's Phone</label>
                            <input type="text" class="form-control @error('mother_phone') is-invalid @enderror" name="mother_phone" value="{{ old('mother_phone', $student->parent?->mother_phone) }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mother's Email</label>
                            <input type="email" class="form-control @error('mother_email') is-invalid @enderror" name="mother_email" value="{{ old('mother_email', $student->parent?->mother_email) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mother's Occupation</label>
                            <input type="text" class="form-control @error('mother_occupation') is-invalid @enderror" name="mother_occupation" value="{{ old('mother_occupation', $student->parent?->mother_occupation) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5>Student Photo</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <img id="photoPreview" src="{{ $student->photo ? asset('storage/' . $student->photo) : asset('assets/images/user/default-avatar.png') }}" alt="Student Photo" class="img-thumbnail" style="width: 200px; height: 200px; object-fit: cover;">
                    </div>
                    <input type="file" class="form-control @error('photo') is-invalid @enderror" name="photo" id="photoInput" accept="image/*">
                    @error('photo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Leave empty to keep current photo</small>
                </div>
            </div>

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
</script>
@endpush
