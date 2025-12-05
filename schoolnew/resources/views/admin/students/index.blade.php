@extends('layouts.app')

@section('title', 'Students Management')

@section('page-title', 'Students Management')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item active">Students</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12">
		<!-- Success/Error Messages -->
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
					<h5>All Students {{ $academicYear ? '(' . $academicYear->name . ')' : '' }}</h5>
					<a href="{{ route('admin.students.create') }}" class="btn btn-primary">Add New</a>
				</div>
			</div>
			<div class="card-body">
				<!-- Filters -->
				<form method="GET" action="{{ route('admin.students.index') }}" class="mb-4">
					<div class="row g-3">
						<div class="col-md-3">
							<input type="text" name="search" class="form-control" placeholder="Search name, admission no..." value="{{ request('search') }}">
						</div>
						<div class="col-md-2">
							<select name="class_id" class="form-select" id="filterClass">
								<option value="">All Classes</option>
								@foreach($classes as $class)
									<option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
										{{ $class->name }}
									</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-2">
							<select name="section_id" class="form-select" id="filterSection">
								<option value="">All Sections</option>
							</select>
						</div>
						<div class="col-md-2">
							<select name="status" class="form-select">
								<option value="">All Status</option>
								<option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
								<option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
								<option value="graduated" {{ request('status') == 'graduated' ? 'selected' : '' }}>Graduated</option>
								<option value="transferred" {{ request('status') == 'transferred' ? 'selected' : '' }}>Transferred</option>
							</select>
						</div>
						<div class="col-md-2">
							<button type="submit" class="btn btn-outline-primary w-100">
								<i data-feather="search" class="me-1"></i> Filter
							</button>
						</div>
						@if(request()->hasAny(['search', 'class_id', 'section_id', 'status']))
							<div class="col-md-1">
								<a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary w-100" title="Clear Filters">
									<i data-feather="x"></i>
								</a>
							</div>
						@endif
					</div>
				</form>

				<!-- Students Table -->
				<div class="table-responsive">
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th>#</th>
								<th>Photo</th>
								<th>Admission No</th>
								<th>Name</th>
								<th>Class</th>
								<th>Section</th>
								<th>Gender</th>
								<th>Status</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							@forelse($students as $student)
								<tr>
									<td>{{ $students->firstItem() + $loop->index }}</td>
									<td>
										<div class="avatar avatar-sm">
											@if($student->photo)
												<img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->full_name }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
											@else
												<div class="avatar-title rounded-circle bg-{{ $student->gender == 'male' ? 'primary' : 'danger' }}">
													{{ strtoupper(substr($student->first_name, 0, 1)) }}
												</div>
											@endif
										</div>
									</td>
									<td><strong>{{ $student->admission_no }}</strong></td>
									<td>{{ $student->full_name }}</td>
									<td>{{ $student->schoolClass->name ?? 'N/A' }}</td>
									<td>{{ $student->section->name ?? 'N/A' }}</td>
									<td>
										<span class="badge bg-{{ $student->gender == 'male' ? 'primary' : 'danger' }}">
											{{ ucfirst($student->gender) }}
										</span>
									</td>
									<td>
										<span class="badge bg-{{ $student->status == 'active' ? 'success' : ($student->status == 'inactive' ? 'secondary' : 'warning') }}">
											{{ ucfirst($student->status) }}
										</span>
									</td>
									<td>
										<div class="common-align gap-2 justify-content-start">
											<a class="square-white" href="{{ route('admin.students.show', $student) }}" title="View">
												<svg><use href="{{ asset('assets/svg/icon-sprite.svg#eye') }}"></use></svg>
											</a>
											<a class="square-white" href="{{ route('admin.students.edit', $student) }}" title="Edit">
												<svg><use href="{{ asset('assets/svg/icon-sprite.svg#edit-content') }}"></use></svg>
											</a>
											<form action="{{ route('admin.students.destroy', $student) }}" method="POST" class="d-inline delete-form">
												@csrf
												@method('DELETE')
												<button type="button" class="square-white trash-7 border-0 bg-transparent p-0 delete-confirm" title="Delete" data-name="{{ $student->full_name }}">
													<svg><use href="{{ asset('assets/svg/icon-sprite.svg#trash1') }}"></use></svg>
												</button>
											</form>
										</div>
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
				<div class="d-flex justify-content-center mt-4">
					{{ $students->withQueryString()->links() }}
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
	// Class data for filtering
	const classesData = @json($classes);

	document.getElementById('filterClass').addEventListener('change', function() {
		const classId = this.value;
		const sectionSelect = document.getElementById('filterSection');

		sectionSelect.innerHTML = '<option value="">All Sections</option>';

		if (classId) {
			const selectedClass = classesData.find(c => c.id == classId);
			if (selectedClass && selectedClass.sections) {
				selectedClass.sections.forEach(section => {
					const option = document.createElement('option');
					option.value = section.id;
					option.textContent = section.name;
					sectionSelect.appendChild(option);
				});
			}
		}
	});

	// Trigger change on page load if class is selected
	if (document.getElementById('filterClass').value) {
		document.getElementById('filterClass').dispatchEvent(new Event('change'));
		@if(request('section_id'))
			document.getElementById('filterSection').value = '{{ request('section_id') }}';
		@endif
	}
</script>
@endpush
