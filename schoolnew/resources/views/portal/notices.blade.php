@extends('layouts.portal')

@section('title', 'Notices')
@section('page-title', 'Notices')

@section('breadcrumb')
    <li class="breadcrumb-item active">Notices</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Filter -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('portal.notices') }}" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Filter by Type</label>
                            <select name="type" class="form-select">
                                <option value="">All Types</option>
                                @foreach(\App\Models\Notice::TYPES as $key => $label)
                                    <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('portal.notices') }}" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Notices List -->
    <div class="row">
        <div class="col-12">
            @if($notices->count() > 0)
                @foreach($notices as $notice)
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <span class="badge {{ $notice->getTypeBadgeClass() }} mb-2">{{ $notice->getTypeLabel() }}</span>
                                    <h5 class="mb-2">{{ $notice->title }}</h5>
                                    <p class="text-muted mb-2">
                                        <i class="fa fa-calendar me-1"></i> {{ $notice->publish_date->format('M d, Y') }}
                                        @if($notice->expiry_date)
                                            <span class="ms-3"><i class="fa fa-clock-o me-1"></i> Expires: {{ $notice->expiry_date->format('M d, Y') }}</span>
                                        @endif
                                    </p>
                                    <p class="mb-0">{{ Str::limit(strip_tags($notice->content), 200) }}</p>
                                </div>
                                <div>
                                    <a href="{{ route('portal.notices.show', $notice) }}" class="btn btn-outline-primary btn-sm">
                                        Read More <i class="fa fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="mt-3">
                    {{ $notices->links() }}
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fa fa-bell-slash fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Notices Available</h5>
                        <p class="text-muted">There are no notices at this time.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
