@extends('layouts.app')

@section('title', 'Homework')

@section('page-title', 'Homework Management')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item active">Homework</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12">
		@if(session('success'))
			<div class="alert alert-success alert-dismissible fade show" role="alert">
				{{ session('success') }}
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
		@endif

		@if(session('error'))
			<div class="alert alert-danger alert-dismissible fade show" role="alert">
				{{ session('error') }}
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
		@endif

		<div class="card">
			<div class="card-header">
				<div class="d-flex justify-content-between align-items-center">
					<h5>All Homework</h5>
					<a href="{{ route('admin.homework.create') }}" class="btn btn-primary">
						<i data-feather="plus" class="me-1"></i> Assign Homework
					</a>
				</div>
			</div>
			<div class="card-body">
				<form action="{{ route('admin.homework.index') }}" method="GET" class="row g-3 mb-3">
					<div class="col-md-3">
						<select name="academic_year" class="form-select" onchange="this.form.submit()">
							<option value="">All Academic Years</option>
							@foreach($academicYears as $year)
								<option value="{{ $year->id }}" {{ request('academic_year') == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-2">
						<select name="class" class="form-select" onchange="this.form.submit()">
							<option value="">All Classes</option>
							@foreach($classes as $class)
								<option value="{{ $class->id }}" {{ request('class') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-2">
						<select name="subject" class="form-select" onchange="this.form.submit()">
							<option value="">All Subjects</option>
							@foreach($subjects as $subject)
								<option value="{{ $subject->id }}" {{ request('subject') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-2">
						<select name="status" class="form-select" onchange="this.form.submit()">
							<option value="">All Status</option>
							<option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
							<option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
						</select>
					</div>
				</form>

				<div class="table-responsive">
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th>#</th>
								<th>Title</th>
								<th>Class</th>
								<th>Subject</th>
								<th>Homework Date</th>
								<th>Submission Date</th>
								<th>Status</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							@forelse($homeworks as $homework)
								<tr>
									<td>{{ $homeworks->firstItem() + $loop->index }}</td>
									<td><strong>{{ $homework->title }}</strong></td>
									<td>
										<span class="badge badge-light-info">
											{{ $homework->schoolClass->name }}{{ $homework->section ? ' (' . $homework->section->name . ')' : '' }}
										</span>
									</td>
									<td>{{ $homework->subject->name }}</td>
									<td>{{ $homework->homework_date->format('d M Y') }}</td>
									<td>{{ $homework->submission_date->format('d M Y') }}</td>
									<td>
										@if($homework->is_overdue)
											<span class="badge badge-light-danger">Overdue</span>
										@else
											<span class="badge badge-light-success">Active</span>
										@endif
									</td>
									<td>
										<div class="common-align gap-2 justify-content-start">
											<a class="square-white" href="{{ route('admin.homework.submissions', $homework) }}" title="View Submissions">
												<svg><use href="{{ asset('assets/svg/icon-sprite.svg#eye') }}"></use></svg>
											</a>
											<a class="square-white" href="{{ route('admin.homework.edit', $homework) }}" title="Edit">
												<svg><use href="{{ asset('assets/svg/icon-sprite.svg#edit-content') }}"></use></svg>
											</a>
											<form action="{{ route('admin.homework.destroy', $homework) }}" method="POST" class="d-inline delete-form">
												@csrf
												@method('DELETE')
												<button type="button" class="square-white trash-7 border-0 bg-transparent p-0 delete-confirm" title="Delete" data-name="{{ $homework->title }}">
													<svg><use href="{{ asset('assets/svg/icon-sprite.svg#trash1') }}"></use></svg>
												</button>
											</form>
										</div>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="8" class="text-center py-4">
										<div class="text-muted">
											<i data-feather="book-open" style="width: 48px; height: 48px;"></i>
											<p class="mt-2 mb-0">No homework found.</p>
											<a href="{{ route('admin.homework.create') }}" class="btn btn-primary mt-3">Assign First Homework</a>
										</div>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				@if($homeworks->hasPages())
					<div class="mt-3">
						{{ $homeworks->links() }}
					</div>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection
