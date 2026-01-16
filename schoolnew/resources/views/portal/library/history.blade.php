@extends('layouts.portal')

@section('title', 'Borrowing History')
@section('page-title', 'My Borrowing History')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('portal.library.index') }}">Library</a></li>
    <li class="breadcrumb-item active">History</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Summary -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-light-primary">
                <div class="card-body text-center">
                    <h4 class="text-primary">{{ $summary['total_borrowed'] }}</h4>
                    <p class="mb-0 text-primary">Total Books Borrowed</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light-success">
                <div class="card-body text-center">
                    <h4 class="text-success">{{ $summary['total_returned'] }}</h4>
                    <p class="mb-0 text-success">Books Returned</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light-warning">
                <div class="card-body text-center">
                    <h4 class="text-warning">{{ number_format($summary['total_fine'], 2) }}</h4>
                    <p class="mb-0 text-warning">Total Fine Paid</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('portal.library.history') }}" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="issued" {{ request('status') == 'issued' ? 'selected' : '' }}>Issued</option>
                        <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">From Date</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">To Date</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('portal.library.history') }}" class="btn btn-light">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- History Table -->
    <div class="card">
        <div class="card-header pb-0">
            <h5>Borrowing History</h5>
        </div>
        <div class="card-body">
            @if($history->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Book</th>
                                <th>Category</th>
                                <th>Issue Date</th>
                                <th>Due Date</th>
                                <th>Return Date</th>
                                <th>Fine</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($history as $issue)
                                <tr>
                                    <td>{{ $history->firstItem() + $loop->index }}</td>
                                    <td>
                                        <a href="{{ route('portal.library.show', $issue->book) }}">
                                            <strong>{{ $issue->book->title }}</strong>
                                        </a>
                                        <br><small class="text-muted">{{ $issue->book->author }}</small>
                                    </td>
                                    <td>{{ $issue->book->category->name ?? '-' }}</td>
                                    <td>{{ $issue->issue_date->format('d M Y') }}</td>
                                    <td>{{ $issue->due_date->format('d M Y') }}</td>
                                    <td>{{ $issue->return_date ? $issue->return_date->format('d M Y') : '-' }}</td>
                                    <td>
                                        @if($issue->fine_amount > 0)
                                            <span class="text-danger">{{ number_format($issue->fine_amount, 2) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($issue->status == 'returned')
                                            <span class="badge bg-success">Returned</span>
                                        @elseif($issue->is_overdue)
                                            <span class="badge bg-danger">Overdue</span>
                                        @else
                                            <span class="badge bg-warning">Issued</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($history->hasPages())
                    <div class="mt-3">{{ $history->links() }}</div>
                @endif
            @else
                <div class="text-center py-5">
                    <i data-feather="book" style="width: 64px; height: 64px; opacity: 0.5;"></i>
                    <p class="mt-3 text-muted">No borrowing history found.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
