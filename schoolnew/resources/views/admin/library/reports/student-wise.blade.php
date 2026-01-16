@extends('layouts.app')

@section('title', 'Student-wise Library Report')

@section('page-title', 'Student-wise Library Report')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.library.reports.index') }}">Library Reports</a></li>
    <li class="breadcrumb-item active">Student-wise</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.library.reports.student-wise') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Filter by Class</label>
                    <select name="class_id" class="form-select">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">
                        <i data-feather="filter" class="me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.library.reports.student-wise') }}" class="btn btn-light">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card">
        <div class="card-header pb-0">
            <h5>Student Library Statistics</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Class</th>
                            <th>Total Borrowed</th>
                            <th>Currently Issued</th>
                            <th>Total Fine Paid</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td>{{ $students->firstItem() + $loop->index }}</td>
                                <td>
                                    <strong>{{ $student->full_name }}</strong>
                                    <br><small class="text-muted">{{ $student->admission_no }}</small>
                                </td>
                                <td>
                                    {{ $student->schoolClass->name ?? '-' }}
                                    {{ $student->section ? '(' . $student->section->name . ')' : '' }}
                                </td>
                                <td>{{ $student->book_issues_count }}</td>
                                <td>
                                    @if($student->current_issues_count > 0)
                                        <span class="badge bg-warning">{{ $student->current_issues_count }}</span>
                                    @else
                                        <span class="text-muted">0</span>
                                    @endif
                                </td>
                                <td>{{ number_format($student->book_issues_sum_fine_amount ?? 0, 2) }}</td>
                                <td>
                                    <a href="{{ route('admin.library.reports.issues', ['student_id' => $student->id]) }}" class="btn btn-sm btn-outline-primary">
                                        <i data-feather="eye" style="width: 14px; height: 14px;"></i> View History
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <p class="text-muted">No students found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($students->hasPages())
                <div class="mt-3">{{ $students->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
