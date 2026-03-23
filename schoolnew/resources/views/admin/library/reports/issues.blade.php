@extends('layouts.app')

@section('title', 'Issue History Report')

@section('page-title', 'Book Issue History Report')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.library.reports.index') }}">Library Reports</a></li>
    <li class="breadcrumb-item active">Issue History</li>
@endsection

@push('styles')
<style>
    .issue-stat {
        border: none;
        border-radius: 12px;
    }
    .issue-stat .card-body {
        padding: 1.25rem 1.5rem;
    }
    .issue-stat .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .book-cell {
        line-height: 1.4;
    }
    .book-cell .title {
        font-weight: 600;
        color: #2d3436;
    }
    .book-cell .author {
        font-size: 11px;
        color: #95a5a6;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Filter Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold">Filter Report</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.library.reports.issues') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">From Date</label>
                    <input type="date" name="from_date" class="form-control" value="{{ $fromDate }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">To Date</label>
                    <input type="date" name="to_date" class="form-control" value="{{ $toDate }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="issued" {{ request('status') == 'issued' ? 'selected' : '' }}>Issued</option>
                        <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Student</label>
                    <select name="student_id" class="form-select">
                        <option value="">All Students</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="icon-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('admin.library.reports.issues') }}" class="btn btn-outline-secondary" title="Reset">
                            <i class="icon-reload"></i>
                        </a>
                        <a href="{{ route('admin.library.reports.export', ['type' => 'issues', 'from_date' => $fromDate, 'to_date' => $toDate]) }}" class="btn btn-outline-success" title="Export Excel">
                            <i data-feather="download" style="width: 16px; height: 16px;"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card issue-stat shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon bg-primary bg-opacity-10">
                            <i data-feather="book-open" class="text-primary" style="width: 22px; height: 22px;"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Total Issued</p>
                            <h4 class="mb-0 fw-bold">{{ $summary['total_issued'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card issue-stat shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon bg-success bg-opacity-10">
                            <i data-feather="check-circle" class="text-success" style="width: 22px; height: 22px;"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Total Returned</p>
                            <h4 class="mb-0 fw-bold text-success">{{ $summary['total_returned'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card issue-stat shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon bg-warning bg-opacity-10">
                            <span class="text-warning fw-bold" style="font-size: 18px;">₹</span>
                        </div>
                        <div>
                            <p class="text-muted mb-0" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Fine Collected</p>
                            <h4 class="mb-0 fw-bold text-warning">₹{{ number_format($summary['total_fine'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card issue-stat shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon bg-danger bg-opacity-10">
                            <i data-feather="alert-triangle" class="text-danger" style="width: 22px; height: 22px;"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Overdue</p>
                            <h4 class="mb-0 fw-bold text-danger">{{ $summary['overdue_count'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold">Issue Records <span class="fw-normal text-muted">({{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} — {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }})</span></h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 4%;">#</th>
                            <th style="width: 20%;">Book</th>
                            <th style="width: 14%;">Student</th>
                            <th style="width: 11%;">Issue Date</th>
                            <th style="width: 11%;">Due Date</th>
                            <th style="width: 11%;">Return Date</th>
                            <th style="width: 9%;" class="text-end">Fine</th>
                            <th style="width: 10%;" class="text-center">Status</th>
                            <th style="width: 10%;">Issued By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($issues as $issue)
                            <tr>
                                <td class="text-muted">{{ $issues->firstItem() + $loop->index }}</td>
                                <td>
                                    <div class="book-cell">
                                        <div class="title">{{ Str::limit($issue->book->title ?? 'N/A', 30) }}</div>
                                        <div class="author">{{ $issue->book->author ?? '' }}</div>
                                    </div>
                                </td>
                                <td>{{ $issue->student->full_name ?? 'N/A' }}</td>
                                <td>{{ $issue->issue_date->format('d M Y') }}</td>
                                <td>{{ $issue->due_date->format('d M Y') }}</td>
                                <td>{{ $issue->return_date ? $issue->return_date->format('d M Y') : '-' }}</td>
                                <td class="text-end">
                                    @if($issue->fine_amount > 0)
                                        <span class="fw-bold text-danger">₹{{ number_format($issue->fine_amount, 2) }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($issue->status === 'issued')
                                        @if($issue->is_overdue)
                                            <span class="badge badge-light-danger px-2">Overdue</span>
                                        @else
                                            <span class="badge badge-light-warning px-2">Issued</span>
                                        @endif
                                    @else
                                        <span class="badge badge-light-success px-2">Returned</span>
                                    @endif
                                </td>
                                <td>{{ $issue->issuedBy->name ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px;">
                                            <i data-feather="inbox" class="text-muted" style="width: 28px; height: 28px;"></i>
                                        </div>
                                        <p class="text-muted mb-0">No records found for the selected period.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($issues->hasPages())
            <div class="card-footer bg-white">
                {{ $issues->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
