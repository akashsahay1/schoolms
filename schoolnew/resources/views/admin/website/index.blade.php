@extends('layouts.app')

@section('title', 'Website Management')

@section('page-title', 'Website Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Website Management</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>Website Overview</h5>
                    <a href="{{ route('website.home') }}" target="_blank" class="btn btn-outline-primary btn-sm">
                        <i data-feather="external-link" class="me-1"></i> View Website
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <!-- Sliders -->
                    <div class="col-xl-4 col-md-6">
                        <div class="card border shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-muted mb-2">Homepage Sliders</h6>
                                        <h3 class="mb-0">{{ $stats['sliders'] }}</h3>
                                    </div>
                                    <div class="bg-light-primary p-3 rounded">
                                        <i data-feather="image" class="text-primary"></i>
                                    </div>
                                </div>
                                <a href="{{ route('admin.website.sliders') }}" class="btn btn-light btn-sm mt-3">
                                    Manage Sliders <i data-feather="arrow-right" style="width: 14px;"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Facilities -->
                    <div class="col-xl-4 col-md-6">
                        <div class="card border shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-muted mb-2">Facilities</h6>
                                        <h3 class="mb-0">{{ $stats['facilities'] }}</h3>
                                    </div>
                                    <div class="bg-light-success p-3 rounded">
                                        <i data-feather="star" class="text-success"></i>
                                    </div>
                                </div>
                                <a href="{{ route('admin.website.facilities') }}" class="btn btn-light btn-sm mt-3">
                                    Manage Facilities <i data-feather="arrow-right" style="width: 14px;"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonials -->
                    <div class="col-xl-4 col-md-6">
                        <div class="card border shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-muted mb-2">Testimonials</h6>
                                        <h3 class="mb-0">{{ $stats['testimonials'] }}</h3>
                                    </div>
                                    <div class="bg-light-warning p-3 rounded">
                                        <i data-feather="message-circle" class="text-warning"></i>
                                    </div>
                                </div>
                                <a href="{{ route('admin.website.testimonials') }}" class="btn btn-light btn-sm mt-3">
                                    Manage Testimonials <i data-feather="arrow-right" style="width: 14px;"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Gallery -->
                    <div class="col-xl-4 col-md-6">
                        <div class="card border shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-muted mb-2">Gallery Images</h6>
                                        <h3 class="mb-0">{{ $stats['gallery'] }}</h3>
                                    </div>
                                    <div class="bg-light-info p-3 rounded">
                                        <i data-feather="grid" class="text-info"></i>
                                    </div>
                                </div>
                                <a href="{{ route('admin.website.gallery') }}" class="btn btn-light btn-sm mt-3">
                                    Manage Gallery <i data-feather="arrow-right" style="width: 14px;"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Pages -->
                    <div class="col-xl-4 col-md-6">
                        <div class="card border shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-muted mb-2">Website Pages</h6>
                                        <h3 class="mb-0">{{ $stats['pages'] }}</h3>
                                    </div>
                                    <div class="bg-light-secondary p-3 rounded">
                                        <i data-feather="file-text" class="text-secondary"></i>
                                    </div>
                                </div>
                                <a href="{{ route('admin.website.pages') }}" class="btn btn-light btn-sm mt-3">
                                    Manage Pages <i data-feather="arrow-right" style="width: 14px;"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Messages -->
                    <div class="col-xl-4 col-md-6">
                        <div class="card border shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-muted mb-2">New Messages</h6>
                                        <h3 class="mb-0">{{ $stats['contacts'] }}</h3>
                                    </div>
                                    <div class="bg-light-danger p-3 rounded">
                                        <i data-feather="mail" class="text-danger"></i>
                                    </div>
                                </div>
                                <a href="{{ route('admin.website.contacts') }}" class="btn btn-light btn-sm mt-3">
                                    View Messages <i data-feather="arrow-right" style="width: 14px;"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
    if (typeof feather !== 'undefined') feather.replace();
});
</script>
@endpush
