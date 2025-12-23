@extends('layouts.app')

@section('title', 'Issue Book')

@section('page-title', 'Library - Issue Book')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.library.issue.index') }}">Book Issue</a></li>
	<li class="breadcrumb-item active">Issue Book</li>
@endsection

@section('content')
<div class="row">
	<div class="col-md-10 col-lg-8">
		<div class="card">
			<div class="card-header">
				<h5>Issue Book to Student</h5>
			</div>
			<div class="card-body">
				@if(session('error'))
					<div class="alert alert-danger alert-dismissible fade show" role="alert">
						{{ session('error') }}
						<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
					</div>
				@endif

				@if($errors->any())
					<div class="alert alert-danger alert-dismissible fade show" role="alert">
						<strong>Please correct the following errors:</strong>
						<ul class="mb-0 mt-2">
							@foreach($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
						<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
					</div>
				@endif

				<form action="{{ route('admin.library.issue.store') }}" method="POST">
					@csrf

					<div class="mb-3">
						<label for="book_id" class="form-label">Book <span class="text-danger">*</span></label>
						<select class="form-select @error('book_id') is-invalid @enderror" id="book_id" name="book_id" required>
							<option value="">Select Book</option>
							@foreach($books as $book)
								<option value="{{ $book->id }}" {{ old('book_id') == $book->id ? 'selected' : '' }}>
									{{ $book->title }} - {{ $book->author }} (Available: {{ $book->available_copies }})
								</option>
							@endforeach
						</select>
						@error('book_id')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>

					<div class="mb-3">
						<label for="student_id" class="form-label">Student <span class="text-danger">*</span></label>
						<select class="form-select @error('student_id') is-invalid @enderror" id="student_id" name="student_id" required>
							<option value="">Select Student</option>
							@foreach($students as $student)
								<option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
									{{ $student->full_name }} - {{ $student->class->name ?? '' }} {{ $student->section->name ?? '' }}
								</option>
							@endforeach
						</select>
						@error('student_id')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>

					<div class="row">
						<div class="col-md-6 mb-3">
							<label for="issue_date" class="form-label">Issue Date <span class="text-danger">*</span></label>
							<input type="date" class="form-control @error('issue_date') is-invalid @enderror" id="issue_date" name="issue_date" value="{{ old('issue_date', date('Y-m-d')) }}" required>
							@error('issue_date')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>

						<div class="col-md-6 mb-3">
							<label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
							<input type="date" class="form-control @error('due_date') is-invalid @enderror" id="due_date" name="due_date" value="{{ old('due_date', date('Y-m-d', strtotime('+14 days'))) }}" required>
							@error('due_date')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>

					<div class="mb-3">
						<label for="remarks" class="form-label">Remarks</label>
						<textarea class="form-control @error('remarks') is-invalid @enderror" id="remarks" name="remarks" rows="2">{{ old('remarks') }}</textarea>
						@error('remarks')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>

					<div class="d-flex justify-content-end gap-2">
						<a href="{{ route('admin.library.issue.index') }}" class="btn btn-light">Cancel</a>
						<button type="submit" class="btn btn-primary">
							<i data-feather="book-open" class="me-1"></i> Issue Book
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection
