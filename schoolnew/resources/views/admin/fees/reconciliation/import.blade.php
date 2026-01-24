@extends('layouts.app')

@section('title', 'Import Bank Statement')
@section('page-title', 'Import Bank Statement')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.fees.reconciliation.index') }}">Reconciliation</a></li>
    <li class="breadcrumb-item active">Import</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">Import Bank Statement (CSV)</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.fees.reconciliation.process-import') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="alert alert-info">
                            <h6 class="alert-heading"><i data-feather="info" class="me-2"></i>CSV Format Requirements</h6>
                            <p class="mb-2">Your CSV file should have columns in this order:</p>
                            <ol class="mb-0">
                                <li><strong>Date</strong> - Transaction date</li>
                                <li><strong>Reference</strong> - Transaction reference/UTR number</li>
                                <li><strong>Description</strong> - Transaction description</li>
                                <li><strong>Credit</strong> - Credit amount (incoming)</li>
                                <li><strong>Debit</strong> - Debit amount (outgoing) - optional</li>
                                <li><strong>Balance</strong> - Running balance - optional</li>
                            </ol>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Bank Name <span class="text-danger">*</span></label>
                                <select name="bank_name" class="form-select @error('bank_name') is-invalid @enderror" required>
                                    <option value="">Select Bank</option>
                                    <option value="State Bank of India" {{ old('bank_name') == 'State Bank of India' ? 'selected' : '' }}>State Bank of India</option>
                                    <option value="HDFC Bank" {{ old('bank_name') == 'HDFC Bank' ? 'selected' : '' }}>HDFC Bank</option>
                                    <option value="ICICI Bank" {{ old('bank_name') == 'ICICI Bank' ? 'selected' : '' }}>ICICI Bank</option>
                                    <option value="Axis Bank" {{ old('bank_name') == 'Axis Bank' ? 'selected' : '' }}>Axis Bank</option>
                                    <option value="Punjab National Bank" {{ old('bank_name') == 'Punjab National Bank' ? 'selected' : '' }}>Punjab National Bank</option>
                                    <option value="Bank of Baroda" {{ old('bank_name') == 'Bank of Baroda' ? 'selected' : '' }}>Bank of Baroda</option>
                                    <option value="Kotak Mahindra Bank" {{ old('bank_name') == 'Kotak Mahindra Bank' ? 'selected' : '' }}>Kotak Mahindra Bank</option>
                                    <option value="Yes Bank" {{ old('bank_name') == 'Yes Bank' ? 'selected' : '' }}>Yes Bank</option>
                                    <option value="Other" {{ old('bank_name') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('bank_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Account Number</label>
                                <input type="text" name="account_number" class="form-control @error('account_number') is-invalid @enderror" value="{{ old('account_number') }}" placeholder="Last 4 digits recommended">
                                @error('account_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Date Format <span class="text-danger">*</span></label>
                                <select name="date_format" class="form-select @error('date_format') is-invalid @enderror" required>
                                    <option value="d/m/Y" {{ old('date_format') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY (31/12/2026)</option>
                                    <option value="d-m-Y" {{ old('date_format') == 'd-m-Y' ? 'selected' : '' }}>DD-MM-YYYY (31-12-2026)</option>
                                    <option value="Y-m-d" {{ old('date_format') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD (2026-12-31)</option>
                                    <option value="m/d/Y" {{ old('date_format') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY (12/31/2026)</option>
                                    <option value="d M Y" {{ old('date_format') == 'd M Y' ? 'selected' : '' }}>DD Mon YYYY (31 Dec 2026)</option>
                                </select>
                                @error('date_format')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">CSV File <span class="text-danger">*</span></label>
                                <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" accept=".csv,.txt" required>
                                @error('file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Max size: 5MB. Accepted formats: CSV, TXT</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.fees.reconciliation.index') }}" class="btn btn-light">
                                <i data-feather="arrow-left" class="me-1"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i data-feather="upload" class="me-1"></i> Import Statement
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sample CSV -->
            <div class="card shadow-sm mt-4">
                <div class="card-header">
                    <h6 class="mb-0">Sample CSV Format</h6>
                </div>
                <div class="card-body">
                    <pre class="bg-light p-3 rounded mb-0" style="font-size: 12px;">Date,Reference,Description,Credit,Debit,Balance
15/01/2026,UTR123456789,NEFT-FEE PAYMENT-JOHN DOE,5000.00,0.00,125000.00
15/01/2026,UTR987654321,IMPS-RCP2026000001,2500.00,0.00,127500.00
16/01/2026,CHQ456789,CHEQUE DEPOSIT,10000.00,0.00,137500.00</pre>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
