@extends('layouts.app')

@section('title', 'Library Reports')

@section('page-title', 'Library Reports Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Library</li>
    <li class="breadcrumb-item active">Reports</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card o-hidden border-0">
                <div class="card-body bg-primary">
                    <div class="media static-top-widget">
                        <div class="media-body">
                            <span class="m-0 text-white">Total Books</span>
                            <h4 class="mb-0 text-white counter">{{ $stats['total_books'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card o-hidden border-0">
                <div class="card-body bg-success">
                    <div class="media static-top-widget">
                        <div class="media-body">
                            <span class="m-0 text-white">Available</span>
                            <h4 class="mb-0 text-white counter">{{ $stats['available_books'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card o-hidden border-0">
                <div class="card-body bg-warning">
                    <div class="media static-top-widget">
                        <div class="media-body">
                            <span class="m-0 text-white">Issued</span>
                            <h4 class="mb-0 text-white counter">{{ $stats['issued_books'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card o-hidden border-0">
                <div class="card-body bg-danger">
                    <div class="media static-top-widget">
                        <div class="media-body">
                            <span class="m-0 text-white">Overdue</span>
                            <h4 class="mb-0 text-white counter">{{ $stats['overdue_books'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card o-hidden border-0">
                <div class="card-body bg-info">
                    <div class="media static-top-widget">
                        <div class="media-body">
                            <span class="m-0 text-white">Categories</span>
                            <h4 class="mb-0 text-white counter">{{ $stats['total_categories'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card o-hidden border-0">
                <div class="card-body bg-secondary">
                    <div class="media static-top-widget">
                        <div class="media-body">
                            <span class="m-0 text-white">Fine Collected</span>
                            <h4 class="mb-0 text-white counter">{{ number_format($stats['total_fine_collected'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Report Links -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Quick Reports</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('admin.library.reports.issues') }}" class="btn btn-outline-primary w-100 py-3">
                                <i data-feather="file-text" class="me-2"></i> Issue History Report
                            </a>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('admin.library.reports.overdue') }}" class="btn btn-outline-danger w-100 py-3">
                                <i data-feather="alert-triangle" class="me-2"></i> Overdue Books Report
                            </a>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('admin.library.reports.inventory') }}" class="btn btn-outline-success w-100 py-3">
                                <i data-feather="book" class="me-2"></i> Book Inventory Report
                            </a>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('admin.library.reports.fines') }}" class="btn btn-outline-warning w-100 py-3">
                                <i data-feather="dollar-sign" class="me-2"></i> Fine Collection Report
                            </a>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('admin.library.reports.student-wise') }}" class="btn btn-outline-info w-100 py-3">
                                <i data-feather="users" class="me-2"></i> Student-wise Report
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Issues -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>Recent Issues</h5>
                        <a href="{{ route('admin.library.reports.issues') }}" class="btn btn-sm btn-primary">View All</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Book</th>
                                    <th>Student</th>
                                    <th>Issue Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentIssues as $issue)
                                    <tr>
                                        <td>{{ Str::limit($issue->book->title ?? 'N/A', 25) }}</td>
                                        <td>{{ $issue->student->full_name ?? 'N/A' }}</td>
                                        <td>{{ $issue->issue_date->format('d M Y') }}</td>
                                        <td>
                                            <span class="badge badge-light-{{ $issue->status === 'issued' ? ($issue->is_overdue ? 'danger' : 'warning') : 'success' }}">
                                                {{ $issue->is_overdue && $issue->status === 'issued' ? 'Overdue' : ucfirst($issue->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No recent issues</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overdue Books -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>Overdue Books</h5>
                        <a href="{{ route('admin.library.reports.overdue') }}" class="btn btn-sm btn-danger">View All</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Book</th>
                                    <th>Student</th>
                                    <th>Due Date</th>
                                    <th>Fine</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($overdueBooks as $issue)
                                    <tr>
                                        <td>{{ Str::limit($issue->book->title ?? 'N/A', 25) }}</td>
                                        <td>{{ $issue->student->full_name ?? 'N/A' }}</td>
                                        <td class="text-danger">{{ $issue->due_date->format('d M Y') }}</td>
                                        <td class="text-danger fw-bold">{{ number_format($issue->calculated_fine, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No overdue books</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Stats -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Books by Category</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($categoryStats as $category)
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <span class="text-muted">{{ $category->name }}</span>
                                        <h6 class="mb-0">{{ $category->books_count }} books</h6>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
