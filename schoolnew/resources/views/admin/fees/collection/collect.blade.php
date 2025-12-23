@extends('layouts.app')

@section('title', 'Collect Fee')

@section('page-title', 'Collect Fee')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.fees.collection') }}">Fee Collection</a></li>
	<li class="breadcrumb-item active">Collect Fee</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12">
		@if(session('error'))
			<div class="alert alert-danger alert-dismissible fade show" role="alert">
				{{ session('error') }}
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
		@endif

		<div class="card mb-3">
			<div class="card-body">
				<div class="row">
					<div class="col-md-4">
						<h6 class="text-muted">Student Information</h6>
						<p class="mb-1"><strong>Name:</strong> {{ $student->full_name }}</p>
						<p class="mb-1"><strong>Admission No:</strong> {{ $student->admission_no }}</p>
						<p class="mb-1"><strong>Class:</strong> {{ $student->schoolClass->name }} {{ $student->section ? '(' . $student->section->name . ')' : '' }}</p>
					</div>
					<div class="col-md-4">
						<h6 class="text-muted">Contact Information</h6>
						<p class="mb-1"><strong>Email:</strong> {{ $student->email ?? '-' }}</p>
						<p class="mb-1"><strong>Phone:</strong> {{ $student->phone ?? '-' }}</p>
					</div>
					<div class="col-md-4">
						<h6 class="text-muted">Academic Year</h6>
						<p class="mb-1"><strong>{{ $currentYear->name }}</strong></p>
					</div>
				</div>
			</div>
		</div>

		<div class="card">
			<div class="card-header">
				<h5>Fee Details</h5>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>Fee Type</th>
								<th>Fee Group</th>
								<th>Amount</th>
								<th>Due Date</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							@foreach($feeStructures as $structure)
								<tr>
									<td>{{ $structure->feeType->name }}</td>
									<td>{{ $structure->feeGroup->name }}</td>
									<td><strong>₹{{ number_format($structure->amount, 2) }}</strong></td>
									<td>{{ $structure->due_date ? $structure->due_date->format('d M Y') : '-' }}</td>
									<td>
										@if(in_array($structure->id, $paidFees))
											<span class="badge badge-light-success">Paid</span>
										@else
											<span class="badge badge-light-warning">Pending</span>
										@endif
									</td>
									<td>
										@if(in_array($structure->id, $paidFees))
											<span class="text-muted">Already Paid</span>
										@else
											<button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#collectModal{{ $structure->id }}">
												Collect Fee
											</button>
										@endif
									</td>
								</tr>

								@if(!in_array($structure->id, $paidFees))
									<div class="modal fade" id="collectModal{{ $structure->id }}" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<form action="{{ route('admin.fees.collection.store') }}" method="POST">
													@csrf
													<input type="hidden" name="student_id" value="{{ $student->id }}">
													<input type="hidden" name="fee_structure_id" value="{{ $structure->id }}">
													<input type="hidden" name="academic_year_id" value="{{ $currentYear->id }}">
													<input type="hidden" name="amount" value="{{ $structure->amount }}">

													<div class="modal-header">
														<h5 class="modal-title">Collect {{ $structure->feeType->name }}</h5>
														<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
													</div>
													<div class="modal-body">
														<div class="mb-3">
															<label class="form-label">Fee Amount</label>
															<input type="text" class="form-control" value="₹{{ number_format($structure->amount, 2) }}" readonly>
														</div>

														<div class="mb-3">
															<label for="discount_amount{{ $structure->id }}" class="form-label">Discount Amount</label>
															<input type="number" step="0.01" class="form-control" id="discount_amount{{ $structure->id }}" name="discount_amount" value="0" min="0" max="{{ $structure->amount }}">
														</div>

														<div class="mb-3">
															<label for="fine_amount{{ $structure->id }}" class="form-label">Fine Amount</label>
															<input type="number" step="0.01" class="form-control" id="fine_amount{{ $structure->id }}" name="fine_amount" value="{{ $structure->fine_amount }}" min="0">
														</div>

														<div class="mb-3">
															<label for="payment_mode{{ $structure->id }}" class="form-label">Payment Mode <span class="text-danger">*</span></label>
															<select class="form-select" id="payment_mode{{ $structure->id }}" name="payment_mode" required>
																<option value="">Select Payment Mode</option>
																<option value="cash">Cash</option>
																<option value="cheque">Cheque</option>
																<option value="card">Card</option>
																<option value="online">Online</option>
																<option value="bank_transfer">Bank Transfer</option>
															</select>
														</div>

														<div class="mb-3">
															<label for="transaction_id{{ $structure->id }}" class="form-label">Transaction ID / Cheque No</label>
															<input type="text" class="form-control" id="transaction_id{{ $structure->id }}" name="transaction_id">
														</div>

														<div class="mb-3">
															<label for="payment_date{{ $structure->id }}" class="form-label">Payment Date <span class="text-danger">*</span></label>
															<input type="date" class="form-control" id="payment_date{{ $structure->id }}" name="payment_date" value="{{ date('Y-m-d') }}" required>
														</div>

														<div class="mb-3">
															<label for="remarks{{ $structure->id }}" class="form-label">Remarks</label>
															<textarea class="form-control" id="remarks{{ $structure->id }}" name="remarks" rows="2"></textarea>
														</div>
													</div>
													<div class="modal-footer">
														<button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
														<button type="submit" class="btn btn-primary">Collect Fee</button>
													</div>
												</form>
											</div>
										</div>
									</div>
								@endif
							@endforeach
						</tbody>
					</table>
				</div>

				<div class="mt-3">
					<a href="{{ route('admin.fees.collection') }}" class="btn btn-light">Back to Collection</a>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
