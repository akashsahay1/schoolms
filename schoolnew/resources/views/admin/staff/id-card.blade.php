@extends('layouts.app')

@section('title', 'Staff ID Card - ' . $staff->full_name)

@section('page-title', 'Staff ID Card')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.staff.index') }}">Staff</a></li>
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
                    <div class="staff-photo">
                        <img src="{{ $staff->photo_url }}" alt="{{ $staff->full_name }}">
                    </div>
                    <div class="staff-info">
                        <h3 class="staff-name">{{ $staff->full_name }}</h3>
                        <p class="staff-designation">{{ $staff->designation->name ?? 'Staff' }}</p>
                        <table class="info-table">
                            <tr>
                                <td>Staff ID</td>
                                <td><strong>{{ $staff->staff_id }}</strong></td>
                            </tr>
                            <tr>
                                <td>Department</td>
                                <td>{{ $staff->department->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Blood Group</td>
                                <td>{{ $staff->blood_group ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Phone</td>
                                <td>{{ $staff->phone }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="id-card-footer">
                    <div class="validity">
                        <span>Valid Till: {{ now()->addYear()->format('M Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Back of ID Card -->
            <div class="id-card id-card-back">
                <div class="id-card-header-back">
                    <h4>STAFF IDENTITY CARD</h4>
                </div>

                <div class="id-card-body-back">
                    <div class="address-section">
                        <h5>Address</h5>
                        <p>{{ $staff->current_address ?? 'N/A' }}</p>
                    </div>

                    <div class="emergency-section">
                        <h5>Emergency Contact</h5>
                        <p>{{ $staff->emergency_contact ?? 'N/A' }}</p>
                    </div>

                    <div class="terms-section">
                        <h6>Terms & Conditions</h6>
                        <ul>
                            <li>This card is the property of the school.</li>
                            <li>If found, please return to the school office.</li>
                            <li>Not transferable.</li>
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
    }

    .school-address {
        font-size: 10px;
        margin: 0;
        opacity: 0.9;
    }

    .id-card-body {
        padding: 20px;
        text-align: center;
    }

    .staff-photo {
        margin-bottom: 15px;
    }

    .staff-photo img {
        width: 100px;
        height: 120px;
        object-fit: cover;
        border: 3px solid #7366ff;
        border-radius: 10px;
    }

    .staff-name {
        font-size: 18px;
        font-weight: 700;
        color: #333;
        margin: 0 0 5px;
    }

    .staff-designation {
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
    }

    .id-card-body-back {
        padding: 20px;
    }

    .address-section, .emergency-section {
        margin-bottom: 15px;
    }

    .address-section h5, .emergency-section h5 {
        font-size: 12px;
        color: #7366ff;
        margin: 0 0 5px;
        font-weight: 600;
    }

    .address-section p, .emergency-section p {
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

        .sidebar-wrapper, .page-header, .footer {
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
    }
</style>
@endpush
