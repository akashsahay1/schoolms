@extends('layouts.app')

@section('title', 'Student ID Card - ' . $student->full_name)

@section('page-title', 'Student ID Card')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}">Students</a></li>
	<li class="breadcrumb-item active">ID Card</li>
@endsection

@section('content')
<div class="row justify-content-center">
	<div class="col-md-6">
		<div class="mb-3 text-end no-print">
			<button type="button" class="btn btn-success" onclick="window.print()">
				<i data-feather="printer" class="me-1"></i> Print ID Card
			</button>
		</div>

		<div class="id-card-container">
			<!-- Front of ID Card -->
			<div class="id-card id-card-front">
				<div class="id-card-header">
					<div class="school-logo">
						<img src="{{ asset('assets/images/logo/logo.png') }}" alt="School Logo">
					</div>
					<div class="school-name">
						<h2>{{ config('app.name', 'Shree Education Academy') }}</h2>
						<p class="school-address">{{ config('app.address', 'School Address') }}</p>
					</div>
				</div>

				<div class="id-card-body">
					<div class="student-photo">
						<img src="{{ $student->photo_url }}" alt="{{ $student->full_name }}">
					</div>
					<div class="student-info">
						<h3 class="student-name">{{ $student->full_name }}</h3>
						<p class="student-class">{{ $student->schoolClass->name ?? 'N/A' }} - {{ $student->section->name ?? 'N/A' }}</p>
						<table class="info-table">
							<tr>
								<td>Admission No</td>
								<td><strong>{{ $student->admission_no }}</strong></td>
							</tr>
							<tr>
								<td>Roll No</td>
								<td>{{ $student->roll_no ?? 'N/A' }}</td>
							</tr>
							<tr>
								<td>Blood Group</td>
								<td>{{ $student->blood_group ?? 'N/A' }}</td>
							</tr>
							<tr>
								<td>Date of Birth</td>
								<td>{{ $student->date_of_birth ? $student->date_of_birth->format('d M, Y') : 'N/A' }}</td>
							</tr>
						</table>
					</div>
				</div>

				<div class="id-card-footer">
					<div class="validity">
						<span>Session: {{ $student->academicYear->name ?? now()->year }}</span>
					</div>
				</div>
			</div>

			<!-- Back of ID Card -->
			<div class="id-card id-card-back">
				<div class="id-card-header-back">
					<h4>STUDENT IDENTITY CARD</h4>
				</div>

				<div class="id-card-body-back">
					<div class="parent-section">
						<h5>Parent/Guardian Details</h5>
						<table class="info-table-back">
							<tr>
								<td>Father's Name</td>
								<td>{{ $student->parent->father_name ?? 'N/A' }}</td>
							</tr>
							<tr>
								<td>Contact No</td>
								<td>{{ $student->parent->father_phone ?? $student->phone ?? 'N/A' }}</td>
							</tr>
						</table>
					</div>

					<div class="address-section">
						<h5>Address</h5>
						<p>{{ $student->current_address ?? $student->parent->current_address ?? 'N/A' }}</p>
					</div>

					<div class="terms-section">
						<h6>Terms & Conditions</h6>
						<ul>
							<li>This card is the property of the school.</li>
							<li>If found, please return to the school office.</li>
							<li>Student must carry this card at all times.</li>
						</ul>
					</div>
				</div>

				<div class="id-card-footer-back">
					<div class="signature-section">
						<div class="signature-line"></div>
						<p>Principal's Signature</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@push('styles')
<style>
	.id-card-container {
		display: flex;
		flex-wrap: wrap;
		justify-content: center;
		gap: 30px;
	}

	.id-card {
		width: 340px;
		height: 520px;
		border: 2px solid #333;
		border-radius: 15px;
		background: linear-gradient(135deg, #fff 0%, #f5f5f5 100%);
		box-shadow: 0 4px 15px rgba(0,0,0,0.15);
		overflow: hidden;
		position: relative;
	}

	.id-card-front .id-card-header {
		background: linear-gradient(135deg, #7366ff 0%, #5e54d9 100%);
		color: white;
		padding: 15px;
		text-align: center;
	}

	.school-logo img {
		width: 60px;
		height: 60px;
		object-fit: contain;
		border-radius: 50%;
		background: white;
		padding: 5px;
	}

	.school-name h2 {
		font-size: 16px;
		margin: 10px 0 5px;
		font-weight: 600;
		color: white;
	}

	.school-address {
		font-size: 10px;
		margin: 0;
		opacity: 0.9;
		color: white;
	}

	.id-card-body {
		padding: 20px;
		text-align: center;
	}

	.student-photo {
		margin-bottom: 15px;
	}

	.student-photo img {
		width: 100px;
		height: 120px;
		object-fit: cover;
		border: 3px solid #7366ff;
		border-radius: 10px;
	}

	.student-name {
		font-size: 18px;
		font-weight: 700;
		color: #333;
		margin: 0 0 5px;
	}

	.student-class {
		font-size: 14px;
		color: #7366ff;
		font-weight: 500;
		margin: 0 0 15px;
	}

	.info-table {
		width: 100%;
		font-size: 12px;
		text-align: left;
	}

	.info-table td {
		padding: 5px 8px;
		border-bottom: 1px solid #eee;
	}

	.info-table td:first-child {
		color: #666;
		width: 40%;
	}

	.id-card-footer {
		position: absolute;
		bottom: 0;
		left: 0;
		right: 0;
		background: #7366ff;
		color: white;
		padding: 10px;
		text-align: center;
		font-size: 12px;
	}

	/* Back Card Styles */
	.id-card-back {
		background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
	}

	.id-card-header-back {
		background: #7366ff;
		color: white;
		padding: 15px;
		text-align: center;
	}

	.id-card-header-back h4 {
		margin: 0;
		font-size: 18px;
		font-weight: 600;
		color: white;
	}

	.id-card-body-back {
		padding: 20px;
	}

	.parent-section, .address-section {
		margin-bottom: 15px;
	}

	.parent-section h5, .address-section h5 {
		font-size: 12px;
		color: #7366ff;
		margin: 0 0 8px;
		font-weight: 600;
	}

	.info-table-back {
		width: 100%;
		font-size: 11px;
	}

	.info-table-back td {
		padding: 4px 0;
	}

	.info-table-back td:first-child {
		color: #666;
		width: 40%;
	}

	.address-section p {
		font-size: 11px;
		color: #333;
		margin: 0;
		line-height: 1.4;
	}

	.terms-section {
		background: #fff;
		padding: 10px;
		border-radius: 8px;
		margin-top: 10px;
	}

	.terms-section h6 {
		font-size: 11px;
		color: #333;
		margin: 0 0 8px;
		font-weight: 600;
	}

	.terms-section ul {
		font-size: 9px;
		color: #666;
		margin: 0;
		padding-left: 15px;
	}

	.terms-section li {
		margin-bottom: 3px;
	}

	.id-card-footer-back {
		position: absolute;
		bottom: 0;
		left: 0;
		right: 0;
		padding: 15px 20px;
	}

	.signature-section {
		text-align: right;
	}

	.signature-line {
		width: 120px;
		border-bottom: 1px solid #333;
		margin-left: auto;
		margin-bottom: 5px;
	}

	.signature-section p {
		font-size: 10px;
		color: #666;
		margin: 0;
	}

	@media print {
		* {
			-webkit-print-color-adjust: exact !important;
			print-color-adjust: exact !important;
			color-adjust: exact !important;
		}

		.no-print {
			display: none !important;
		}

		body {
			background: white !important;
		}

		.page-wrapper, .page-body-wrapper, .page-body {
			margin: 0 !important;
			padding: 0 !important;
		}

		.sidebar-wrapper, .page-header, .footer, .breadcrumb-wrapper {
			display: none !important;
		}

		.id-card-container {
			display: flex;
			flex-direction: row;
			justify-content: center;
			gap: 20px;
		}

		.id-card {
			box-shadow: none !important;
			page-break-inside: avoid;
		}

		.id-card-front .id-card-header {
			background: linear-gradient(135deg, #7366ff 0%, #5e54d9 100%) !important;
		}

		.id-card-footer {
			background: #7366ff !important;
		}

		.id-card-header-back {
			background: #7366ff !important;
		}

		.school-name h2,
		.school-address,
		.id-card-footer,
		.id-card-header-back h4 {
			color: white !important;
		}

		.student-photo img {
			border-color: #7366ff !important;
		}
	}
</style>
@endpush
