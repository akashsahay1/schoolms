@extends('layouts.app')

@section('title', 'Collection Report')
@section('page-title', 'Collection Report')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.fees.reports.index') }}">Fee Reports</a></li>
	<li class="breadcrumb-item active">Collection</li>
@endsection

@push('styles')
<style>
	.stat-card-sm .card-body { padding: 1.25rem; }
	.stat-card-sm .stat-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
	.stat-card-sm p { font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
	.stat-card-sm h4 { font-size: 1.4rem; }
</style>
@endpush

@section('content')
<div class="container-fluid">
	<!-- Filters -->
	<div class="card mb-4">
		<div class="card-header">
			<h6 class="mb-0">Filter Report</h6>
		</div>
		<div class="card-body">
			<form method="GET" action="{{ route('admin.fees.reports.collection') }}" class="row g-3 align-items-end">
				<div class="col-md-2">
					<label class="form-label">From Date</label>
					<input type="date" name="from_date" class="form-control" value="{{ $fromDate }}">
				</div>
				<div class="col-md-2">
					<label class="form-label">To Date</label>
					<input type="date" name="to_date" class="form-control" value="{{ $toDate }}">
				</div>
				<div class="col-md-2">
					<label class="form-label">Class</label>
					<select name="class_id" class="form-select">
						<option value="">All Classes</option>
						@foreach($classes as $class)
							<option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-2">
					<label class="form-label">Fee Type</label>
					<select name="fee_type_id" class="form-select">
						<option value="">All Types</option>
						@foreach($feeTypes as $type)
							<option value="{{ $type->id }}" {{ request('fee_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-2">
					<label class="form-label">Student</label>
					<select name="student_id" class="form-select" id="studentFilter">
						<option value="">All Students</option>
						@foreach($students as $s)
							<option value="{{ $s->id }}" {{ request('student_id') == $s->id ? 'selected' : '' }}>{{ $s->full_name }} ({{ $s->admission_no }})</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-2">
					<div class="d-flex gap-2">
						<button type="submit" class="btn btn-primary flex-fill">
							<i class="icon-filter me-1"></i> Filter
						</button>
						<a href="{{ route('admin.fees.reports.collection') }}" class="btn btn-outline-secondary" title="Reset">
							<i class="icon-reload"></i>
						</a>
					</div>
				</div>
			</form>
		</div>
	</div>

	<!-- Student Info Banner (when student selected) -->
	@if($selectedStudent)
	<div class="alert alert-primary d-flex align-items-center mb-4">
		<i class="icon-user me-2" style="font-size: 18px;"></i>
		<div>
			Showing results for <strong>{{ $selectedStudent->full_name }}</strong> ({{ $selectedStudent->admission_no }}) — {{ $selectedStudent->schoolClass->name ?? '' }}
			<a href="{{ route('admin.fees.reports.collection', ['from_date' => $fromDate, 'to_date' => $toDate]) }}" class="ms-2 text-white">Clear Student Filter</a>
		</div>
	</div>
	@endif

	<!-- Summary Cards -->
	<div class="row mb-4">
		@if($studentStats)
			<!-- Student-specific stats -->
			<div class="col-md-4 mb-3">
				<div class="card stat-card-sm border-0 shadow-sm h-100">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center">
							<div>
								<p class="text-muted mb-1">Total Fees</p>
								<h4 class="mb-0">₹{{ number_format($studentStats['total_fees'], 2) }}</h4>
							</div>
							<div class="stat-icon bg-primary bg-opacity-10">
								<span class="text-primary fw-bold" style="font-size: 18px;">₹</span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-4 mb-3">
				<div class="card stat-card-sm border-0 shadow-sm h-100">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center">
							<div>
								<p class="text-muted mb-1">Total Paid</p>
								<h4 class="mb-0 text-success">₹{{ number_format($studentStats['total_paid'], 2) }}</h4>
							</div>
							<div class="stat-icon bg-success bg-opacity-10">
								<i data-feather="check-circle" class="text-success" style="width: 20px; height: 20px;"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-4 mb-3">
				<div class="card stat-card-sm border-0 shadow-sm h-100">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center">
							<div>
								<p class="text-muted mb-1">Due Amount</p>
								@if($studentStats['total_due'] > 0)
									<h4 class="mb-0 text-danger">₹{{ number_format($studentStats['total_due'], 2) }}</h4>
								@elseif($studentStats['total_due'] < 0)
									<h4 class="mb-0 text-info">Advance ₹{{ number_format(abs($studentStats['total_due']), 2) }}</h4>
								@else
									<h4 class="mb-0 text-success">All Paid</h4>
								@endif
							</div>
							<div class="stat-icon {{ $studentStats['total_due'] > 0 ? 'bg-danger' : ($studentStats['total_due'] < 0 ? 'bg-info' : 'bg-success') }} bg-opacity-10">
								<i data-feather="{{ $studentStats['total_due'] > 0 ? 'alert-circle' : ($studentStats['total_due'] < 0 ? 'arrow-up-circle' : 'check') }}" class="{{ $studentStats['total_due'] > 0 ? 'text-danger' : ($studentStats['total_due'] < 0 ? 'text-info' : 'text-success') }}" style="width: 20px; height: 20px;"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
		@else
			<!-- Overall stats -->
			<div class="col-md-3 mb-3">
				<div class="card stat-card-sm border-0 shadow-sm h-100">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center">
							<div>
								<p class="text-muted mb-1">Total Collected</p>
								<h4 class="mb-0 text-success">₹{{ number_format($summary['total_amount'], 2) }}</h4>
							</div>
							<div class="stat-icon bg-success bg-opacity-10">
								<span class="text-success fw-bold" style="font-size: 18px;">₹</span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3 mb-3">
				<div class="card stat-card-sm border-0 shadow-sm h-100">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center">
							<div>
								<p class="text-muted mb-1">Discount</p>
								<h4 class="mb-0 text-warning">₹{{ number_format($summary['total_discount'], 2) }}</h4>
							</div>
							<div class="stat-icon bg-warning bg-opacity-10">
								<i data-feather="percent" class="text-warning" style="width: 18px; height: 18px;"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3 mb-3">
				<div class="card stat-card-sm border-0 shadow-sm h-100">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center">
							<div>
								<p class="text-muted mb-1">Fine</p>
								<h4 class="mb-0 text-danger">₹{{ number_format($summary['total_fine'], 2) }}</h4>
							</div>
							<div class="stat-icon bg-danger bg-opacity-10">
								<i data-feather="clock" class="text-danger" style="width: 18px; height: 18px;"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3 mb-3">
				<div class="card stat-card-sm border-0 shadow-sm h-100">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center">
							<div>
								<p class="text-muted mb-1">Transactions</p>
								<h4 class="mb-0 text-primary">{{ number_format($summary['total_transactions']) }}</h4>
							</div>
							<div class="stat-icon bg-primary bg-opacity-10">
								<i data-feather="hash" class="text-primary" style="width: 18px; height: 18px;"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
		@endif
	</div>

	<div class="row">
		<!-- Collection Table -->
		<div class="col-xl-8 mb-4">
			<div class="card">
				<div class="card-header d-flex justify-content-between align-items-center">
					<h6 class="mb-0">Collection Details</h6>
					<div class="d-flex gap-2">
						<a href="{{ route('admin.fees.reports.export-excel', ['type' => 'collection', 'from_date' => $fromDate, 'to_date' => $toDate] + request()->only(['class_id', 'fee_type_id', 'student_id'])) }}" class="btn btn-sm btn-outline-success">
							<i data-feather="download" style="width: 14px; height: 14px;" class="me-1"></i> Excel
						</a>
						<a href="{{ route('admin.fees.reports.export-pdf', ['type' => 'collection', 'from_date' => $fromDate, 'to_date' => $toDate] + request()->only(['class_id', 'fee_type_id', 'student_id'])) }}" class="btn btn-sm btn-outline-danger">
							<i data-feather="file-text" style="width: 14px; height: 14px;" class="me-1"></i> PDF
						</a>
					</div>
				</div>
				<div class="card-body p-0">
					<div class="table-responsive">
						<table class="table table-hover mb-0">
							<thead class="bg-light">
								<tr>
									<th>Receipt No</th>
									<th>Date</th>
									@if(!$selectedStudent)
										<th>Student</th>
										<th>Class</th>
									@endif
									<th>Fee Type</th>
									<th class="text-end">Amount</th>
									@if($selectedStudent)
										<th class="text-center">Status</th>
									@endif
								</tr>
							</thead>
							<tbody>
								@forelse($collections as $collection)
									<tr>
										<td>
											<a href="{{ route('admin.fees.receipt', $collection) }}" class="text-primary">{{ $collection->receipt_no }}</a>
										</td>
										<td>{{ $collection->payment_date->format('d M Y') }}</td>
										@if(!$selectedStudent)
											<td>{{ $collection->student->full_name ?? 'N/A' }}</td>
											<td>{{ $collection->student->schoolClass->name ?? '-' }}</td>
										@endif
										<td>{{ $collection->feeStructure->feeType->name ?? '-' }}</td>
										<td class="text-end fw-bold">₹{{ number_format($collection->paid_amount, 2) }}</td>
										@if($selectedStudent)
											<td class="text-center"><span class="badge badge-light-success px-3 py-1">Paid</span></td>
										@endif
									</tr>
								@empty
									<tr>
										<td colspan="{{ $selectedStudent ? 5 : 6 }}" class="text-center py-4 text-muted">
											@if($selectedStudent)
												No payments found for <strong>{{ $selectedStudent->full_name }}</strong> in the selected date range ({{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} — {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}).
											@else
												No collections found for the selected filters.
											@endif
										</td>
									</tr>
								@endforelse
							</tbody>
							@if($collections->count() > 0)
								<tfoot class="bg-light">
									<tr>
										<th colspan="{{ $selectedStudent ? 2 : 4 }}">Total ({{ $summary['total_transactions'] }} transactions)</th>
										<th></th>
										<th class="text-end">₹{{ number_format($summary['total_amount'], 2) }}</th>
										@if($selectedStudent)
											<th></th>
										@endif
									</tr>
								</tfoot>
							@endif
						</table>
					</div>
				</div>
				@if($collections->hasPages())
					<div class="card-footer">
						{{ $collections->appends(request()->query())->links() }}
					</div>
				@endif
			</div>
		</div>

		<!-- Daily Breakdown -->
		<div class="col-xl-4 mb-4">
			<div class="card">
				<div class="card-header">
					<h6 class="mb-0">Daily Breakdown</h6>
				</div>
				<div class="card-body p-0">
					<div class="table-responsive" style="max-height: 500px;">
						<table class="table table-hover mb-0">
							<thead class="bg-light sticky-top">
								<tr>
									<th>Date</th>
									<th class="text-center">Count</th>
									<th class="text-end">Amount</th>
								</tr>
							</thead>
							<tbody>
								@forelse($dailyData as $day)
									<tr>
										<td>{{ \Carbon\Carbon::parse($day->date)->format('d M Y') }}</td>
										<td class="text-center">{{ $day->count }}</td>
										<td class="text-end fw-bold">₹{{ number_format($day->total, 2) }}</td>
									</tr>
								@empty
									<tr>
										<td colspan="3" class="text-center py-4 text-muted">
											@if($selectedStudent)
												No payments by {{ $selectedStudent->full_name }} in this date range.
											@else
												No daily data available for the selected period.
											@endif
										</td>
									</tr>
								@endforelse
							</tbody>
							@if($dailyData->count() > 0)
								<tfoot class="bg-light">
									<tr>
										<th>Total</th>
										<th class="text-center">{{ $dailyData->sum('count') }}</th>
										<th class="text-end">₹{{ number_format($dailyData->sum('total'), 2) }}</th>
									</tr>
								</tfoot>
							@endif
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
