@extends('layouts.app')

@section('title', 'Student Reports')

@section('page-title', 'Student Reports')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item active">Student Reports</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12">
		<!-- Statistics Cards -->
		<div class="row mb-4">
			<div class="col-md-3">
				<div class="card bg-light-primary">
					<div class="card-body text-center py-3">
						<h3 class="mb-1">{{ $stats['total'] }}</h3>
						<p class="mb-0 text-muted">Total Students</p>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="card bg-light-success">
					<div class="card-body text-center py-3">
						<h3 class="mb-1">{{ $stats['active'] }}</h3>
						<p class="mb-0 text-muted">Active</p>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="card bg-light-info">
					<div class="card-body text-center py-3">
						<h3 class="mb-1">{{ $stats['male'] }}</h3>
						<p class="mb-0 text-muted">Male</p>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="card bg-light-warning">
					<div class="card-body text-center py-3">
						<h3 class="mb-1">{{ $stats['female'] }}</h3>
						<p class="mb-0 text-muted">Female</p>
					</div>
				</div>
			</div>
		</div>

		<div class="card">
			<div class="card-header">
				<div class="d-flex justify-content-between align-items-center">
					<h5>Student Report</h5>
					<a href="{{ route('admin.reports.students.export', request()->all()) }}" class="btn btn-success">
						<i data-feather="download" class="me-1"></i> Export CSV
					</a>
				</div>
			</div>
			<div class="card-body">
				<!-- Filters -->
				<form method="GET" action="{{ route('admin.reports.students') }}" class="mb-4">
					<div class="row g-3">
						<div class="col-md-2">
							<select name="class_id" class="form-select" id="classSelect">
								<option value="">All Classes</option>
								@foreach($classes as $class)
									<option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
										{{ $class->name }}
									</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-2">
							<select name="section_id" class="form-select" id="sectionSelect">
								<option value="">All Sections</option>
								@foreach($sections as $section)
									<option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>
										{{ $section->name }}
									</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-2">
							<select name="gender" class="form-select">
								<option value="">All Gender</option>
								<option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
								<option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
							</select>
						</div>
						<div class="col-md-2">
							<select name="status" class="form-select">
								<option value="">All Status</option>
								<option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
								<option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
								<option value="alumni" {{ request('status') == 'alumni' ? 'selected' : '' }}>Alumni</option>
							</select>
						</div>
						<div class="col-md-2">
							<input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
						</div>
						<div class="col-md-2">
							<div class="d-flex gap-2">
								<button type="submit" class="btn btn-primary flex-fill">
									<i data-feather="filter" class="me-1"></i> Filter
								</button>
								<a href="{{ route('admin.reports.students') }}" class="btn btn-outline-secondary">
									<i data-feather="x"></i>
								</a>
							</div>
						</div>
					</div>
				</form>

				<!-- Students Table -->
				<div class="table-responsive">
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th>#</th>
								<th>Admission No</th>
								<th>Student Name</th>
								<th>Class</th>
								<th>Section</th>
								<th>Gender</th>
								<th>DOB</th>
								<th>Phone</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
							@forelse($students as $student)
								<tr>
									<td>{{ $students->firstItem() + $loop->index }}</td>
									<td>{{ $student->admission_no }}</td>
									<td>
										<div class="d-flex align-items-center">
											<img src="{{ $student->photo_url }}" alt="Photo" class="rounded-circle me-2" width="32" height="32">
											<span>{{ $student->full_name }}</span>
										</div>
									</td>
									<td>{{ $student->schoolClass->name ?? '-' }}</td>
									<td>{{ $student->section->name ?? '-' }}</td>
									<td>{{ ucfirst($student->gender ?? '-') }}</td>
									<td>{{ $student->date_of_birth?->format('M d, Y') ?? '-' }}</td>
									<td>{{ $student->phone ?? '-' }}</td>
									<td>
										<span class="badge bg-{{ $student->status === 'active' ? 'success' : ($student->status === 'alumni' ? 'info' : 'secondary') }}">
											{{ ucfirst($student->status) }}
										</span>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="9" class="text-center py-4">
										<div class="text-muted">
											<i data-feather="users" style="width: 48px; height: 48px;"></i>
											<p class="mt-2 mb-0">No students found.</p>
										</div>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				<!-- Pagination -->
				<div class="d-flex justify-content-between align-items-center mt-4">
					<div class="text-muted">
						Showing {{ $students->firstItem() ?? 0 }} to {{ $students->lastItem() ?? 0 }} of {{ $students->total() }} entries
					</div>
					{{ $students->withQueryString()->links() }}
				</div>
			</div>
		</div>

		<!-- Class-wise Distribution -->
		@if($classWiseStats->isNotEmpty())
		<div class="card mt-4">
			<div class="card-header">
				<h5>Class-wise Student Distribution</h5>
			</div>
			<div class="card-body">
				<div class="row">
					@foreach($classWiseStats as $stat)
						<div class="col-md-2 mb-3">
							<div class="card border">
								<div class="card-body text-center py-3">
									<h4 class="mb-1">{{ $stat->count }}</h4>
									<p class="mb-0 text-muted small">{{ $stat->schoolClass->name ?? 'Unknown' }}</p>
								</div>
							</div>
						</div>
					@endforeach
				</div>
			</div>
		</div>
		@endif
	</div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
	if (typeof feather !== 'undefined') {
		feather.replace();
	}
});
</script>
@endpush
