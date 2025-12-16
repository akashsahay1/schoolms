@extends('layouts.portal')

@section('title', 'My Profile')
@section('page-title', 'Parent Profile')

@section('breadcrumb')
    <li class="breadcrumb-item active">My Profile</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body text-center">
                    <img src="{{ $user->avatar_url }}" alt="" class="rounded-circle mb-3" width="120" height="120" style="object-fit: cover;">
                    <h5 class="mb-1">{{ $parent->father_name ?? $parent->mother_name ?? $parent->guardian_name ?? 'Parent' }}</h5>
                    <p class="text-muted mb-2">Parent / Guardian</p>
                    <hr>
                    <div class="text-start">
                        <div class="mb-2">
                            <small class="text-muted">Primary Contact</small>
                            <p class="mb-0">{{ $parent->primary_contact ?? 'N/A' }}</p>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Primary Email</small>
                            <p class="mb-0">{{ $parent->primary_email ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card">
                <div class="card-header pb-0">
                    <ul class="nav nav-tabs border-tab" role="tablist">
                        @if($parent->father_name)
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#father" role="tab">Father</a>
                            </li>
                        @endif
                        @if($parent->mother_name)
                            <li class="nav-item">
                                <a class="nav-link {{ !$parent->father_name ? 'active' : '' }}" data-bs-toggle="tab" href="#mother" role="tab">Mother</a>
                            </li>
                        @endif
                        @if($parent->guardian_name)
                            <li class="nav-item">
                                <a class="nav-link {{ !$parent->father_name && !$parent->mother_name ? 'active' : '' }}" data-bs-toggle="tab" href="#guardian" role="tab">Guardian</a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#children" role="tab">Children</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        @if($parent->father_name)
                            <div class="tab-pane fade show active" id="father" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted">Name</label>
                                        <p class="fw-medium mb-0">{{ $parent->father_name }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted">Phone</label>
                                        <p class="fw-medium mb-0">{{ $parent->father_phone ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted">Email</label>
                                        <p class="fw-medium mb-0">{{ $parent->father_email ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted">Occupation</label>
                                        <p class="fw-medium mb-0">{{ $parent->father_occupation ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($parent->mother_name)
                            <div class="tab-pane fade {{ !$parent->father_name ? 'show active' : '' }}" id="mother" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted">Name</label>
                                        <p class="fw-medium mb-0">{{ $parent->mother_name }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted">Phone</label>
                                        <p class="fw-medium mb-0">{{ $parent->mother_phone ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted">Email</label>
                                        <p class="fw-medium mb-0">{{ $parent->mother_email ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted">Occupation</label>
                                        <p class="fw-medium mb-0">{{ $parent->mother_occupation ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($parent->guardian_name)
                            <div class="tab-pane fade {{ !$parent->father_name && !$parent->mother_name ? 'show active' : '' }}" id="guardian" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted">Name</label>
                                        <p class="fw-medium mb-0">{{ $parent->guardian_name }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted">Relation</label>
                                        <p class="fw-medium mb-0">{{ $parent->guardian_relation ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted">Phone</label>
                                        <p class="fw-medium mb-0">{{ $parent->guardian_phone ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted">Email</label>
                                        <p class="fw-medium mb-0">{{ $parent->guardian_email ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="tab-pane fade" id="children" role="tabpanel">
                            @if($parent->students->count() > 0)
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Adm. No</th>
                                                <th>Class</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($parent->students as $student)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img src="{{ $student->photo_url }}" alt="" class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;">
                                                            {{ $student->full_name }}
                                                        </div>
                                                    </td>
                                                    <td>{{ $student->admission_no }}</td>
                                                    <td>{{ $student->schoolClass->name ?? '' }} - {{ $student->section->name ?? '' }}</td>
                                                    <td>
                                                        <span class="badge badge-light-{{ $student->status === 'active' ? 'success' : 'secondary' }}">
                                                            {{ ucfirst($student->status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">No children linked to your account.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
