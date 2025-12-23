@extends('layouts.app')

@section('title', 'Outstanding Fees')

@section('page-title', 'Outstanding Fees')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.fees.collection') }}">Fee Collection</a></li>
	<li class="breadcrumb-item active">Outstanding</li>
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

		<div class="card">
			<div class="card-header">
				<div class="d-flex justify-content-between align-items-center">
					<h5>Outstanding Fees Report</h5>
					<span class="badge badge-light-info">Academic Year: {{ $currentYear->name }}</span>
				</div>
			</div>
			<div class="card-body">
				<form action="{{ route('admin.fees.outstanding') }}" method="GET" class="row g-3 mb-3">
					<div class="col-md-4">
						<select name="class" class="form-select" onchange="this.form.submit()">
							<option value="">All Classes</option>
							@foreach($classes as $class)
								<option value="{{ $class->id }}" {{ request('class') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-4">
						<div class="form-check mt-2">
							<input type="checkbox" class="form-check-input" id="show_only_outstanding" name="show_only_outstanding" value="1" {{ request('show_only_outstanding') ? 'checked' : '' }} onchange="this.form.submit()">
							<label class="form-check-label" for="show_only_outstanding">Show only students with outstanding fees</label>
						</div>
					</div>
				</form>

				<div class="table-responsive">
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th>#</th>
								<th>Student</th>
								<th>Admission No</th>
								<th>Class</th>
								<th>Total Fees</th>
								<th>Paid</th>
								<th>Outstanding</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							@forelse($outstandingData as $index => $data)
								<tr class="{{ $data['outstanding'] > 0 ? 'table-warning' : '' }}">
									<td>{{ $index + 1 }}</td>
									<td><strong>{{ $data['student']->full_name }}</strong></td>
									<td>{{ $data['student']->admission_no }}</td>
									<td>
										<span class="badge badge-light-info">
											{{ $data['student']->schoolClass->name }} {{ $data['student']->section ? '(' . $data['student']->section->name . ')' : '' }}
										</span>
									</td>
									<td>₹{{ number_format($data['total_fees'], 2) }}</td>
									<td><span class="text-success">₹{{ number_format($data['paid_fees'], 2) }}</span></td>
									<td>
										@if($data['outstanding'] > 0)
											<strong class="text-danger">₹{{ number_format($data['outstanding'], 2) }}</strong>
										@else
											<span class="text-success">Fully Paid</span>
										@endif
									</td>
									<td>
										@if($data['outstanding'] > 0)
											<a href="{{ route('admin.fees.collect', $data['student']) }}" class="btn btn-sm btn-primary">
												Collect Fee
											</a>
										@else
											<span class="text-muted">-</span>
										@endif
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="8" class="text-center py-4">
										<div class="text-muted">
											<i data-feather="check-circle" style="width: 48px; height: 48px;"></i>
											<p class="mt-2 mb-0">No outstanding fees found.</p>
										</div>
									</td>
								</tr>
							@endforelse
						</tbody>
						@if(count($outstandingData) > 0)
							<tfoot>
								<tr class="table-active">
									<td colspan="4"><strong>Total</strong></td>
									<td><strong>₹{{ number_format(array_sum(array_column($outstandingData, 'total_fees')), 2) }}</strong></td>
									<td><strong>₹{{ number_format(array_sum(array_column($outstandingData, 'paid_fees')), 2) }}</strong></td>
									<td><strong class="text-danger">₹{{ number_format(array_sum(array_column($outstandingData, 'outstanding')), 2) }}</strong></td>
									<td></td>
								</tr>
							</tfoot>
						@endif
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
