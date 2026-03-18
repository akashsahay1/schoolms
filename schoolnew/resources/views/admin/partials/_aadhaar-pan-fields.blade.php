{{--
	Reusable Aadhaar & PAN Card partial for staff/teacher create/edit forms

	Variables:
	- $isVisible: closure to check field visibility
	- $isRequired: closure to check field required status
	- $model: the model instance (for edit forms) or null (for create forms)
	- $context: 'create' or 'edit'
--}}

@php
	$hasAadhaar = $isVisible('aadhaar_number') || $isVisible('aadhaar_front') || $isVisible('aadhaar_back');
	$hasPan = $isVisible('pan_number') || $isVisible('pan_front');
	$modelObj = $model ?? null;
	$oldDocType = old('document_type', '');
	if (!$oldDocType && $modelObj) {
		if (($modelObj->aadhaar_number || $modelObj->aadhaar_front || $modelObj->aadhaar_back) && ($modelObj->pan_number || $modelObj->pan_front)) {
			$oldDocType = 'both';
		} elseif ($modelObj->pan_number || $modelObj->pan_front) {
			$oldDocType = 'pan';
		} elseif ($modelObj->aadhaar_number || $modelObj->aadhaar_front || $modelObj->aadhaar_back) {
			$oldDocType = 'aadhaar';
		}
	}
@endphp

@if($hasAadhaar || $hasPan)
<div class="card">
	<div class="card-header">
		<h5>Aadhaar & PAN Card Details</h5>
	</div>
	<div class="card-body">
		<!-- Document Type Selector -->
		<div class="row g-3 mb-3">
			<div class="col-md-6">
				<label for="document_type" class="form-label">Document Type <span class="text-danger">*</span></label>
				<select class="form-select" id="document_type" name="document_type">
					<option value="">-- Select Document Type --</option>
					@if($hasAadhaar)
						<option value="aadhaar" {{ $oldDocType == 'aadhaar' ? 'selected' : '' }}>Aadhaar Card</option>
					@endif
					@if($hasPan)
						<option value="pan" {{ $oldDocType == 'pan' ? 'selected' : '' }}>PAN Card</option>
					@endif
					@if($hasAadhaar && $hasPan)
						<option value="both" {{ $oldDocType == 'both' ? 'selected' : '' }}>Both (Aadhaar & PAN)</option>
					@endif
				</select>
				<small class="text-muted">Select which document(s) to provide</small>
			</div>
		</div>

		<!-- Aadhaar Card Fields -->
		<div class="aadhaar-fields" style="display: none;">
			<h6 class="text-primary mb-3 mt-2"><i class="icon-id-badge me-1"></i> Aadhaar Card</h6>
			<div class="row g-3">
				@if($isVisible('aadhaar_number'))
				<div class="col-md-4">
					<label for="aadhaar_number" class="form-label">Aadhaar Number @if($isRequired('aadhaar_number'))<span class="text-danger">*</span>@endif</label>
					<input type="text" class="form-control doc-field @error('aadhaar_number') is-invalid @enderror" id="aadhaar_number" name="aadhaar_number" value="{{ old('aadhaar_number', $modelObj->aadhaar_number ?? '') }}" placeholder="Enter 12-digit number" maxlength="12">
					@error('aadhaar_number')
						<div class="invalid-feedback">{{ $message }}</div>
					@enderror
				</div>
				@endif
				@if($isVisible('aadhaar_front'))
				<div class="col-md-4">
					<label for="aadhaar_front" class="form-label">Aadhaar Front @if($isRequired('aadhaar_front'))<span class="text-danger">*</span>@endif</label>
					<input type="file" class="form-control doc-field @error('aadhaar_front') is-invalid @enderror" id="aadhaar_front" name="aadhaar_front" accept="image/*,.pdf">
					@error('aadhaar_front')
						<div class="invalid-feedback">{{ $message }}</div>
					@enderror
					@if($modelObj && $modelObj->aadhaar_front)
						<small class="text-success"><a href="{{ asset('storage/' . $modelObj->aadhaar_front) }}" target="_blank">View current file</a></small>
					@else
						<small class="text-muted">JPG, PNG or PDF (max 2MB)</small>
					@endif
				</div>
				@endif
				@if($isVisible('aadhaar_back'))
				<div class="col-md-4">
					<label for="aadhaar_back" class="form-label">Aadhaar Back @if($isRequired('aadhaar_back'))<span class="text-danger">*</span>@endif</label>
					<input type="file" class="form-control doc-field @error('aadhaar_back') is-invalid @enderror" id="aadhaar_back" name="aadhaar_back" accept="image/*,.pdf">
					@error('aadhaar_back')
						<div class="invalid-feedback">{{ $message }}</div>
					@enderror
					@if($modelObj && $modelObj->aadhaar_back)
						<small class="text-success"><a href="{{ asset('storage/' . $modelObj->aadhaar_back) }}" target="_blank">View current file</a></small>
					@else
						<small class="text-muted">JPG, PNG or PDF (max 2MB)</small>
					@endif
				</div>
				@endif
			</div>
		</div>

		<!-- PAN Card Fields -->
		<div class="pan-fields" style="display: none;">
			<h6 class="text-primary mb-3 mt-2"><i class="icon-credit-card me-1"></i> PAN Card</h6>
			<div class="row g-3">
				@if($isVisible('pan_number'))
				<div class="col-md-6">
					<label for="pan_number" class="form-label">PAN Number @if($isRequired('pan_number'))<span class="text-danger">*</span>@endif</label>
					<input type="text" class="form-control doc-field @error('pan_number') is-invalid @enderror" id="pan_number" name="pan_number" value="{{ old('pan_number', $modelObj->pan_number ?? '') }}" placeholder="Enter 10-character PAN" maxlength="10" style="text-transform: uppercase;">
					@error('pan_number')
						<div class="invalid-feedback">{{ $message }}</div>
					@enderror
				</div>
				@endif
				@if($isVisible('pan_front'))
				<div class="col-md-6">
					<label for="pan_front" class="form-label">PAN Card Upload @if($isRequired('pan_front'))<span class="text-danger">*</span>@endif</label>
					<input type="file" class="form-control doc-field @error('pan_front') is-invalid @enderror" id="pan_front" name="pan_front" accept="image/*,.pdf">
					@error('pan_front')
						<div class="invalid-feedback">{{ $message }}</div>
					@enderror
					@if($modelObj && $modelObj->pan_front)
						<small class="text-success"><a href="{{ asset('storage/' . $modelObj->pan_front) }}" target="_blank">View current file</a></small>
					@else
						<small class="text-muted">JPG, PNG or PDF (max 2MB)</small>
					@endif
				</div>
				@endif
			</div>
		</div>
	</div>
</div>

@push('scripts')
<script>
jQuery(document).ready(function() {
	var reqAadhaarNumber = {{ $isRequired('aadhaar_number') ? 'true' : 'false' }};
	var reqAadhaarFront = {{ $isRequired('aadhaar_front') ? 'true' : 'false' }};
	var reqAadhaarBack = {{ $isRequired('aadhaar_back') ? 'true' : 'false' }};
	var reqPanNumber = {{ $isRequired('pan_number') ? 'true' : 'false' }};
	var reqPanFront = {{ $isRequired('pan_front') ? 'true' : 'false' }};

	function toggleDocFields() {
		var docType = jQuery('#document_type').val();

		jQuery('.aadhaar-fields').hide().find('.doc-field').prop('required', false);
		jQuery('.pan-fields').hide().find('.doc-field').prop('required', false);

		if (docType === 'aadhaar' || docType === 'both') {
			jQuery('.aadhaar-fields').show();
			if (reqAadhaarNumber) jQuery('#aadhaar_number').prop('required', true);
			if (reqAadhaarFront) jQuery('#aadhaar_front').prop('required', true);
			if (reqAadhaarBack) jQuery('#aadhaar_back').prop('required', true);
		}

		if (docType === 'pan' || docType === 'both') {
			jQuery('.pan-fields').show();
			if (reqPanNumber) jQuery('#pan_number').prop('required', true);
			if (reqPanFront) jQuery('#pan_front').prop('required', true);
		}
	}

	jQuery('#document_type').on('change', toggleDocFields);
	toggleDocFields();
});
</script>
@endpush
@endif
