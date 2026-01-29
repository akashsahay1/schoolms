@extends('layouts.teacher-portal')

@section('title', 'My Classes')
@section('page-title', 'My Classes')

@section('breadcrumb')
<li class="breadcrumb-item active">My Classes</li>
@endsection

@section('content')
<div class="row">
	<!-- Help Tip -->
	<div class="col-12 mb-4">
		<div class="help-tip">
			<i data-feather="info" class="me-2 text-primary"></i>
			<strong>Your Assigned Classes:</strong> Click on any class card to view the student list for that class.
		</div>
	</div>

	@if($classes->count() > 0)
		@foreach($classes as $key => $classData)
			<div class="col-xl-4 col-md-6 mb-4">
				<a href="{{ route('teacher.class-students', [explode('-', $key)[0], explode('-', $key)[1]]) }}" class="card quick-action-card h-100 text-decoration-none">
					<div class="card-body">
						<div class="d-flex align-items-center mb-3">
							<div class="quick-action-icon bg-primary bg-opacity-10 me-3">
								<i data-feather="book-open" class="text-primary"></i>
							</div>
							<div>
								<h5 class="mb-0 text-dark">{{ $classData['class']->name ?? 'N/A' }}</h5>
								@if($classData['section'])
									<span class="text-muted">Section: {{ $classData['section']->name }}</span>
								@endif
							</div>
						</div>

						<div class="row text-center border-top pt-3">
							<div class="col-6 border-end">
								<h4 class="mb-0 text-primary">{{ $classData['student_count'] }}</h4>
								<small class="text-muted">Students</small>
							</div>
							<div class="col-6">
								<h4 class="mb-0 text-success">{{ $classData['subjects']->count() }}</h4>
								<small class="text-muted">Subjects</small>
							</div>
						</div>

						@if($classData['subjects']->count() > 0)
							<div class="mt-3">
								<small class="text-muted d-block mb-2">Your Subjects:</small>
								<div class="d-flex flex-wrap gap-1">
									@foreach($classData['subjects']->take(4) as $subject)
										<span class="badge bg-light text-dark">{{ $subject->name }}</span>
									@endforeach
									@if($classData['subjects']->count() > 4)
										<span class="badge bg-primary">+{{ $classData['subjects']->count() - 4 }}</span>
									@endif
								</div>
							</div>
						@endif
					</div>
					<div class="card-footer bg-light text-center py-2">
						<span class="text-primary">
							View Students <i data-feather="chevron-right" style="width: 16px; height: 16px;"></i>
						</span>
					</div>
				</a>
			</div>
		@endforeach
	@else
		<div class="col-12">
			<div class="card">
				<div class="card-body text-center py-5">
					<i data-feather="book" style="width: 64px; height: 64px;" class="text-muted mb-3"></i>
					<h5 class="text-muted">No Classes Assigned</h5>
					<p class="text-muted mb-0">You don't have any classes assigned to you yet. Please contact the administrator.</p>
				</div>
			</div>
		</div>
	@endif
</div>
@endsection
