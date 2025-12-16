@extends('layouts.portal')

@section('title', $notice->title)
@section('page-title', 'Notice Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('portal.notices') }}">Notices</a></li>
    <li class="breadcrumb-item active">View</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="mb-4">
                        <span class="badge {{ $notice->getTypeBadgeClass() }}">{{ $notice->getTypeLabel() }}</span>
                        @if($notice->isExpired())
                            <span class="badge badge-light-danger ms-2">Expired</span>
                        @endif
                    </div>

                    <h3 class="mb-3">{{ $notice->title }}</h3>

                    <div class="text-muted mb-4">
                        <span class="me-3"><i class="fa fa-calendar me-1"></i> Published: {{ $notice->publish_date->format('F d, Y') }}</span>
                        @if($notice->expiry_date)
                            <span><i class="fa fa-clock-o me-1"></i> Expires: {{ $notice->expiry_date->format('F d, Y') }}</span>
                        @endif
                    </div>

                    <hr>

                    <div class="notice-content py-3">
                        {!! nl2br(e($notice->content)) !!}
                    </div>

                    @if($notice->attachment)
                        <hr>
                        <div class="mt-3">
                            <h6>Attachment:</h6>
                            <a href="{{ asset('storage/' . $notice->attachment) }}" class="btn btn-outline-primary" target="_blank">
                                <i class="fa fa-download me-1"></i> Download Attachment
                            </a>
                        </div>
                    @endif

                    <hr>

                    <div class="mt-4">
                        <a href="{{ route('portal.notices') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left me-1"></i> Back to Notices
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
