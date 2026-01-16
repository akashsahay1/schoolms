@extends('layouts.portal')

@section('title', 'Search Books')
@section('page-title', 'Search Library Books')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('portal.library.index') }}">Library</a></li>
    <li class="breadcrumb-item active">Search</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Search Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('portal.library.search') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Book title, author, or ISBN..." value="{{ request('search') }}">
                </div>
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
                <div class="col-md-2">
                    <div class="form-check mt-4">
                        <input type="checkbox" name="available" class="form-check-input" id="available" value="1" {{ request('available') ? 'checked' : '' }}>
                        <label class="form-check-label" for="available">Available Only</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">
                        <i data-feather="search" class="me-1"></i> Search
                    </button>
                    <a href="{{ route('portal.library.search') }}" class="btn btn-light">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Results -->
    <div class="row">
        @forelse($books as $book)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex">
                            @if($book->cover_image)
                                <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="me-3" style="width: 80px; height: 100px; object-fit: cover;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center me-3" style="width: 80px; height: 100px;">
                                    <i data-feather="book" style="width: 32px; height: 32px; opacity: 0.5;"></i>
                                </div>
                            @endif
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $book->title }}</h6>
                                <p class="text-muted small mb-1">{{ $book->author }}</p>
                                <span class="badge bg-light text-dark">{{ $book->category->name ?? 'Uncategorized' }}</span>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                @if($book->available_copies > 0)
                                    <span class="badge bg-success">{{ $book->available_copies }} Available</span>
                                @else
                                    <span class="badge bg-danger">Not Available</span>
                                @endif
                            </div>
                            <a href="{{ route('portal.library.show', $book) }}" class="btn btn-sm btn-outline-primary">
                                <i data-feather="eye" style="width: 14px; height: 14px;"></i> Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i data-feather="search" style="width: 64px; height: 64px; opacity: 0.5;"></i>
                        <p class="mt-3 text-muted">No books found matching your search criteria.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    @if($books->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $books->links() }}
        </div>
    @endif
</div>
@endsection
