@extends('layouts.app')

@section('title', 'View Notice')
@section('page-title', 'Notice Details')

@section('breadcrumb')
    <li class="breadcrumb-item">Communication</li>
    <li class="breadcrumb-item"><a href="{{ route('admin.notices.index') }}">Notices</a></li>
    <li class="breadcrumb-item active">View</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>Notice Details</h5>
                        <div>
                            <a href="{{ route('admin.notices.edit', $notice) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <a href="{{ route('admin.notices.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span class="badge {{ $notice->getTypeBadgeClass() }}">{{ $notice->getTypeLabel() }}</span>
                        @if(!$notice->is_published)
                            <span class="badge badge-light-secondary">Draft</span>
                        @elseif($notice->isExpired())
                            <span class="badge badge-light-danger">Expired</span>
                        @else
                            <span class="badge badge-light-success">Published</span>
                        @endif
                    </div>

                    <h3 class="mb-3">{{ $notice->title }}</h3>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <small class="text-muted">Published:</small>
                            <p class="mb-2">{{ $notice->publish_date->format('F d, Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Expiry:</small>
                            <p class="mb-2">{{ $notice->expiry_date ? $notice->expiry_date->format('F d, Y') : 'No Expiry' }}</p>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h6>Content</h6>
                        <div class="bg-light text-dark p-3 rounded">
                            {!! nl2br(e($notice->content)) !!}
                        </div>
                    </div>

                    @if($notice->attachment)
                        <div class="mb-4">
                            <h6>Attachment</h6>
                            <a href="{{ asset('storage/' . $notice->attachment) }}" class="btn btn-outline-primary" target="_blank">
                                <i class="fa fa-download me-1"></i> Download
                            </a>
                        </div>
                    @endif

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">Target Audience:</small>
                            <p class="mb-2">
                                @foreach($notice->target_audience ?? [] as $audience)
                                    <span class="badge badge-light-primary">{{ \App\Models\Notice::AUDIENCES[$audience] ?? $audience }}</span>
                                @endforeach
                            </p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Created By:</small>
                            <p class="mb-2">{{ $notice->creator->name ?? 'N/A' }} on {{ $notice->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
