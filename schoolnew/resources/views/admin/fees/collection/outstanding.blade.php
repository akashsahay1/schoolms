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
				<form action="{{ route('admin.fees.outstanding') }}" method="GET" class="row g-3 align-items-end mb-4">
					<div class="col-md-3">
						<label class="form-label">Search Student</label>
						<input type="text" name="search" class="form-control" placeholder="Name, Admission No..." value="{{ request('search') }}">
					</div>
					<div class="col-md-2">
						<label class="form-label">Class</label>
						<select name="class" class="form-select">
							<option value="">All Classes</option>
							@foreach($classes as $class)
								<option value="{{ $class->id }}" {{ request('class') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-3">
						<div class="form-check mt-4">
							<input type="checkbox" class="form-check-input" id="show_only_outstanding" name="show_only_outstanding" value="1" {{ request('show_only_outstanding') ? 'checked' : '' }}>
							<label class="form-check-label" for="show_only_outstanding">Only outstanding</label>
						</div>
					</div>
					<div class="col-md-4">
						<div class="d-flex gap-2">
							<button type="submit" class="btn btn-primary">
								<i class="icon-filter me-1"></i> Filter
							</button>
							@if(request()->hasAny(['search', 'class', 'show_only_outstanding']))
								<a href="{{ route('admin.fees.outstanding') }}" class="btn btn-outline-secondary" title="Reset">
									<i class="icon-reload"></i>
								</a>
							@endif
						</div>
					</div>
				</form>

				<div class="table-responsive">
					<table class="table table-hover mb-0">
						<thead class="bg-light">
							<tr>
								<th style="width: 4%;">#</th>
								<th style="width: 18%;">Student</th>
								<th style="width: 10%;">Admission</th>
								<th style="width: 10%;">Class</th>
								<th style="width: 12%;" class="text-end">Academic</th>
								<th style="width: 12%;" class="text-end">Transport</th>
								<th style="width: 12%;" class="text-end">Paid</th>
								<th style="width: 12%;" class="text-end">Outstanding</th>
								<th style="width: 10%;" class="text-center">Action</th>
							</tr>
						</thead>
						<tbody>
							@forelse($outstandingData as $index => $data)
								<tr>
									<td class="text-muted">{{ $index + 1 }}</td>
									<td><strong>{{ $data['student']->full_name }}</strong></td>
									<td><span class="text-muted">{{ $data['student']->admission_no }}</span></td>
									<td>
										{{ $data['student']->schoolClass->name ?? '-' }}
										@if($data['student']->section)
											<span class="text-muted">({{ $data['student']->section->name }})</span>
										@endif
									</td>
									<td class="text-end">₹{{ number_format($data['academic_fees'], 2) }}</td>
									<td class="text-end">
										@if($data['has_transport'])
											₹{{ number_format($data['transport_fees'], 2) }}
										@else
											<span class="text-muted">-</span>
										@endif
									</td>
									<td class="text-end text-success">₹{{ number_format($data['paid_fees'], 2) }}</td>
									<td class="text-end">
										@if($data['outstanding'] > 0)
											<strong class="text-danger">₹{{ number_format($data['outstanding'], 2) }}</strong>
										@else
											<span class="badge badge-light-success px-2">Paid</span>
										@endif
									</td>
									<td class="text-center">
										@if($data['outstanding'] > 0)
											<a href="{{ route('admin.fees.collect', $data['student']) }}" class="btn btn-sm btn-primary" style="color: #fff;">
												<i class="icon-money me-1" style="color: #fff;"></i> Collect
											</a>
										@else
											<span class="badge badge-light-success">Paid</span>
										@endif
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="9" class="text-center py-5">
										<div class="d-flex flex-column align-items-center">
											<div class="rounded-circle bg-light d-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px;">
												<i class="icon-check" style="font-size: 24px; color: #27ae60;"></i>
											</div>
											<p class="text-muted mb-0">No outstanding fees found.</p>
										</div>
									</td>
								</tr>
							@endforelse
						</tbody>
						@if(count($outstandingData) > 0)
							<tfoot class="bg-light">
								<tr class="fw-bold">
									<td colspan="4">Total</td>
									<td class="text-end">₹{{ number_format(array_sum(array_column($outstandingData, 'academic_fees')), 2) }}</td>
									<td class="text-end">₹{{ number_format(array_sum(array_column($outstandingData, 'transport_fees')), 2) }}</td>
									<td class="text-end text-success">₹{{ number_format(array_sum(array_column($outstandingData, 'paid_fees')), 2) }}</td>
									<td class="text-end text-danger">₹{{ number_format(array_sum(array_column($outstandingData, 'outstanding')), 2) }}</td>
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
