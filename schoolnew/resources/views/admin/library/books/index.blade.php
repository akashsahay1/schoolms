@extends('layouts.app')

@section('title', 'Library Books')

@section('page-title', 'Library - Books')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item">Library</li>
	<li class="breadcrumb-item active">Books</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12">
		@if(session('success'))
			<div class="alert alert-success alert-dismissible fade show" role="alert">
				{{ session('success') }}
				<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
			</div>
		@endif

		<div class="card">
			<div class="card-header">
				<div class="d-flex justify-content-between align-items-center">
					<h5>All Books</h5>
					<a href="{{ route('admin.library.books.create') }}" class="btn btn-primary">
						<i data-feather="plus" class="me-1"></i> Add Book
					</a>
				</div>
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
								<th>Total Copies</th>
								<th>Available</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							@forelse($books as $book)
								<tr>
									<td>{{ $books->firstItem() + $loop->index }}</td>
									<td><strong>{{ $book->title }}</strong></td>
									<td>{{ $book->author }}</td>
									<td>{{ $book->isbn ?? '-' }}</td>
									<td><span class="badge badge-light-info">{{ $book->category->name ?? '-' }}</span></td>
									<td>{{ $book->total_copies }}</td>
									<td>
										<span class="badge badge-light-{{ $book->available_copies > 0 ? 'success' : 'danger' }}">
											{{ $book->available_copies }}
										</span>
									</td>
									<td>
										<div class="common-align gap-2 justify-content-start">
											<a class="square-white" href="{{ route('admin.library.books.edit', $book) }}" title="Edit">
												<svg><use href="{{ asset('assets/svg/icon-sprite.svg#edit-content') }}"></use></svg>
											</a>
											<form action="{{ route('admin.library.books.destroy', $book) }}" method="POST" class="d-inline delete-form">
												@csrf
												@method('DELETE')
												<button type="button" class="square-white trash-7 border-0 bg-transparent p-0 delete-confirm" title="Delete" data-name="{{ $book->title }}">
													<svg><use href="{{ asset('assets/svg/icon-sprite.svg#trash1') }}"></use></svg>
												</button>
											</form>
										</div>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="8" class="text-center py-4">
										<p class="text-muted">No books found.</p>
										<a href="{{ route('admin.library.books.create') }}" class="btn btn-primary">Add First Book</a>
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
</div>
@endsection
