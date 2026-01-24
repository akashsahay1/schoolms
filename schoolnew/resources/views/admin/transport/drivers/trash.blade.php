@extends('layouts.app')

@section('title', 'Drivers Trash')

@section('page-title', 'Transport - Drivers Trash')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.drivers.index') }}">Drivers</a></li>
    <li class="breadcrumb-item active">Trash</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>Deleted Drivers</h5>
                    <div class="d-flex gap-2">
                        @if($drivers->count() > 0)
                            <form action="{{ route('admin.drivers.empty-trash') }}" method="POST" id="emptyTrashForm">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger" id="emptyTrashBtn">
                                    <i data-feather="trash-2" class="me-1"></i> Empty Trash
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('admin.drivers.index') }}" class="btn btn-primary">
                            <i data-feather="arrow-left" class="me-1"></i> Back to Drivers
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Employee ID</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>License</th>
                                <th>Deleted At</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($drivers as $driver)
                                <tr>
                                    <td>{{ $drivers->firstItem() + $loop->index }}</td>
                                    <td>
                                        <span class="badge badge-light-primary">{{ $driver->employee_id }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $driver->full_name }}</strong>
                                        @if($driver->email)
                                            <br><small class="text-muted">{{ $driver->email }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $driver->phone }}</td>
                                    <td>{{ $driver->license_number }}</td>
                                    <td>{{ $driver->deleted_at->format('M d, Y h:i A') }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <form action="{{ route('admin.drivers.restore', $driver->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" title="Restore">
                                                    <i data-feather="refresh-ccw" style="width: 14px; height: 14px;"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.drivers.force-delete', $driver->id) }}" method="POST" class="d-inline force-delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger force-delete-btn" title="Delete Permanently" data-name="{{ $driver->full_name }}">
                                                    <i data-feather="x-circle" style="width: 14px; height: 14px;"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i data-feather="trash" class="mb-2" style="width: 48px; height: 48px;"></i>
                                            <p>Trash is empty.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($drivers->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $drivers->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
    // Empty trash confirmation
    jQuery('#emptyTrashBtn').on('click', function() {
        Swal.fire({
            title: 'Empty Trash?',
            text: 'This will permanently delete all items in trash. This action cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, empty trash!'
        }).then((result) => {
            if (result.isConfirmed) {
                jQuery('#emptyTrashForm').submit();
            }
        });
    });

    // Force delete confirmation
    jQuery('.force-delete-btn').on('click', function() {
        var name = jQuery(this).data('name');
        var form = jQuery(this).closest('form');

        Swal.fire({
            title: 'Delete Permanently?',
            text: 'Are you sure you want to permanently delete "' + name + '"? This cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete permanently!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    if (typeof feather !== 'undefined') feather.replace();
});
</script>
@endpush
