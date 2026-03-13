@extends('layouts.app')

@section('title', 'Custom Report Builder')

@section('page-title', 'Custom Report Builder')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.reports.students') }}">Reports</a></li>
	<li class="breadcrumb-item active">Report Builder</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<div class="d-flex justify-content-between align-items-center">
					<h5>Custom Report Builder</h5>
					<a href="{{ route('admin.reports.builder.create') }}" class="btn btn-primary">
						<i data-feather="plus" class="me-1"></i> Create New Report
					</a>
				</div>
			</div>
			<div class="card-body">
				<p class="text-muted mb-4">Build custom reports by selecting data sources, columns, and filters. Save templates for frequently used reports.</p>

				<!-- Quick Start Cards -->
				<div class="row mb-4">
					@foreach($dataSources as $key => $name)
						<div class="col-md-3 mb-3">
							<a href="{{ route('admin.reports.builder.create', ['data_source' => $key]) }}" class="text-decoration-none">
								<div class="card border hover-shadow h-100">
									<div class="card-body text-center py-4">
										<div class="mb-3">
											@switch($key)
												@case('students')
													<i data-feather="users" class="text-primary" style="width: 40px; height: 40px;"></i>
													@break
												@case('staff')
													<i data-feather="briefcase" class="text-success" style="width: 40px; height: 40px;"></i>
													@break
												@case('attendance')
													<i data-feather="check-circle" class="text-info" style="width: 40px; height: 40px;"></i>
													@break
												@case('staff_attendance')
													<i data-feather="clock" class="text-warning" style="width: 40px; height: 40px;"></i>
													@break
												@case('fees')
													<span class="text-danger" style="font-size: 40px; font-weight: bold;">₹</span>
													@break
												@case('library')
													<i data-feather="book" class="text-secondary" style="width: 40px; height: 40px;"></i>
													@break
												@case('transport')
													<i data-feather="truck" class="text-dark" style="width: 40px; height: 40px;"></i>
													@break
											@endswitch
										</div>
										<h6 class="text-dark mb-0">{{ $name }}</h6>
									</div>
								</div>
							</a>
						</div>
					@endforeach
				</div>

				<!-- Saved Templates -->
				@if($templates->isNotEmpty())
					<h6 class="mb-3">Saved Report Templates</h6>
					<div class="table-responsive">
						<table class="table table-striped table-hover">
							<thead>
								<tr>
									<th>#</th>
									<th>Template Name</th>
									<th>Data Source</th>
									<th>Columns</th>
									<th>Created By</th>
									<th>Visibility</th>
									<th>Created</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								@foreach($templates as $template)
									<tr>
										<td>{{ $loop->iteration }}</td>
										<td>{{ $template->name }}</td>
										<td>
											<span class="badge bg-light-primary">{{ $dataSources[$template->data_source] ?? $template->data_source }}</span>
										</td>
										<td>{{ count($template->columns) }} columns</td>
										<td>{{ $template->creator->name ?? 'Unknown' }}</td>
										<td>
											@if($template->is_public)
												<span class="badge bg-success">Public</span>
											@else
												<span class="badge bg-secondary">Private</span>
											@endif
										</td>
										<td>{{ $template->created_at->format('M d, Y') }}</td>
										<td>
											<div class="common-align gap-2">
												<a href="{{ route('admin.reports.builder.create', ['template' => $template->id]) }}" class="square-white" title="Use Template">
													<svg>
														<use href="{{ asset('assets/svg/icon-sprite.svg#eye') }}"></use>
													</svg>
												</a>
												@if($template->created_by === auth()->id())
													<button type="button" class="square-white border-0 bg-transparent p-0 delete-template" data-id="{{ $template->id }}" data-name="{{ $template->name }}" title="Delete">
														<svg>
															<use href="{{ asset('assets/svg/icon-sprite.svg#trash1') }}"></use>
														</svg>
													</button>
												@endif
											</div>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				@else
					<div class="text-center py-5">
						<i data-feather="file-text" style="width: 64px; height: 64px;" class="text-muted mb-3"></i>
						<h6 class="text-muted">No saved templates yet</h6>
						<p class="text-muted">Create your first report template to see it here.</p>
					</div>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection

@push('styles')
<style>
.hover-shadow:hover {
	box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
	transition: box-shadow 0.3s ease;
}
</style>
@endpush

@push('scripts')
<script>
jQuery(document).ready(function() {
	if (typeof feather !== 'undefined') {
		feather.replace();
	}

	// Delete template
	jQuery('.delete-template').on('click', function() {
		var templateId = jQuery(this).data('id');
		var templateName = jQuery(this).data('name');

		Swal.fire({
			title: 'Delete Template?',
			text: 'Are you sure you want to delete "' + templateName + '"?',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#d33',
			cancelButtonColor: '#3085d6',
			confirmButtonText: 'Yes, delete it!'
		}).then((result) => {
			if (result.isConfirmed) {
				jQuery.ajax({
					url: '{{ url("admin/reports/builder/templates") }}/' + templateId,
					method: 'DELETE',
					headers: {
						'X-CSRF-TOKEN': '{{ csrf_token() }}'
					},
					success: function(response) {
						if (response.success) {
							Swal.fire('Deleted!', response.message, 'success').then(() => {
								location.reload();
							});
						} else {
							Swal.fire('Error!', response.message, 'error');
						}
					},
					error: function() {
						Swal.fire('Error!', 'Failed to delete template.', 'error');
					}
				});
			}
		});
	});
});
</script>
@endpush
