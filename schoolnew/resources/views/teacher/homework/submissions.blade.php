@extends('layouts.teacher-portal')

@section('title', 'Review Submissions')
@section('page-title', 'Review Submissions')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('teacher.homework.index') }}">Homework</a></li>
<li class="breadcrumb-item active">Review Submissions</li>
@endsection

@section('content')
<div class="row">
	<!-- Homework Selection -->
	<div class="col-lg-4 mb-4">
		<div class="card h-100">
			<div class="card-header pb-0">
				<h6 class="mb-0">Select Homework</h6>
			</div>
			<div class="card-body">
				@if($homeworkList->count() > 0)
					<div class="list-group list-group-flush">
						@foreach($homeworkList as $hw)
							<a href="{{ route('teacher.homework.submissions', ['homework_id' => $hw->id]) }}" class="list-group-item list-group-item-action {{ $selectedHomework && $selectedHomework->id == $hw->id ? 'active' : '' }}">
								<div class="d-flex justify-content-between align-items-center">
									<div>
										<strong>{{ Str::limit($hw->title, 25) }}</strong>
										<br>
										<small class="{{ $selectedHomework && $selectedHomework->id == $hw->id ? 'text-white-50' : 'text-muted' }}">
											{{ $hw->schoolClass->name ?? '' }} - {{ $hw->subject->name ?? '' }}
										</small>
									</div>
									@if($hw->submissions_count > 0)
										<span class="badge bg-{{ $selectedHomework && $selectedHomework->id == $hw->id ? 'light text-primary' : 'warning' }}">
											{{ $hw->submissions_count }}
										</span>
									@endif
								</div>
							</a>
						@endforeach
					</div>
				@else
					<p class="text-muted text-center mb-0">No homework assigned yet</p>
				@endif
			</div>
		</div>
	</div>

	<!-- Submissions List -->
	<div class="col-lg-8 mb-4">
		<div class="card h-100">
			<div class="card-header pb-0">
				<h6 class="mb-0">
					@if($selectedHomework)
						Submissions for: {{ $selectedHomework->title }}
					@else
						Select homework to view submissions
					@endif
				</h6>
			</div>
			<div class="card-body">
				@if($selectedHomework)
					@if($submissions->count() > 0)
						<div class="table-responsive">
							<table class="table table-hover">
								<thead>
									<tr>
										<th>Student</th>
										<th>Submitted On</th>
										<th>Status</th>
										<th>Marks</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									@foreach($submissions as $submission)
										<tr>
											<td>
												<strong>{{ $submission->student->full_name ?? 'N/A' }}</strong>
												<br>
												<small class="text-muted">{{ $submission->student->admission_number ?? '' }}</small>
											</td>
											<td>{{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y H:i') : $submission->created_at->format('M d, Y H:i') }}</td>
											<td>
												@switch($submission->status)
													@case('submitted')
														<span class="badge bg-warning">Pending Review</span>
														@break
													@case('evaluated')
														<span class="badge bg-success">Evaluated</span>
														@break
													@case('rejected')
														<span class="badge bg-danger">Rejected</span>
														@break
												@endswitch
											</td>
											<td>{{ $submission->marks ?? '-' }}</td>
											<td>
												<button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#gradeModal{{ $submission->id }}">
													<i data-feather="edit" style="width: 14px; height: 14px;"></i> Grade
												</button>
											</td>
										</tr>

										<!-- Grade Modal -->
										<div class="modal fade" id="gradeModal{{ $submission->id }}" tabindex="-1">
											<div class="modal-dialog">
												<div class="modal-content">
													<div class="modal-header">
														<h5 class="modal-title">Grade Submission</h5>
														<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
													</div>
													<form action="{{ route('teacher.homework.grade', $submission) }}" method="POST">
														@csrf
														<div class="modal-body">
															<p><strong>Student:</strong> {{ $submission->student->full_name ?? 'N/A' }}</p>
															@if($submission->file)
																<p>
																	<strong>Submitted File:</strong>
																	<a href="{{ asset('storage/' . $submission->file) }}" target="_blank" class="btn btn-sm btn-outline-primary">
																		<i data-feather="download" style="width: 14px; height: 14px;"></i> Download
																	</a>
																</p>
															@endif
															@if($submission->answer)
																<p><strong>Answer:</strong></p>
																<div class="border rounded p-2 bg-light">{{ $submission->answer }}</div>
															@endif
															<hr>
															<div class="mb-3">
																<label class="form-label">Marks</label>
																<input type="number" name="marks" class="form-control" value="{{ $submission->marks }}" min="0" step="0.5">
															</div>
															<div class="mb-3">
																<label class="form-label">Remarks</label>
																<textarea name="remarks" rows="3" class="form-control" placeholder="Optional feedback...">{{ $submission->remarks }}</textarea>
															</div>
															<div class="mb-3">
																<label class="form-label">Status</label>
																<select name="status" class="form-select" required>
																	<option value="evaluated" {{ $submission->status == 'evaluated' ? 'selected' : '' }}>Evaluated</option>
																	<option value="rejected" {{ $submission->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
																</select>
															</div>
														</div>
														<div class="modal-footer">
															<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
															<button type="submit" class="btn btn-primary">Save Grade</button>
														</div>
													</form>
												</div>
											</div>
										</div>
									@endforeach
								</tbody>
							</table>
						</div>
					@else
						<div class="text-center py-4">
							<i data-feather="inbox" style="width: 48px; height: 48px;" class="text-muted mb-3"></i>
							<p class="text-muted mb-0">No submissions received yet</p>
						</div>
					@endif
				@else
					<div class="text-center py-5">
						<i data-feather="arrow-left" style="width: 48px; height: 48px;" class="text-muted mb-3"></i>
						<p class="text-muted mb-0">Select homework from the list to view submissions</p>
					</div>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection
