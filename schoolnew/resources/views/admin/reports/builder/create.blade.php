@extends('layouts.app')

@section('title', 'Build Custom Report')

@section('page-title', 'Build Custom Report')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.reports.builder.index') }}">Report Builder</a></li>
	<li class="breadcrumb-item active">Create Report</li>
@endsection

@section('content')
<div class="row">
	<!-- Report Configuration -->
	<div class="col-lg-4">
		<div class="card sticky-top" style="top: 80px;">
			<div class="card-header">
				<h5>Report Configuration</h5>
			</div>
			<div class="card-body">
				<form id="reportForm">
					@csrf
					<!-- Data Source -->
					<div class="mb-3">
						<label class="form-label">Data Source <span class="text-danger">*</span></label>
						<select name="data_source" id="dataSource" class="form-select" required>
							@foreach($dataSources as $key => $name)
								<option value="{{ $key }}" {{ $selectedSource === $key ? 'selected' : '' }}>{{ $name }}</option>
							@endforeach
						</select>
					</div>

					<!-- Column Selection -->
					<div class="mb-3">
						<label class="form-label">Select Columns <span class="text-danger">*</span></label>
						<div class="border rounded p-3" style="max-height: 250px; overflow-y: auto;" id="columnsContainer">
							@foreach($columns as $key => $label)
								<div class="form-check">
									<input class="form-check-input column-checkbox" type="checkbox" name="columns[]" value="{{ $key }}" id="col_{{ $key }}">
									<label class="form-check-label" for="col_{{ $key }}">{{ $label }}</label>
								</div>
							@endforeach
						</div>
						<div class="mt-2">
							<button type="button" class="btn btn-sm btn-outline-primary" id="selectAllColumns">Select All</button>
							<button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllColumns">Deselect All</button>
						</div>
					</div>

					<!-- Filters -->
					<div class="mb-3">
						<label class="form-label">Filters</label>
						<div id="filtersContainer">
							@foreach($filters as $key => $filter)
								<div class="mb-2 filter-field" data-filter="{{ $key }}">
									<label class="form-label small">{{ $filter['label'] }}</label>
									@if($filter['type'] === 'select')
										<select name="filters[{{ $key }}]" class="form-select form-select-sm">
											<option value="">All</option>
											@if(isset($filter['options']))
												@foreach($filter['options'] as $optKey => $optLabel)
													<option value="{{ $optKey }}">{{ $optLabel }}</option>
												@endforeach
											@elseif(isset($filter['model']))
												@php
													$modelData = [];
													if ($filter['model'] === 'SchoolClass') $modelData = $filterData['classes'] ?? [];
													elseif ($filter['model'] === 'Section') $modelData = $filterData['sections'] ?? [];
													elseif ($filter['model'] === 'AcademicYear') $modelData = $filterData['academic_years'] ?? [];
													elseif ($filter['model'] === 'Department') $modelData = $filterData['departments'] ?? [];
													elseif ($filter['model'] === 'Designation') $modelData = $filterData['designations'] ?? [];
													elseif ($filter['model'] === 'FeeType') $modelData = $filterData['fee_types'] ?? [];
													elseif ($filter['model'] === 'BookCategory') $modelData = $filterData['categories'] ?? [];
													elseif ($filter['model'] === 'TransportRoute') $modelData = $filterData['routes'] ?? [];
													elseif ($filter['model'] === 'Vehicle') $modelData = $filterData['vehicles'] ?? [];
												@endphp
												@foreach($modelData as $item)
													<option value="{{ $item->id }}">{{ $item->name ?? $item->vehicle_number ?? '' }}</option>
												@endforeach
											@endif
										</select>
									@elseif($filter['type'] === 'date')
										<input type="date" name="filters[{{ $key }}]" class="form-control form-control-sm">
									@endif
								</div>
							@endforeach
						</div>
					</div>

					<!-- Sort Options -->
					<div class="mb-3">
						<label class="form-label">Sort By</label>
						<div class="row g-2">
							<div class="col-8">
								<select name="sort[field]" id="sortField" class="form-select form-select-sm">
									<option value="">Default</option>
									@foreach($columns as $key => $label)
										<option value="{{ $key }}">{{ $label }}</option>
									@endforeach
								</select>
							</div>
							<div class="col-4">
								<select name="sort[direction]" class="form-select form-select-sm">
									<option value="asc">ASC</option>
									<option value="desc">DESC</option>
								</select>
							</div>
						</div>
					</div>

					<!-- Action Buttons -->
					<div class="d-grid gap-2">
						<button type="button" class="btn btn-primary" id="previewBtn">
							<i data-feather="eye" class="me-1"></i> Preview Report
						</button>
						<div class="btn-group">
							<button type="button" class="btn btn-success" id="exportCsvBtn" disabled>
								<i data-feather="download" class="me-1"></i> Export CSV
							</button>
							<button type="button" class="btn btn-danger" id="exportPdfBtn" disabled>
								<i data-feather="file-text" class="me-1"></i> Export PDF
							</button>
						</div>
						<button type="button" class="btn btn-outline-primary" id="saveTemplateBtn" disabled>
							<i data-feather="save" class="me-1"></i> Save as Template
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Report Preview -->
	<div class="col-lg-8">
		<div class="card">
			<div class="card-header">
				<div class="d-flex justify-content-between align-items-center">
					<h5>Report Preview</h5>
					<span id="recordCount" class="badge bg-primary">0 records</span>
				</div>
			</div>
			<div class="card-body">
				<div id="previewPlaceholder" class="text-center py-5">
					<i data-feather="file-text" style="width: 64px; height: 64px;" class="text-muted mb-3"></i>
					<h6 class="text-muted">Configure and preview your report</h6>
					<p class="text-muted">Select columns and filters, then click "Preview Report" to see results.</p>
				</div>

				<div id="previewLoading" class="text-center py-5 d-none">
					<div class="spinner-border text-primary" role="status">
						<span class="visually-hidden">Loading...</span>
					</div>
					<p class="mt-3 text-muted">Generating report preview...</p>
				</div>

				<div id="previewResults" class="d-none">
					<div class="table-responsive">
						<table class="table table-striped table-hover table-sm" id="reportTable">
							<thead id="reportTableHead"></thead>
							<tbody id="reportTableBody"></tbody>
						</table>
					</div>
					<div id="paginationInfo" class="text-muted mt-3"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Save Template Modal -->
<div class="modal fade" id="saveTemplateModal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Save Report Template</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<div class="mb-3">
					<label class="form-label">Template Name <span class="text-danger">*</span></label>
					<input type="text" class="form-control" id="templateName" placeholder="e.g., Monthly Attendance Report">
				</div>
				<div class="form-check">
					<input class="form-check-input" type="checkbox" id="templatePublic">
					<label class="form-check-label" for="templatePublic">
						Make this template available to all users
					</label>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-primary" id="confirmSaveTemplate">Save Template</button>
			</div>
		</div>
	</div>
</div>
@endsection

@push('styles')
<style>
.column-checkbox {
	cursor: pointer;
}
.form-check-label {
	cursor: pointer;
}
#reportTable th, #reportTable td {
	white-space: nowrap;
	font-size: 0.875rem;
}
</style>
@endpush

@push('scripts')
<script>
jQuery(document).ready(function() {
	if (typeof feather !== 'undefined') {
		feather.replace();
	}

	var currentData = null;
	var currentColumns = null;

	// Data source change
	jQuery('#dataSource').on('change', function() {
		var source = jQuery(this).val();
		jQuery.ajax({
			url: '{{ route("admin.reports.builder.source-config") }}',
			method: 'GET',
			data: { data_source: source },
			success: function(response) {
				if (response.success) {
					updateColumnsUI(response.columns);
					updateFiltersUI(response.filters, response.filterData);
					updateSortUI(response.columns);
					resetPreview();
				}
			}
		});
	});

	function updateColumnsUI(columns) {
		var html = '';
		jQuery.each(columns, function(key, label) {
			html += '<div class="form-check">';
			html += '<input class="form-check-input column-checkbox" type="checkbox" name="columns[]" value="' + key + '" id="col_' + key + '">';
			html += '<label class="form-check-label" for="col_' + key + '">' + label + '</label>';
			html += '</div>';
		});
		jQuery('#columnsContainer').html(html);
	}

	function updateFiltersUI(filters, filterData) {
		var html = '';
		jQuery.each(filters, function(key, filter) {
			html += '<div class="mb-2 filter-field" data-filter="' + key + '">';
			html += '<label class="form-label small">' + filter.label + '</label>';

			if (filter.type === 'select') {
				html += '<select name="filters[' + key + ']" class="form-select form-select-sm">';
				html += '<option value="">All</option>';

				if (filter.options) {
					jQuery.each(filter.options, function(optKey, optLabel) {
						html += '<option value="' + optKey + '">' + optLabel + '</option>';
					});
				} else if (filter.model) {
					var modelData = getFilterModelData(filter.model, filterData);
					jQuery.each(modelData, function(i, item) {
						var displayValue = item.name || item.vehicle_number || '';
						html += '<option value="' + item.id + '">' + displayValue + '</option>';
					});
				}
				html += '</select>';
			} else if (filter.type === 'date') {
				html += '<input type="date" name="filters[' + key + ']" class="form-control form-control-sm">';
			}

			html += '</div>';
		});
		jQuery('#filtersContainer').html(html);
	}

	function getFilterModelData(model, filterData) {
		switch(model) {
			case 'SchoolClass': return filterData.classes || [];
			case 'Section': return filterData.sections || [];
			case 'AcademicYear': return filterData.academic_years || [];
			case 'Department': return filterData.departments || [];
			case 'Designation': return filterData.designations || [];
			case 'FeeType': return filterData.fee_types || [];
			case 'BookCategory': return filterData.categories || [];
			case 'TransportRoute': return filterData.routes || [];
			case 'Vehicle': return filterData.vehicles || [];
			default: return [];
		}
	}

	function updateSortUI(columns) {
		var html = '<option value="">Default</option>';
		jQuery.each(columns, function(key, label) {
			html += '<option value="' + key + '">' + label + '</option>';
		});
		jQuery('#sortField').html(html);
	}

	function resetPreview() {
		currentData = null;
		currentColumns = null;
		jQuery('#previewPlaceholder').removeClass('d-none');
		jQuery('#previewResults').addClass('d-none');
		jQuery('#recordCount').text('0 records');
		jQuery('#exportCsvBtn, #exportPdfBtn, #saveTemplateBtn').prop('disabled', true);
	}

	// Select/Deselect all columns
	jQuery('#selectAllColumns').on('click', function() {
		jQuery('.column-checkbox').prop('checked', true);
	});

	jQuery('#deselectAllColumns').on('click', function() {
		jQuery('.column-checkbox').prop('checked', false);
	});

	// Preview report
	jQuery('#previewBtn').on('click', function() {
		var selectedColumns = [];
		jQuery('.column-checkbox:checked').each(function() {
			selectedColumns.push(jQuery(this).val());
		});

		if (selectedColumns.length === 0) {
			Swal.fire('Error', 'Please select at least one column.', 'error');
			return;
		}

		var formData = jQuery('#reportForm').serializeArray();
		var data = {
			data_source: jQuery('#dataSource').val(),
			columns: selectedColumns,
			filters: {},
			sort: {
				field: jQuery('[name="sort[field]"]').val(),
				direction: jQuery('[name="sort[direction]"]').val()
			}
		};

		jQuery('[name^="filters["]').each(function() {
			var name = jQuery(this).attr('name').match(/filters\[(.+)\]/)[1];
			var value = jQuery(this).val();
			if (value) {
				data.filters[name] = value;
			}
		});

		jQuery('#previewPlaceholder').addClass('d-none');
		jQuery('#previewResults').addClass('d-none');
		jQuery('#previewLoading').removeClass('d-none');

		jQuery.ajax({
			url: '{{ route("admin.reports.builder.preview") }}',
			method: 'POST',
			data: data,
			headers: {
				'X-CSRF-TOKEN': '{{ csrf_token() }}'
			},
			success: function(response) {
				jQuery('#previewLoading').addClass('d-none');

				if (response.success) {
					currentData = response.data;
					currentColumns = response.columns;
					renderPreview(response.data, response.columns);
					jQuery('#recordCount').text(response.total + ' records');
					jQuery('#previewResults').removeClass('d-none');
					jQuery('#exportCsvBtn, #exportPdfBtn, #saveTemplateBtn').prop('disabled', false);
				} else {
					jQuery('#previewPlaceholder').removeClass('d-none');
					Swal.fire('Error', 'Failed to generate report.', 'error');
				}
			},
			error: function(xhr) {
				jQuery('#previewLoading').addClass('d-none');
				jQuery('#previewPlaceholder').removeClass('d-none');
				Swal.fire('Error', 'Failed to generate report. Please try again.', 'error');
			}
		});
	});

	function renderPreview(data, columns) {
		// Header
		var headerHtml = '<tr>';
		headerHtml += '<th>#</th>';
		jQuery.each(columns, function(key, label) {
			headerHtml += '<th>' + label + '</th>';
		});
		headerHtml += '</tr>';
		jQuery('#reportTableHead').html(headerHtml);

		// Body
		var bodyHtml = '';
		if (data.length === 0) {
			bodyHtml = '<tr><td colspan="' + (Object.keys(columns).length + 1) + '" class="text-center py-4">No data found.</td></tr>';
		} else {
			var maxRows = Math.min(data.length, 100);
			jQuery.each(data.slice(0, maxRows), function(index, row) {
				bodyHtml += '<tr>';
				bodyHtml += '<td>' + (index + 1) + '</td>';
				jQuery.each(columns, function(key, label) {
					bodyHtml += '<td>' + (row[key] || '-') + '</td>';
				});
				bodyHtml += '</tr>';
			});
		}
		jQuery('#reportTableBody').html(bodyHtml);

		// Pagination info
		if (data.length > 100) {
			jQuery('#paginationInfo').html('Showing 1 to 100 of ' + data.length + ' records. Export to see all records.');
		} else {
			jQuery('#paginationInfo').html('Showing all ' + data.length + ' records.');
		}
	}

	// Export CSV
	jQuery('#exportCsvBtn').on('click', function() {
		exportReport('csv');
	});

	// Export PDF
	jQuery('#exportPdfBtn').on('click', function() {
		exportReport('pdf');
	});

	function exportReport(format) {
		var selectedColumns = [];
		jQuery('.column-checkbox:checked').each(function() {
			selectedColumns.push(jQuery(this).val());
		});

		var data = {
			data_source: jQuery('#dataSource').val(),
			columns: selectedColumns,
			filters: {},
			sort: {
				field: jQuery('[name="sort[field]"]').val(),
				direction: jQuery('[name="sort[direction]"]').val()
			}
		};

		jQuery('[name^="filters["]').each(function() {
			var name = jQuery(this).attr('name').match(/filters\[(.+)\]/)[1];
			var value = jQuery(this).val();
			if (value) {
				data.filters[name] = value;
			}
		});

		var url = format === 'csv'
			? '{{ route("admin.reports.builder.export-csv") }}'
			: '{{ route("admin.reports.builder.export-pdf") }}';

		// Create form and submit
		var form = jQuery('<form>', {
			'method': 'POST',
			'action': url
		});

		form.append(jQuery('<input>', { type: 'hidden', name: '_token', value: '{{ csrf_token() }}' }));
		form.append(jQuery('<input>', { type: 'hidden', name: 'data_source', value: data.data_source }));

		jQuery.each(data.columns, function(i, col) {
			form.append(jQuery('<input>', { type: 'hidden', name: 'columns[]', value: col }));
		});

		jQuery.each(data.filters, function(key, value) {
			form.append(jQuery('<input>', { type: 'hidden', name: 'filters[' + key + ']', value: value }));
		});

		form.append(jQuery('<input>', { type: 'hidden', name: 'sort[field]', value: data.sort.field }));
		form.append(jQuery('<input>', { type: 'hidden', name: 'sort[direction]', value: data.sort.direction }));

		form.appendTo('body').submit().remove();
	}

	// Save template
	jQuery('#saveTemplateBtn').on('click', function() {
		jQuery('#saveTemplateModal').modal('show');
	});

	jQuery('#confirmSaveTemplate').on('click', function() {
		var templateName = jQuery('#templateName').val().trim();
		if (!templateName) {
			Swal.fire('Error', 'Please enter a template name.', 'error');
			return;
		}

		var selectedColumns = [];
		jQuery('.column-checkbox:checked').each(function() {
			selectedColumns.push(jQuery(this).val());
		});

		var data = {
			name: templateName,
			data_source: jQuery('#dataSource').val(),
			columns: selectedColumns,
			filters: {},
			sort: {
				field: jQuery('[name="sort[field]"]').val(),
				direction: jQuery('[name="sort[direction]"]').val()
			},
			is_public: jQuery('#templatePublic').is(':checked')
		};

		jQuery('[name^="filters["]').each(function() {
			var name = jQuery(this).attr('name').match(/filters\[(.+)\]/)[1];
			var value = jQuery(this).val();
			if (value) {
				data.filters[name] = value;
			}
		});

		jQuery.ajax({
			url: '{{ route("admin.reports.builder.save-template") }}',
			method: 'POST',
			data: data,
			headers: {
				'X-CSRF-TOKEN': '{{ csrf_token() }}'
			},
			success: function(response) {
				jQuery('#saveTemplateModal').modal('hide');
				if (response.success) {
					Swal.fire('Success', response.message, 'success');
					jQuery('#templateName').val('');
					jQuery('#templatePublic').prop('checked', false);
				} else {
					Swal.fire('Error', response.message, 'error');
				}
			},
			error: function() {
				Swal.fire('Error', 'Failed to save template.', 'error');
			}
		});
	});
});
</script>
@endpush
