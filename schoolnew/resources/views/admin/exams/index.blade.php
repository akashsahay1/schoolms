@extends('layouts.app')

@section('title', 'Exam Management')

@section('page-title', 'Exam Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Exams</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Exam Management</h5>
                <a href="{{ route('admin.exams.create') }}" class="btn btn-primary">
                    <i data-feather="plus" class="icon-xs"></i> Create New Exam
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <i data-feather="calendar" class="icon-lg mb-3"></i>
                                <h4>{{ $exams->where('status', 'ongoing')->count() + $exams->where('status', 'upcoming')->count() }}</h4>
                                <p class="mb-0">Active Exams</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <i data-feather="edit-3" class="icon-lg mb-3"></i>
                                <h4>{{ $exams->where('status', 'completed')->count() }}</h4>
                                <p class="mb-0">Completed Exams</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <i data-feather="users" class="icon-lg mb-3"></i>
                                <h4>0</h4>
                                <p class="mb-0">Students Appeared</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <i data-feather="file-text" class="icon-lg mb-3"></i>
                                <h4>{{ $exams->where('is_published', true)->count() }}</h4>
                                <p class="mb-0">Results Published</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Exam Name</th>
                                <th>Type</th>
                                <th>Academic Year</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($exams as $exam)
                                <tr>
                                    <td>
                                        <strong>{{ $exam->name }}</strong>
                                        @if($exam->description)
                                            <br><small class="text-muted">{{ $exam->description }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $exam->examType->name }}</td>
                                    <td>{{ $exam->academicYear->name }}</td>
                                    <td>{{ $exam->start_date->format('d M Y') }}</td>
                                    <td>{{ $exam->end_date->format('d M Y') }}</td>
                                    <td>{!! $exam->status_badge !!}</td>
                                    <td>
                                        <div class="common-align gap-2 justify-content-start">
                                            <a href="{{ route('admin.exams.edit', $exam) }}" class="square-white" title="Edit">
                                                <svg>
                                                    <use href="{{ asset('assets/svg/icon-sprite.svg#edit-content') }}"></use>
                                                </svg>
                                            </a>
                                            <form action="{{ route('admin.exams.destroy', $exam) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="square-white trash-7 border-0 bg-transparent p-0 delete-confirm" 
                                                        title="Delete" data-name="{{ $exam->name }}">
                                                    <svg>
                                                        <use href="{{ asset('assets/svg/icon-sprite.svg#trash1') }}"></use>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        No exams created yet. Click "Create New Exam" to get started.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection