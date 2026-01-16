@extends('layouts.portal')

@section('title', $book->title)
@section('page-title', 'Book Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('portal.library.index') }}">Library</a></li>
    <li class="breadcrumb-item"><a href="{{ route('portal.library.search') }}">Search</a></li>
    <li class="breadcrumb-item active">{{ Str::limit($book->title, 30) }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Book Details -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-3 mb-md-0">
                            @if($book->cover_image)
                                <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="img-fluid rounded" style="max-height: 300px;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center rounded" style="height: 300px;">
                                    <i data-feather="book" style="width: 64px; height: 64px; opacity: 0.5;"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <h4 class="mb-2">{{ $book->title }}</h4>
                            <p class="text-muted mb-3">by {{ $book->author }}</p>

                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="fw-bold" style="width: 120px;">ISBN:</td>
                                    <td>{{ $book->isbn ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Publisher:</td>
                                    <td>{{ $book->publisher ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Edition:</td>
                                    <td>{{ $book->edition ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Year:</td>
                                    <td>{{ $book->published_year ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Category:</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $book->category->name ?? 'Uncategorized' }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Rack No:</td>
                                    <td>{{ $book->rack_no ?? '-' }}</td>
                                </tr>
                            </table>

                            <hr>

                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted">Availability:</span>
                                    @if($book->available_copies > 0)
                                        <span class="badge bg-success ms-2">{{ $book->available_copies }} of {{ $book->total_copies }} Available</span>
                                    @else
                                        <span class="badge bg-danger ms-2">Currently Unavailable</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($book->description)
                        <hr>
                        <h6>Description</h6>
                        <p class="text-muted">{{ $book->description }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Current Issue Status -->
            @if($currentIssue)
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0">Currently Borrowed</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>Issue Date:</strong> {{ $currentIssue->issue_date->format('d M Y') }}</p>
                        <p class="mb-2"><strong>Due Date:</strong> {{ $currentIssue->due_date->format('d M Y') }}</p>
                        @if($currentIssue->is_overdue)
                            <div class="alert alert-danger mb-0">
                                <strong>Overdue!</strong> {{ $currentIssue->overdue_days }} days overdue.
                                <br>Fine: {{ number_format($currentIssue->calculated_fine, 2) }}
                            </div>
                        @else
                            <div class="alert alert-info mb-0">
                                {{ $currentIssue->days_remaining }} days remaining
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Your History with this Book -->
            @if($borrowHistory->count() > 0)
                <div class="card mt-3">
                    <div class="card-header pb-0">
                        <h6>Your History with this Book</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @foreach($borrowHistory->take(5) as $history)
                                <li class="list-group-item px-0">
                                    <div class="d-flex justify-content-between">
                                        <small>
                                            {{ $history->issue_date->format('d M Y') }}
                                            @if($history->return_date)
                                                - {{ $history->return_date->format('d M Y') }}
                                            @endif
                                        </small>
                                        @if($history->status == 'returned')
                                            <span class="badge bg-success">Returned</span>
                                        @else
                                            <span class="badge bg-warning">Issued</span>
                                        @endif
                                    </div>
                                    @if($history->fine_amount > 0)
                                        <small class="text-danger">Fine: {{ number_format($history->fine_amount, 2) }}</small>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Back Link -->
            <div class="mt-3">
                <a href="{{ route('portal.library.search') }}" class="btn btn-outline-secondary w-100">
                    <i data-feather="arrow-left" class="me-1"></i> Back to Search
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
