@extends('layouts.app')

@section('title', 'Library Settings')

@section('page-title', 'Library Settings')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Settings</li>
    <li class="breadcrumb-item active">Library</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header pb-0">
                    <h5>Library Configuration</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.library.update') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Fine Per Day (Amount per overdue day) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="library_fine_per_day" class="form-control @error('library_fine_per_day') is-invalid @enderror" value="{{ old('library_fine_per_day', $settings['library_fine_per_day'] ?? 2) }}" step="0.01" min="0" required>
                                    </div>
                                    <small class="text-muted">Amount charged per day for overdue books</small>
                                    @error('library_fine_per_day')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Max Books Per Student <span class="text-danger">*</span></label>
                                    <input type="number" name="library_max_books_per_student" class="form-control @error('library_max_books_per_student') is-invalid @enderror" value="{{ old('library_max_books_per_student', $settings['library_max_books_per_student'] ?? 3) }}" min="1" max="20" required>
                                    <small class="text-muted">Maximum number of books a student can borrow at once</small>
                                    @error('library_max_books_per_student')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Default Issue Days <span class="text-danger">*</span></label>
                                    <input type="number" name="library_default_issue_days" class="form-control @error('library_default_issue_days') is-invalid @enderror" value="{{ old('library_default_issue_days', $settings['library_default_issue_days'] ?? 14) }}" min="1" max="90" required>
                                    <small class="text-muted">Default number of days a book can be borrowed</small>
                                    @error('library_default_issue_days')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Max Renewals Allowed</label>
                                    <input type="number" name="library_max_renewals" class="form-control @error('library_max_renewals') is-invalid @enderror" value="{{ old('library_max_renewals', $settings['library_max_renewals'] ?? 2) }}" min="0" max="10">
                                    <small class="text-muted">Maximum number of times a book can be renewed</small>
                                    @error('library_max_renewals')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" name="library_allow_renewal" class="form-check-input" id="allow_renewal" {{ ($settings['library_allow_renewal'] ?? '1') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="allow_renewal">Allow Book Renewal</label>
                                    </div>
                                    <small class="text-muted">Allow students to renew borrowed books</small>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">
                                <i data-feather="save" class="me-1"></i> Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Card -->
            <div class="card mt-4">
                <div class="card-header pb-0">
                    <h5>How Fine Calculation Works</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6 class="alert-heading">Automatic Fine Calculation</h6>
                        <p class="mb-2">When a book is returned after the due date, the system automatically calculates the fine:</p>
                        <code>Fine = (Number of Overdue Days) x (Fine Per Day)</code>
                        <hr>
                        <p class="mb-0">Example: If fine per day is 2 and book is returned 5 days late, fine will be 10</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
