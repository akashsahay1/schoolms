@extends('layouts.portal')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('breadcrumb')
    <li class="breadcrumb-item active">My Profile</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Profile Card -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body text-center">
                    <img src="{{ $student->photo_url }}" alt="{{ $student->full_name }}" class="rounded-circle mb-3" width="120" height="120" style="object-fit: cover;">
                    <h5 class="mb-1">{{ $student->full_name }}</h5>
                    <p class="text-muted mb-2">{{ $student->schoolClass->name ?? 'N/A' }} - {{ $student->section->name ?? 'N/A' }}</p>
                    <div class="mb-3">
                        <span class="badge badge-light-primary">Admission No: {{ $student->admission_no }}</span>
                        @if($student->roll_no)
                            <span class="badge badge-light-secondary">Roll No: {{ $student->roll_no }}</span>
                        @endif
                    </div>
                    <hr>
                    <div class="row text-start">
                        <div class="col-6 mb-2">
                            <small class="text-muted">Email</small>
                            <p class="mb-0">{{ $student->email ?? $user->email ?? 'N/A' }}</p>
                        </div>
                        <div class="col-6 mb-2">
                            <small class="text-muted">Phone</small>
                            <p class="mb-0">{{ $student->phone ?? 'N/A' }}</p>
                        </div>
                        <div class="col-6 mb-2">
                            <small class="text-muted">Date of Birth</small>
                            <p class="mb-0">{{ $student->date_of_birth ? $student->date_of_birth->format('M d, Y') : 'N/A' }}</p>
                        </div>
                        <div class="col-6 mb-2">
                            <small class="text-muted">Gender</small>
                            <p class="mb-0">{{ ucfirst($student->gender ?? 'N/A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details Tabs -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header pb-0">
                    <ul class="nav nav-tabs border-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#personal" role="tab">Personal Info</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#academic" role="tab">Academic Info</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#parent" role="tab">Parent/Guardian</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Personal Info Tab -->
                        <div class="tab-pane fade show active" id="personal" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted">First Name</label>
                                    <p class="mb-0 fw-medium">{{ $student->first_name }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted">Last Name</label>
                                    <p class="mb-0 fw-medium">{{ $student->last_name }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted">Blood Group</label>
                                    <p class="mb-0 fw-medium">{{ $student->blood_group ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted">Religion</label>
                                    <p class="mb-0 fw-medium">{{ $student->religion ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted">Nationality</label>
                                    <p class="mb-0 fw-medium">{{ $student->nationality ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted">Mother Tongue</label>
                                    <p class="mb-0 fw-medium">{{ $student->mother_tongue ?? 'N/A' }}</p>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="text-muted">Current Address</label>
                                    <p class="mb-0 fw-medium">{{ $student->current_address ?? 'N/A' }}</p>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="text-muted">Permanent Address</label>
                                    <p class="mb-0 fw-medium">{{ $student->permanent_address ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Academic Info Tab -->
                        <div class="tab-pane fade" id="academic" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted">Admission Number</label>
                                    <p class="mb-0 fw-medium">{{ $student->admission_no }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted">Roll Number</label>
                                    <p class="mb-0 fw-medium">{{ $student->roll_no ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted">Class</label>
                                    <p class="mb-0 fw-medium">{{ $student->schoolClass->name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted">Section</label>
                                    <p class="mb-0 fw-medium">{{ $student->section->name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted">Academic Year</label>
                                    <p class="mb-0 fw-medium">{{ $student->academicYear->name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted">Admission Date</label>
                                    <p class="mb-0 fw-medium">{{ $student->admission_date ? $student->admission_date->format('M d, Y') : 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted">Previous School</label>
                                    <p class="mb-0 fw-medium">{{ $student->previous_school ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted">Status</label>
                                    <p class="mb-0">
                                        <span class="badge badge-light-{{ $student->status === 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($student->status) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Parent/Guardian Tab -->
                        <div class="tab-pane fade" id="parent" role="tabpanel">
                            @if($student->parent)
                                <div class="row">
                                    @if($student->parent->father_name)
                                        <div class="col-md-6 mb-4">
                                            <h6 class="text-primary mb-3">Father's Information</h6>
                                            <div class="mb-2">
                                                <label class="text-muted">Name</label>
                                                <p class="mb-0 fw-medium">{{ $student->parent->father_name }}</p>
                                            </div>
                                            <div class="mb-2">
                                                <label class="text-muted">Phone</label>
                                                <p class="mb-0 fw-medium">{{ $student->parent->father_phone ?? 'N/A' }}</p>
                                            </div>
                                            <div class="mb-2">
                                                <label class="text-muted">Email</label>
                                                <p class="mb-0 fw-medium">{{ $student->parent->father_email ?? 'N/A' }}</p>
                                            </div>
                                            <div class="mb-2">
                                                <label class="text-muted">Occupation</label>
                                                <p class="mb-0 fw-medium">{{ $student->parent->father_occupation ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    @endif

                                    @if($student->parent->mother_name)
                                        <div class="col-md-6 mb-4">
                                            <h6 class="text-primary mb-3">Mother's Information</h6>
                                            <div class="mb-2">
                                                <label class="text-muted">Name</label>
                                                <p class="mb-0 fw-medium">{{ $student->parent->mother_name }}</p>
                                            </div>
                                            <div class="mb-2">
                                                <label class="text-muted">Phone</label>
                                                <p class="mb-0 fw-medium">{{ $student->parent->mother_phone ?? 'N/A' }}</p>
                                            </div>
                                            <div class="mb-2">
                                                <label class="text-muted">Email</label>
                                                <p class="mb-0 fw-medium">{{ $student->parent->mother_email ?? 'N/A' }}</p>
                                            </div>
                                            <div class="mb-2">
                                                <label class="text-muted">Occupation</label>
                                                <p class="mb-0 fw-medium">{{ $student->parent->mother_occupation ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    @endif

                                    @if($student->parent->guardian_name)
                                        <div class="col-md-6 mb-4">
                                            <h6 class="text-primary mb-3">Guardian's Information</h6>
                                            <div class="mb-2">
                                                <label class="text-muted">Name</label>
                                                <p class="mb-0 fw-medium">{{ $student->parent->guardian_name }}</p>
                                            </div>
                                            <div class="mb-2">
                                                <label class="text-muted">Relation</label>
                                                <p class="mb-0 fw-medium">{{ $student->parent->guardian_relation ?? 'N/A' }}</p>
                                            </div>
                                            <div class="mb-2">
                                                <label class="text-muted">Phone</label>
                                                <p class="mb-0 fw-medium">{{ $student->parent->guardian_phone ?? 'N/A' }}</p>
                                            </div>
                                            <div class="mb-2">
                                                <label class="text-muted">Email</label>
                                                <p class="mb-0 fw-medium">{{ $student->parent->guardian_email ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <p class="text-muted mb-0">No parent/guardian information available</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
