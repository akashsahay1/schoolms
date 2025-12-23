@extends('layouts.app')

@section('title', 'Homework Submissions')

@section('page-title', 'Homework Submissions')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.homework.index') }}">Homework</a></li>
	<li class="breadcrumb-item active">Submissions</li>
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

		<div class="card mb-3">
			<div class="card-body">
				<h5>{{ $homework->title }}</h5>
				<p class="mb-2"><strong>Class:</strong> {{ $homework->schoolClass->name }}{{ $homework->section ? ' (' . $homework->section->name . ')' : '' }}</p>
				<p class="mb-2"><strong>Subject:</strong> {{ $homework->subject->name }}</p>
				<p class="mb-2"><strong>Submission Date:</strong> {{ $homework->submission_date->format('d M Y') }}</p>
				<p class="mb-0"><strong>Max Marks:</strong> {{ $homework->max_marks }}</p>
			</div>
		</div>

		<div class="row mb-3">
			<div class="col-md-3">
				<div class="card">
					<div class="card-body">
						<h6 class="text-muted">Total Students</h6>
						<h3>{{ $stats['total'] }}</h3>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="card">
					<div class="card-body">
						<h6 class="text-muted">Submitted</h6>
						<h3 class="text-success">{{ $stats['submitted'] }}</h3>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="card">
					<div class="card-body">
						<h6 class="text-muted">Pending</h6>
						<h3 class="text-warning">{{ $stats['pending'] }}</h3>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="card">
					<div class="card-body">
						<h6 class="text-muted">Evaluated</h6>
						<h3 class="text-info">{{ $stats['evaluated'] }}</h3>
					</div>
				</div>
			</div>
		</div>

		<div class="card">
			<div class="card-header">
				<h5>Submissions List</h5>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th>#</th>
								<th>Student</th>
								<th>Admission No</th>
								<th>Status</th>
								<th>Submitted Date</th>
								<th>Marks</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							@foreach($submissions as $submission)
								<tr>
									<td>{{ $loop->iteration }}</td>
									<td><strong>{{ $submission->student->full_name }}</strong></td>
									<td>{{ $submission->student->admission_no }}</td>
									<td>
										@if($submission->status === 'pending')
											<span class="badge badge-light-warning">Pending</span>
										@elseif($submission->status === 'submitted')
											<span class="badge badge-light-success">Submitted</span>
										@elseif($submission->status === 'late')
											<span class="badge badge-light-danger">Late</span>
										@elseif($submission->status === 'evaluated')
											<span class="badge badge-light-info">Evaluated</span>
										@endif
									</td>
									<td>{{ $submission->submitted_date ? $submission->submitted_date->format('d M Y H:i') : '-' }}</td>
									<td>
										@if($submission->marks_obtained !== null)
											<strong>{{ $submission->marks_obtained }}/{{ $homework->max_marks }}</strong>
										@else
											-
										@endif
									</td>
									<td>
										@if($submission->status !== 'pending' && $submission->status !== 'evaluated')
											<button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#evaluateModal{{ $submission->id }}">
												Evaluate
											</button>
										@endif
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>

				<div class="mt-3">
					<a href="{{ route('admin.homework.index') }}" class="btn btn-light">Back to Homework</a>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
