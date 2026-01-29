@extends('layouts.teacher-portal')

@section('title', 'Class Students')
@section('page-title', 'Students - ' . $class->name . ' (' . $section->name . ')')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('teacher.my-classes') }}">My Classes</a></li>
<li class="breadcrumb-item active">{{ $class->name }} - {{ $section->name }}</li>
@endsection

@section('content')
<div class="row">
	<!-- Class Info Card -->
	<div class="col-12 mb-4">
		<div class="card bg-primary text-white">
			<div class="card-body">
				<div class="row align-items-center">
					<div class="col-md-8">
						<h4 class="text-white mb-1">{{ $class->name }} - Section {{ $section->name }}</h4>
						<p class="mb-0 opacity-75">
							<i class="fa fa-users me-2"></i>{{ $students->count() }} Students
						</p>
					</div>
					<div class="col-md-4 text-md-end mt-3 mt-md-0">
						<a href="{{ route('teacher.my-classes') }}" class="btn btn-light btn-sm">
							<i data-feather="arrow-left" style="width: 14px; height: 14px;"></i> Back to Classes
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Students List -->
	<div class="col-12">
		<div class="card">
			<div class="card-header pb-0">
				<div class="row align-items-center">
					<div class="col-md-6">
						<h5 class="mb-0">Student List</h5>
					</div>
					<div class="col-md-6">
						<div class="input-group">
							<span class="input-group-text bg-white"><i data-feather="search" style="width: 16px; height: 16px;"></i></span>
							<input type="text" class="form-control" id="searchStudent" placeholder="Search students...">
						</div>
					</div>
				</div>
			</div>
			<div class="card-body">
				@if($students->count() > 0)
					<div class="table-responsive">
						<table class="table table-hover" id="studentTable">
							<thead>
								<tr>
									<th>#</th>
									<th>Photo</th>
									<th>Admission No.</th>
									<th>Student Name</th>
									<th>Roll No.</th>
									<th>Gender</th>
									<th>Parent Name</th>
									<th>Contact</th>
								</tr>
							</thead>
							<tbody>
								@foreach($students as $index => $student)
									<tr>
										<td>{{ $index + 1 }}</td>
										<td>
											@if($student->photo)
												<img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->full_name }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
											@else
												<div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
													{{ strtoupper(substr($student->first_name, 0, 1)) }}
												</div>
											@endif
										</td>
										<td><span class="badge bg-light text-dark">{{ $student->admission_number }}</span></td>
										<td>
											<strong>{{ $student->full_name }}</strong>
										</td>
										<td>{{ $student->roll_number ?? '-' }}</td>
										<td>
											@if($student->gender == 'male')
												<span class="badge bg-info">Male</span>
											@elseif($student->gender == 'female')
												<span class="badge bg-pink">Female</span>
											@else
												<span class="badge bg-secondary">{{ ucfirst($student->gender) }}</span>
											@endif
										</td>
										<td>
											@if($student->parent)
												{{ $student->parent->father_name ?? $student->parent->mother_name ?? $student->parent->guardian_name ?? '-' }}
											@else
												<span class="text-muted">-</span>
											@endif
										</td>
										<td>
											@if($student->parent && $student->parent->father_phone)
												<a href="tel:{{ $student->parent->father_phone }}" class="text-decoration-none">
													<i data-feather="phone" style="width: 14px; height: 14px;"></i>
													{{ $student->parent->father_phone }}
												</a>
											@elseif($student->parent && $student->parent->mother_phone)
												<a href="tel:{{ $student->parent->mother_phone }}" class="text-decoration-none">
													<i data-feather="phone" style="width: 14px; height: 14px;"></i>
													{{ $student->parent->mother_phone }}
												</a>
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
						<i data-feather="users" style="width: 64px; height: 64px;" class="text-muted mb-3"></i>
						<h5 class="text-muted">No Students Found</h5>
						<p class="text-muted mb-0">There are no students in this class section.</p>
					</div>
				@endif
			</div>
		</div>
	</div>
</div>

@push('styles')
<style>
.bg-pink {
	background-color: #e91e8c !important;
}
</style>
@endpush

@push('scripts')
<script>
jQuery(document).ready(function() {
	jQuery('#searchStudent').on('keyup', function() {
		var value = jQuery(this).val().toLowerCase();
		jQuery('#studentTable tbody tr').filter(function() {
			jQuery(this).toggle(jQuery(this).text().toLowerCase().indexOf(value) > -1);
		});
	});
});
</script>
@endpush
@endsection
