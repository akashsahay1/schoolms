@extends('layouts.app')

@section('title', 'Build Custom Report')

@section('page-title', 'Build Custom Report')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.reports.builder.index') }}">Report Builder</a></li>
	<li class="breadcrumb-item active">Create Report</li>
@endsection

@push('styles')
<style>
	.wizard-steps {
		display: flex;
		justify-content: space-between;
		position: relative;
		margin-bottom: 2rem;
	}
	.wizard-steps::before {
		content: '';
		position: absolute;
		top: 20px;
		left: 40px;
		right: 40px;
		height: 2px;
		background: #e9ecef;
		z-index: 0;
	}
	.wizard-step {
		display: flex;
		flex-direction: column;
		align-items: center;
		position: relative;
		z-index: 1;
		cursor: pointer;
	}
	.wizard-step .step-circle {
		width: 40px;
		height: 40px;
		border-radius: 50%;
		background: #e9ecef;
		color: #7f8c8d;
		display: flex;
		align-items: center;
		justify-content: center;
		font-weight: 700;
		font-size: 14px;
		margin-bottom: 8px;
		transition: all 0.3s ease;
	}
	.wizard-step.active .step-circle {
		background: #7366ff;
		color: #fff;
		box-shadow: 0 3px 10px rgba(115, 102, 255, 0.3);
	}
	.wizard-step.completed .step-circle {
		background: #27ae60;
		color: #fff;
	}
	.wizard-step .step-label {
		font-size: 12px;
		color: #7f8c8d;
		font-weight: 500;
	}
	.wizard-step.active .step-label {
		color: #7366ff;
		font-weight: 600;
	}
	.wizard-step.completed .step-label {
		color: #27ae60;
	}
	.step-panel {
		display: none;
	}
	.step-panel.active {
		display: block;
	}
	.column-grid {
		display: grid;
		grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
		gap: 8px;
	}
	.column-grid .form-check {
		padding: 8px 12px 8px 36px;
		border: 1px solid #e9ecef;
		border-radius: 8px;
		transition: all 0.15s;
	}
	.column-grid .form-check:hover {
		border-color: #7366ff;
		background: rgba(115, 102, 255, 0.03);
	}
	.column-grid .form-check-input:checked + .form-check-label {
		font-weight: 500;
		color: #7366ff;
	}
	#reportTable th, #reportTable td {
		white-space: nowrap;
		font-size: 0.875rem;
	}
</style>
@endpush

@section('content')
<div class="container-fluid">
	<form id="reportForm">
		@csrf

		<!-- Wizard Steps Indicator -->
		<div class="wizard-steps" id="wizardSteps">
			<div class="wizard-step active" data-step="1">
				<div class="step-circle">1</div>
				<span class="step-label">Data Source</span>
			</div>
			<div class="wizard-step" data-step="2">
				<div class="step-circle">2</div>
				<span class="step-label">Columns</span>
			</div>
			<div class="wizard-step" data-step="3">
				<div class="step-circle">3</div>
				<span class="step-label">Filters</span>
			</div>
			<div class="wizard-step" data-step="4">
				<div class="step-circle">4</div>
				<span class="step-label">Preview & Export</span>
			</div>
		</div>

		<!-- Step 1: Data Source -->
		<div class="step-panel active" id="step1">
			<div class="card shadow-sm border-0">
				<div class="card-body py-4">
					<h6 class="fw-bold mb-1">Step 1: Choose Data Source</h6>
					<p class="text-muted mb-4" style="font-size: 13px;">Select what type of data you want to report on.</p>
					<div class="row justify-content-center">
						<div class="col-md-6">
							<select name="data_source" id="dataSource" class="form-select form-select-lg">
								@foreach($dataSources as $key => $name)
									<option value="{{ $key }}" {{ $selectedSource === $key ? 'selected' : '' }}>{{ $name }}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="text-end mt-4">
						<button type="button" class="btn btn-primary wizard-next" data-next="2">
							Next: Select Columns <i class="icon-angle-right ms-1"></i>
						</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Step 2: Columns -->
		<div class="step-panel" id="step2">
			<div class="card shadow-sm border-0">
				<div class="card-body py-4">
					<div class="d-flex justify-content-between align-items-center mb-1">
						<h6 class="fw-bold mb-0">Step 2: Select Columns</h6>
						<div>
							<button type="button" class="btn btn-sm btn-outline-primary" id="selectAllColumns">Select All</button>
							<button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllColumns">Deselect</button>
						</div>
					</div>
					<p class="text-muted mb-4" style="font-size: 13px;">Choose which fields to include in your report. Common columns are pre-selected.</p>
					<div class="column-grid" id="columnsContainer">
						@foreach($columns as $key => $label)
							<div class="form-check">
								<input class="form-check-input column-checkbox" type="checkbox" name="columns[]" value="{{ $key }}" id="col_{{ $key }}">
								<label class="form-check-label" for="col_{{ $key }}">{{ $label }}</label>
							</div>
						@endforeach
					</div>
					<div class="d-flex justify-content-between mt-4">
						<button type="button" class="btn btn-outline-secondary wizard-prev" data-prev="1">
							<i class="icon-angle-left me-1"></i> Back
						</button>
						<button type="button" class="btn btn-primary wizard-next" data-next="3">
							Next: Apply Filters <i class="icon-angle-right ms-1"></i>
						</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Step 3: Filters -->
		<div class="step-panel" id="step3">
			<div class="card shadow-sm border-0">
				<div class="card-body py-4">
					<h6 class="fw-bold mb-1">Step 3: Apply Filters</h6>
					<p class="text-muted mb-4" style="font-size: 13px;">Narrow down your data. Leave empty to include all records.</p>
					<div class="row g-3" id="filtersContainer">
						@foreach($filters as $key => $filter)
							<div class="col-md-4 filter-field" data-filter="{{ $key }}">
								<label class="form-label">{{ $filter['label'] }}</label>
								@if($filter['type'] === 'select')
									<select name="filters[{{ $key }}]" class="form-select">
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
												<option value="{{ $item->id }}">{{ $item->name ?? '' }}</option>
											@endforeach
										@endif
									</select>
								@elseif($filter['type'] === 'date')
									<input type="date" name="filters[{{ $key }}]" class="form-control">
								@endif
							</div>
						@endforeach
					</div>

					<!-- Sort -->
					<hr class="my-4">
					<h6 class="fw-bold mb-3">Sort Order <span class="text-muted fw-normal" style="font-size: 13px;">(optional)</span></h6>
					<div class="row g-3">
						<div class="col-md-4">
							<label class="form-label">Sort By</label>
							<select name="sort[field]" id="sortField" class="form-select">
								<option value="">Default</option>
								@foreach($columns as $key => $label)
									<option value="{{ $key }}">{{ $label }}</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-2">
							<label class="form-label">Direction</label>
							<select name="sort[direction]" class="form-select">
								<option value="asc">A → Z</option>
								<option value="desc">Z → A</option>
							</select>
						</div>
					</div>

					<div class="d-flex justify-content-between mt-4">
						<button type="button" class="btn btn-outline-secondary wizard-prev" data-prev="2">
							<i class="icon-angle-left me-1"></i> Back
						</button>
						<button type="button" class="btn btn-primary" id="previewBtn">
							<i class="icon-bar-chart me-1"></i> Generate Report
						</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Step 4: Preview & Export -->
		<div class="step-panel" id="step4">
			<div class="card shadow-sm border-0">
				<div class="card-header bg-white py-3">
					<div class="d-flex justify-content-between align-items-center">
						<h6 class="mb-0 fw-bold">Report Results <span id="recordCount" class="badge bg-primary ms-2">0 records</span></h6>
						<div class="d-flex gap-2">
							<button type="button" class="btn btn-outline-success btn-sm" id="exportCsvBtn" disabled>
								<i class="icon-download me-1"></i> Excel
							</button>
							<button type="button" class="btn btn-outline-danger btn-sm" id="exportPdfBtn" disabled>
								<i class="icon-file me-1"></i> PDF
							</button>
							<button type="button" class="btn btn-outline-primary btn-sm" id="saveTemplateBtn" disabled>
								<i class="icon-save me-1"></i> Save Template
							</button>
						</div>
					</div>
				</div>
				<div class="card-body p-0">
					<!-- Loading -->
					<div id="previewLoading" class="text-center py-5 d-none">
						<div class="spinner-border text-primary" role="status"></div>
						<p class="mt-3 text-muted">Generating report...</p>
					</div>

					<!-- Placeholder -->
					<div id="previewPlaceholder" class="text-center py-5">
						<div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 64px; height: 64px;">
							<i class="icon-bar-chart" style="font-size: 28px; color: #95a5a6;"></i>
						</div>
						<h6 class="mb-1">No report generated yet</h6>
						<p class="text-muted mb-3" style="font-size: 13px;">Go back to Step 3 and click "Generate Report".</p>
						<button type="button" class="btn btn-sm btn-outline-primary wizard-prev" data-prev="3">
							<i class="icon-angle-left me-1"></i> Go to Filters
						</button>
					</div>

					<!-- Results -->
					<div id="previewResults" class="d-none">
						<div class="table-responsive">
							<table class="table table-hover table-sm mb-0" id="reportTable">
								<thead class="bg-light" id="reportTableHead"></thead>
								<tbody id="reportTableBody"></tbody>
							</table>
						</div>
						<div id="paginationInfo" class="text-muted p-3" style="font-size: 13px;"></div>
					</div>

					<!-- No Data -->
					<div id="previewNoData" class="text-center py-5 d-none">
						<div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 56px; height: 56px;">
							<i class="icon-search" style="font-size: 24px; color: #95a5a6;"></i>
						</div>
						<h6 class="mb-1">No data found</h6>
						<p class="text-muted mb-3" style="font-size: 13px;">Try changing your filters or selecting a different data source.</p>
						<button type="button" class="btn btn-sm btn-outline-primary wizard-prev" data-prev="3">
							<i class="icon-angle-left me-1"></i> Adjust Filters
						</button>
					</div>
				</div>
			</div>

			<div class="text-center mt-3">
				<button type="button" class="btn btn-outline-secondary wizard-prev" data-prev="3">
					<i class="icon-angle-left me-1"></i> Back to Filters
				</button>
			</div>
		</div>
	</form>
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
					<label class="form-check-label" for="templatePublic">Make available to all users</label>
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

@push('scripts')
<script>
jQuery(document).ready(function() {
	var currentStep = 1;
	var currentData = null;
	var currentColumns = null;

	// Default columns to auto-select per data source
	var defaultColumns = {
		students: ['student_name', 'student_admission_no', 'class_name', 'section_name', 'status'],
		staff: ['name', 'employee_id', 'department', 'designation', 'phone'],
		attendance: ['student_name', 'class_name', 'date', 'status'],
		staff_attendance: ['staff_name', 'department', 'date', 'status'],
		fees: ['student_name', 'class_name', 'fee_type', 'paid_amount', 'payment_date'],
		library: ['book_title', 'student_name', 'issue_date', 'status'],
		transport: ['student_name', 'class_name', 'route_name', 'pickup_point']
	};

	function goToStep(step) {
		// Validate before moving forward
		if (step === 3 && jQuery('.column-checkbox:checked').length === 0) {
			Swal.fire('Select Columns', 'Please select at least one column before proceeding.', 'warning');
			return;
		}

		currentStep = step;
		jQuery('.step-panel').removeClass('active');
		jQuery('#step' + step).addClass('active');

		jQuery('.wizard-step').each(function() {
			var s = parseInt(jQuery(this).data('step'));
			jQuery(this).removeClass('active completed');
			if (s < step) jQuery(this).addClass('completed');
			if (s === step) jQuery(this).addClass('active');
		});

		window.scrollTo({ top: 0, behavior: 'smooth' });
	}

	// Wizard navigation
	jQuery(document).on('click', '.wizard-next', function() {
		goToStep(parseInt(jQuery(this).data('next')));
	});

	jQuery(document).on('click', '.wizard-prev', function() {
		goToStep(parseInt(jQuery(this).data('prev')));
	});

	jQuery('.wizard-step').on('click', function() {
		var step = parseInt(jQuery(this).data('step'));
		if (step <= currentStep || jQuery(this).hasClass('completed')) {
			goToStep(step);
		}
	});

	// Auto-select default columns when data source changes
	function autoSelectColumns() {
		var source = jQuery('#dataSource').val();
		var defaults = defaultColumns[source] || [];
		jQuery('.column-checkbox').each(function() {
			jQuery(this).prop('checked', defaults.indexOf(jQuery(this).val()) !== -1);
		});
	}

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
					autoSelectColumns();
				}
			}
		});
	});

	// Auto-select defaults on first load
	autoSelectColumns();

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
			html += '<div class="col-md-4 filter-field" data-filter="' + key + '">';
			html += '<label class="form-label">' + filter.label + '</label>';

			if (filter.type === 'select') {
				html += '<select name="filters[' + key + ']" class="form-select">';
				html += '<option value="">All</option>';
				if (filter.options) {
					jQuery.each(filter.options, function(optKey, optLabel) {
						html += '<option value="' + optKey + '">' + optLabel + '</option>';
					});
				} else if (filter.model) {
					var modelData = getFilterModelData(filter.model, filterData);
					jQuery.each(modelData, function(i, item) {
						html += '<option value="' + item.id + '">' + (item.name || '') + '</option>';
					});
				}
				html += '</select>';
			} else if (filter.type === 'date') {
				html += '<input type="date" name="filters[' + key + ']" class="form-control">';
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
		jQuery('#previewResults, #previewNoData').addClass('d-none');
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

	// Generate Report (Preview)
	jQuery('#previewBtn').on('click', function() {
		var selectedColumns = [];
		jQuery('.column-checkbox:checked').each(function() {
			selectedColumns.push(jQuery(this).val());
		});

		if (selectedColumns.length === 0) {
			Swal.fire('Error', 'Please select at least one column.', 'error');
			return;
		}

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
			var match = jQuery(this).attr('name').match(/filters\[(.+)\]/);
			if (match) {
				var value = jQuery(this).val();
				if (value) data.filters[match[1]] = value;
			}
		});

		// Go to step 4
		goToStep(4);

		jQuery('#previewPlaceholder, #previewResults, #previewNoData').addClass('d-none');
		jQuery('#previewLoading').removeClass('d-none');

		jQuery.ajax({
			url: '{{ route("admin.reports.builder.preview") }}',
			method: 'POST',
			data: data,
			headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
			success: function(response) {
				jQuery('#previewLoading').addClass('d-none');

				if (response.success && response.data.length > 0) {
					currentData = response.data;
					currentColumns = response.columns;
					renderPreview(response.data, response.columns);
					jQuery('#recordCount').text(response.total + ' records');
					jQuery('#previewResults').removeClass('d-none');
					jQuery('#exportCsvBtn, #exportPdfBtn, #saveTemplateBtn').prop('disabled', false);
				} else if (response.success && response.data.length === 0) {
					jQuery('#previewNoData').removeClass('d-none');
					jQuery('#recordCount').text('0 records');
				} else {
					jQuery('#previewPlaceholder').removeClass('d-none');
					Swal.fire('Error', 'Failed to generate report.', 'error');
				}
			},
			error: function() {
				jQuery('#previewLoading').addClass('d-none');
				jQuery('#previewPlaceholder').removeClass('d-none');
				Swal.fire('Error', 'Failed to generate report. Please try again.', 'error');
			}
		});
	});

	function renderPreview(data, columns) {
		var headerHtml = '<tr><th>#</th>';
		jQuery.each(columns, function(key, label) {
			headerHtml += '<th>' + label + '</th>';
		});
		headerHtml += '</tr>';
		jQuery('#reportTableHead').html(headerHtml);

		var bodyHtml = '';
		var maxRows = Math.min(data.length, 100);
		jQuery.each(data.slice(0, maxRows), function(index, row) {
			bodyHtml += '<tr><td class="text-muted">' + (index + 1) + '</td>';
			jQuery.each(columns, function(key) {
				bodyHtml += '<td>' + (row[key] || '-') + '</td>';
			});
			bodyHtml += '</tr>';
		});
		jQuery('#reportTableBody').html(bodyHtml);

		if (data.length > 100) {
			jQuery('#paginationInfo').html('Showing 1 to 100 of ' + data.length + ' records. Export to see all.');
		} else {
			jQuery('#paginationInfo').html('Showing all ' + data.length + ' records.');
		}
	}

	// Export
	jQuery('#exportCsvBtn').on('click', function() { exportReport('excel'); });
	jQuery('#exportPdfBtn').on('click', function() { exportReport('pdf'); });

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
			var match = jQuery(this).attr('name').match(/filters\[(.+)\]/);
			if (match) {
				var value = jQuery(this).val();
				if (value) data.filters[match[1]] = value;
			}
		});

		var url = format === 'excel'
			? '{{ route("admin.reports.builder.export-csv") }}'
			: '{{ route("admin.reports.builder.export-pdf") }}';

		var form = jQuery('<form>', { method: 'POST', action: url });
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
			var match = jQuery(this).attr('name').match(/filters\[(.+)\]/);
			if (match) {
				var value = jQuery(this).val();
				if (value) data.filters[match[1]] = value;
			}
		});

		jQuery.ajax({
			url: '{{ route("admin.reports.builder.save-template") }}',
			method: 'POST',
			data: data,
			headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
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
