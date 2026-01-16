@extends('layouts.app')

@section('title', 'Book Inventory Report')

@section('page-title', 'Book Inventory Report')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.library.reports.index') }}">Library Reports</a></li>
    <li class="breadcrumb-item active">Inventory</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-2 col-sm-4">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h6 class="text-white">Total Titles</h6>
                    <h3 class="mb-0">{{ $summary['total_titles'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h6 class="text-white">Total Copies</h6>
                    <h3 class="mb-0">{{ $summary['total_copies'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h6 class="text-white">Available</h6>
                    <h3 class="mb-0">{{ $summary['available_copies'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h6 class="text-white">Issued</h6>
                    <h3 class="mb-0">{{ $summary['issued_copies'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-8">
            <div class="card bg-secondary text-white">
                <div class="card-body text-center">
                    <h6 class="text-white">Total Inventory Value</h6>
                    <h3 class="mb-0">{{ number_format($summary['total_value'], 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter and Export -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.library.reports.inventory') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Availability</label>
                    <select name="availability" class="form-select">
                        <option value="">All Books</option>
                        <option value="available" {{ request('availability') == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="not_available" {{ request('availability') == 'not_available' ? 'selected' : '' }}>Not Available</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-primary">
                        <i data-feather="filter" class="me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.library.reports.export', ['type' => 'inventory']) }}" class="btn btn-success">
                        <i data-feather="download" class="me-1"></i> Export CSV
                    </a>
                    <a href="{{ route('admin.library.reports.inventory') }}" class="btn btn-light">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card">
        <div class="card-header pb-0">
            <h5>Book Inventory</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>ISBN</th>
                            <th>Category</th>
                            <th>Total</th>
                            <th>Available</th>
                            <th>Issued</th>
                            <th>Price</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($books as $book)
                            <tr>
                                <td>{{ $books->firstItem() + $loop->index }}</td>
                                <td><strong>{{ $book->title }}</strong></td>
                                <td>{{ $book->author }}</td>
                                <td>{{ $book->isbn }}</td>
                                <td>{{ $book->category->name ?? '-' }}</td>
                                <td>{{ $book->total_copies }}</td>
                                <td>
                                    <span class="badge bg-{{ $book->available_copies > 0 ? 'success' : 'danger' }}">
                                        {{ $book->available_copies }}
                                    </span>
                                </td>
                                <td>{{ $book->total_copies - $book->available_copies }}</td>
                                <td>{{ number_format($book->price, 2) }}</td>
                                <td>{{ number_format($book->price * $book->total_copies, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <p class="text-muted">No books found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($books->hasPages())
                <div class="mt-3">{{ $books->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
