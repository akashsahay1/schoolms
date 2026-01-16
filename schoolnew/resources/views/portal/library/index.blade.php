@extends('layouts.portal')

@section('title', 'My Library')
@section('page-title', 'My Library')

@section('breadcrumb')
    <li class="breadcrumb-item active">Library</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card bg-light-primary">
                <div class="card-body text-center">
                    <h3 class="text-primary">{{ $stats['current_books'] }}</h3>
                    <p class="mb-0 text-primary">Currently Borrowed</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card bg-light-danger">
                <div class="card-body text-center">
                    <h3 class="text-danger">{{ $stats['overdue_books'] }}</h3>
                    <p class="mb-0 text-danger">Overdue Books</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card bg-light-info">
                <div class="card-body text-center">
                    <h3 class="text-info">{{ $stats['total_borrowed'] }}</h3>
                    <p class="mb-0 text-info">Total Books Borrowed</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card bg-light-warning">
                <div class="card-body text-center">
                    <h3 class="text-warning">{{ number_format($stats['pending_fine'], 2) }}</h3>
                    <p class="mb-0 text-warning">Pending Fine</p>
                </div>
            </div>
        </div>
    </div>

    @if($stats['pending_fine'] > 0)
        <div class="alert alert-warning">
            <i data-feather="alert-triangle" class="me-2"></i>
            You have a pending fine of <strong>{{ number_format($stats['pending_fine'], 2) }}</strong> for overdue books. Please return them as soon as possible.
            <small class="d-block mt-1">Fine rate: {{ number_format($finePerDay, 2) }} per day for overdue books.</small>
        </div>
    @endif

    <!-- Current Books -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>Currently Borrowed Books</h5>
                        <a href="{{ route('portal.library.search') }}" class="btn btn-outline-primary btn-sm">
                            <i data-feather="search" class="me-1"></i> Search Books
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($currentIssues->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Book</th>
                                        <th>Category</th>
                                        <th>Issue Date</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Fine</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($currentIssues as $issue)
                                        <tr>
                                            <td>
                                                <strong>{{ $issue->book->title }}</strong>
                                                <br><small class="text-muted">{{ $issue->book->author }}</small>
                                            </td>
                                            <td>{{ $issue->book->category->name ?? '-' }}</td>
                                            <td>{{ $issue->issue_date->format('d M Y') }}</td>
                                            <td>
                                                {{ $issue->due_date->format('d M Y') }}
                                                @if($issue->days_remaining > 0)
                                                    <br><small class="text-success">{{ $issue->days_remaining }} days left</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($issue->is_overdue)
                                                    <span class="badge bg-danger">Overdue ({{ $issue->overdue_days }} days)</span>
                                                @else
                                                    <span class="badge bg-success">On Time</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($issue->calculated_fine > 0)
                                                    <span class="text-danger fw-bold">{{ number_format($issue->calculated_fine, 2) }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i data-feather="book-open" style="width: 64px; height: 64px; opacity: 0.5;"></i>
                            <p class="mt-3 text-muted">You haven't borrowed any books currently.</p>
                            <a href="{{ route('portal.library.search') }}" class="btn btn-primary">Browse Library</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <i data-feather="clock" style="width: 32px; height: 32px;" class="text-primary mb-2"></i>
                    <h6>Borrowing History</h6>
                    <p class="text-muted small">View all your past book borrowings</p>
                    <a href="{{ route('portal.library.history') }}" class="btn btn-outline-primary">View History</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <i data-feather="search" style="width: 32px; height: 32px;" class="text-success mb-2"></i>
                    <h6>Search Books</h6>
                    <p class="text-muted small">Find available books in the library</p>
                    <a href="{{ route('portal.library.search') }}" class="btn btn-outline-success">Search Books</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
