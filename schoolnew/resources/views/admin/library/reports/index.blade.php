@extends('layouts.app')

@section('title', 'Library Reports')

@section('page-title', 'Library Reports Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Library</li>
    <li class="breadcrumb-item active">Reports</li>
@endsection

@push('styles')
<style>
    .lib-stat-card {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        transition: transform 0.2s;
    }
    .lib-stat-card:hover {
        transform: translateY(-2px);
    }
    .lib-stat-card .card-body {
        padding: 1.25rem;
    }
    .lib-stat-card .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .report-link-card {
        border: 1px solid #e9ecef;
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.2s;
        text-decoration: none;
        display: block;
        height: 100%;
    }
    .report-link-card:hover {
        border-color: transparent;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transform: translateY(-3px);
    }
    .report-link-card .icon-wrap {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
    }
    .report-link-card h6 {
        margin-bottom: 4px;
        font-weight: 600;
    }
    .report-link-card p {
        font-size: 12px;
        margin: 0;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card lib-stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon bg-primary bg-opacity-10">
                            <i data-feather="book-open" class="text-primary" style="width: 22px; height: 22px;"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0" style="font-size: 12px;">Total Books</p>
                            <h5 class="mb-0 fw-bold">{{ number_format($stats['total_books']) }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card lib-stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon bg-success bg-opacity-10">
                            <i data-feather="check-circle" class="text-success" style="width: 22px; height: 22px;"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0" style="font-size: 12px;">Available</p>
                            <h5 class="mb-0 fw-bold text-success">{{ number_format($stats['available_books']) }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card lib-stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon bg-warning bg-opacity-10">
                            <i data-feather="bookmark" class="text-warning" style="width: 22px; height: 22px;"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0" style="font-size: 12px;">Issued</p>
                            <h5 class="mb-0 fw-bold text-warning">{{ number_format($stats['issued_books']) }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card lib-stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon bg-danger bg-opacity-10">
                            <i data-feather="alert-triangle" class="text-danger" style="width: 22px; height: 22px;"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0" style="font-size: 12px;">Overdue</p>
                            <h5 class="mb-0 fw-bold text-danger">{{ number_format($stats['overdue_books']) }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card lib-stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon bg-info bg-opacity-10">
                            <i data-feather="layers" class="text-info" style="width: 22px; height: 22px;"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0" style="font-size: 12px;">Categories</p>
                            <h5 class="mb-0 fw-bold">{{ number_format($stats['total_categories']) }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card lib-stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon bg-secondary bg-opacity-10">
                            <span class="text-secondary fw-bold" style="font-size: 18px;">₹</span>
                        </div>
                        <div>
                            <p class="text-muted mb-0" style="font-size: 12px;">Fine Collected</p>
                            <h5 class="mb-0 fw-bold">₹{{ number_format($stats['total_fine_collected'], 2) }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Report Links -->
    <div class="row mb-4">
        <div class="col-12">
            <h6 class="text-muted text-uppercase mb-3" style="font-size: 12px; letter-spacing: 1px;">Quick Reports</h6>
        </div>
        <div class="col-xl col-md-4 col-sm-6 mb-3">
            <a href="{{ route('admin.library.reports.issues') }}" class="report-link-card">
                <div class="icon-wrap bg-primary bg-opacity-10">
                    <i data-feather="file-text" class="text-primary" style="width: 24px; height: 24px;"></i>
                </div>
                <h6 class="text-dark">Issue History</h6>
                <p class="text-muted">View all issued books</p>
            </a>
        </div>
        <div class="col-xl col-md-4 col-sm-6 mb-3">
            <a href="{{ route('admin.library.reports.overdue') }}" class="report-link-card">
                <div class="icon-wrap bg-danger bg-opacity-10">
                    <i data-feather="alert-triangle" class="text-danger" style="width: 24px; height: 24px;"></i>
                </div>
                <h6 class="text-dark">Overdue Books</h6>
                <p class="text-muted">Track overdue returns</p>
            </a>
        </div>
        <div class="col-xl col-md-4 col-sm-6 mb-3">
            <a href="{{ route('admin.library.reports.inventory') }}" class="report-link-card">
                <div class="icon-wrap bg-success bg-opacity-10">
                    <i data-feather="book" class="text-success" style="width: 24px; height: 24px;"></i>
                </div>
                <h6 class="text-dark">Book Inventory</h6>
                <p class="text-muted">Full stock overview</p>
            </a>
        </div>
        <div class="col-xl col-md-4 col-sm-6 mb-3">
            <a href="{{ route('admin.library.reports.fines') }}" class="report-link-card">
                <div class="icon-wrap bg-warning bg-opacity-10">
                    <span class="text-warning fw-bold" style="font-size: 20px;">₹</span>
                </div>
                <h6 class="text-dark">Fine Collection</h6>
                <p class="text-muted">Fines & penalties</p>
            </a>
        </div>
        <div class="col-xl col-md-4 col-sm-6 mb-3">
            <a href="{{ route('admin.library.reports.student-wise') }}" class="report-link-card">
                <div class="icon-wrap bg-info bg-opacity-10">
                    <i data-feather="users" class="text-info" style="width: 24px; height: 24px;"></i>
                </div>
                <h6 class="text-dark">Student-wise</h6>
                <p class="text-muted">Per-student activity</p>
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Recent Issues -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">Recent Issues</h6>
                        <a href="{{ route('admin.library.reports.issues') }}" class="btn btn-sm btn-outline-primary">
                            View All <i data-feather="arrow-right" style="width: 14px; height: 14px;" class="ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Book</th>
                                    <th>Student</th>
                                    <th>Issue Date</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentIssues as $issue)
                                    <tr>
                                        <td>{{ Str::limit($issue->book->title ?? 'N/A', 25) }}</td>
                                        <td>{{ $issue->student->full_name ?? 'N/A' }}</td>
                                        <td>{{ $issue->issue_date->format('d M Y') }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-light-{{ $issue->status === 'issued' ? ($issue->is_overdue ? 'danger' : 'warning') : 'success' }} px-2">
                                                {{ $issue->is_overdue && $issue->status === 'issued' ? 'Overdue' : ucfirst($issue->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">No recent issues</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overdue Books -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">
                            Overdue Books
                            @if($stats['overdue_books'] > 0)
                                <span class="badge bg-danger ms-2">{{ $stats['overdue_books'] }}</span>
                            @endif
                        </h6>
                        <a href="{{ route('admin.library.reports.overdue') }}" class="btn btn-sm btn-outline-danger">
                            View All <i data-feather="arrow-right" style="width: 14px; height: 14px;" class="ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Book</th>
                                    <th>Student</th>
                                    <th>Due Date</th>
                                    <th class="text-end">Fine</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($overdueBooks as $issue)
                                    <tr>
                                        <td>{{ Str::limit($issue->book->title ?? 'N/A', 25) }}</td>
                                        <td>{{ $issue->student->full_name ?? 'N/A' }}</td>
                                        <td class="text-danger">{{ $issue->due_date->format('d M Y') }}</td>
                                        <td class="text-end text-danger fw-bold">₹{{ number_format($issue->calculated_fine, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">No overdue books</td>
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
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold">Books by Category</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($categoryStats as $category)
                            <div class="col-xl-3 col-md-4 col-sm-6 mb-3">
                                <div class="d-flex align-items-center p-2 rounded" style="background: #f8f9fa;">
                                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px; flex-shrink: 0;">
                                        <i data-feather="folder" class="text-primary" style="width: 16px; height: 16px;"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0 text-muted" style="font-size: 12px;">{{ $category->name }}</p>
                                        <h6 class="mb-0 fw-bold">{{ $category->books_count }} books</h6>
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
