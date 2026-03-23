@extends('layouts.app')

@section('title', 'Alumni')
@section('page-title', 'Alumni Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Alumni</li>
@endsection

@push('styles')
<style>
    .alumni-stat {
        border: none;
        border-radius: 14px;
        transition: transform 0.15s ease;
    }
    .alumni-stat:hover {
        transform: translateY(-2px);
    }
    .alumni-stat .card-body {
        padding: 1.25rem 1.5rem;
    }
    .alumni-stat .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Stats -->
    <div class="row mb-4 g-3">
        <div class="col-md-4">
            <div class="card alumni-stat shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon" style="background: rgba(115, 102, 255, 0.08);">
                            <i class="icon-user" style="font-size: 22px; color: #7366ff;"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Total Alumni</p>
                            <h4 class="mb-0 fw-bold" style="color: #7366ff;">{{ $stats['total'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card alumni-stat shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon" style="background: rgba(39, 174, 96, 0.08);">
                            <i class="icon-check" style="font-size: 22px; color: #27ae60;"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Graduated</p>
                            <h4 class="mb-0 fw-bold text-success">{{ $stats['graduated'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card alumni-stat shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon" style="background: rgba(230, 126, 34, 0.08);">
                            <i class="icon-share-alt" style="font-size: 22px; color: #e67e22;"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Transferred</p>
                            <h4 class="mb-0 fw-bold" style="color: #e67e22;">{{ $stats['transferred'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 px-3 mb-3" style="font-size: 13px; border-radius: 8px;">
            <i class="icon-check me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" style="padding: 10px;"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-3" style="font-size: 13px; border-radius: 8px;">
            <i class="icon-alert me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" style="padding: 10px;"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">Alumni List</h6>
                @if($class12ActiveCount > 0)
                    <form action="{{ route('admin.alumni.auto-graduate') }}" method="POST" id="autoGraduateForm" class="d-inline">
                        @csrf
                        <button type="button" class="btn btn-success btn-sm" id="autoGraduateBtn">
                            <i class="icon-check me-1"></i> Auto-Graduate Class 12 ({{ $class12ActiveCount }} students)
                        </button>
                    </form>
                @endif
            </div>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <form action="{{ route('admin.alumni.index') }}" method="GET" class="row g-3 align-items-end mb-4">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Name, Admission No..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Last Class</label>
                    <select name="class_id" class="form-select">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="graduated" {{ request('status') == 'graduated' ? 'selected' : '' }}>Graduated</option>
                        <option value="transferred" {{ request('status') == 'transferred' ? 'selected' : '' }}>Transferred</option>
                        <option value="expelled" {{ request('status') == 'expelled' ? 'selected' : '' }}>Expelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Session</label>
                    <select name="academic_year_id" class="form-select">
                        <option value="">All Sessions</option>
                        @foreach($academicYears as $ay)
                            <option value="{{ $ay->id }}" {{ request('academic_year_id') == $ay->id ? 'selected' : '' }}>{{ $ay->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="icon-filter me-1"></i>
                        </button>
                        @if(request()->hasAny(['search', 'class_id', 'status', 'academic_year_id']))
                            <a href="{{ route('admin.alumni.index') }}" class="btn btn-outline-secondary" title="Reset">
                                <i class="icon-reload"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 4%;">#</th>
                            <th style="width: 10%;">Admission No</th>
                            <th style="width: 17%;">Student Name</th>
                            <th style="width: 10%;">Last Class</th>
                            <th style="width: 10%;">Session</th>
                            <th style="width: 10%;" class="text-center">Status</th>
                            <th style="width: 11%;">Leaving Date</th>
                            <th style="width: 14%;">Reason</th>
                            <th style="width: 14%;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($alumni as $student)
                            <tr>
                                <td class="text-muted">{{ $alumni->firstItem() + $loop->index }}</td>
                                <td><span class="badge badge-light-primary px-2">{{ $student->admission_no }}</span></td>
                                <td>
                                    <div style="line-height: 1.3;">
                                        <span class="fw-medium">{{ $student->full_name }}</span>
                                        @if($student->email)
                                            <br><small class="text-muted">{{ $student->email }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    {{ $student->schoolClass->name ?? '-' }}
                                    @if($student->section)
                                        <span class="text-muted">({{ $student->section->name }})</span>
                                    @endif
                                </td>
                                <td>
                                    @if($student->academicYear)
                                        <span class="badge badge-light-info px-2">{{ $student->academicYear->name }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($student->status === 'graduated')
                                        <span class="badge badge-light-success px-2">Graduated</span>
                                    @elseif($student->status === 'transferred')
                                        <span class="badge badge-light-warning px-2">Transferred</span>
                                    @else
                                        <span class="badge badge-light-danger px-2">{{ ucfirst($student->status) }}</span>
                                    @endif
                                </td>
                                <td>{{ $student->leaving_date ? $student->leaving_date->format('d M Y') : '-' }}</td>
                                <td><span class="text-muted">{{ Str::limit($student->leaving_reason, 30) ?? '-' }}</span></td>
                                <td class="text-center">
                                    <div class="d-flex gap-1 justify-content-center">
                                        @if($student->status === 'transferred')
                                            <a href="{{ route('admin.certificates.tc', $student) }}" class="btn btn-sm btn-outline-primary" title="Download TC">
                                                <i class="icon-download" style="font-size: 13px;"></i> TC
                                            </a>
                                        @endif
                                        @if($student->status === 'graduated')
                                            <a href="{{ route('admin.certificates.marksheet', $student) }}" class="btn btn-sm btn-outline-success" title="Download Marksheet">
                                                <i class="icon-download" style="font-size: 13px;"></i> Marksheet
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px;">
                                            <i class="icon-user" style="font-size: 24px; color: #95a5a6;"></i>
                                        </div>
                                        <p class="text-muted mb-0">No alumni records found.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($alumni->hasPages())
            <div class="card-footer bg-white">
                {{ $alumni->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
    jQuery('#autoGraduateBtn').on('click', function() {
        Swal.fire({
            title: 'Auto-Graduate Class 12?',
            html: 'This will mark all <strong>Class 12 students who have passed</strong> (percentage >= 33%) as <strong>Graduated</strong>.<br><br><small class="text-muted">Only active students with exam marks will be affected. This action can be reversed by editing the student.</small>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#27ae60',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Graduate Them',
            cancelButtonText: 'Cancel'
        }).then(function(result) {
            if (result.isConfirmed) {
                jQuery('#autoGraduateForm').submit();
            }
        });
    });
});
</script>
@endpush
