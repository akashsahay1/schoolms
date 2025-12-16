@extends('layouts.app')

@section('title', 'Fee Structure')

@section('page-title', 'Fee Structure Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Fee Structure</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>Fee Structures</h5>
                    <a href="{{ route('admin.fees.structure.create') }}" class="btn btn-primary">
                        <i data-feather="plus" class="me-1"></i> Add Fee Structure
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <form method="GET" action="{{ route('admin.fees.structure') }}" class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label">Academic Year</label>
                        <select name="academic_year_id" class="form-select" onchange="this.form.submit()">
                            <option value="">All Years</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }} {{ $year->is_active ? '(Active)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Class</label>
                        <select name="class_id" class="form-select" onchange="this.form.submit()">
                            <option value="">All Classes</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Fee Type</label>
                        <select name="fee_type_id" class="form-select" onchange="this.form.submit()">
                            <option value="">All Types</option>
                            @foreach($feeTypes as $type)
                                <option value="{{ $type->id }}" {{ request('fee_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if(request()->hasAny(['academic_year_id', 'class_id', 'fee_type_id']))
                        <div class="col-md-3 d-flex align-items-end">
                            <a href="{{ route('admin.fees.structure') }}" class="btn btn-light">
                                <i data-feather="refresh-cw" class="me-1"></i> Clear
                            </a>
                        </div>
                    @endif
                </form>

                <!-- Fee Structure Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Academic Year</th>
                                <th>Class</th>
                                <th>Fee Type</th>
                                <th>Fee Group</th>
                                <th>Amount</th>
                                <th>Due Date</th>
                                <th>Fine</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($feeStructures as $structure)
                                <tr>
                                    <td>{{ $loop->iteration + ($feeStructures->currentPage() - 1) * $feeStructures->perPage() }}</td>
                                    <td>
                                        <span class="badge badge-light-{{ $structure->academicYear->is_active ? 'success' : 'secondary' }}">
                                            {{ $structure->academicYear->name }}
                                        </span>
                                    </td>
                                    <td>{{ $structure->schoolClass->name }}</td>
                                    <td>{{ $structure->feeType->name }}</td>
                                    <td>{{ $structure->feeGroup->name }}</td>
                                    <td><strong>₹{{ number_format($structure->amount, 2) }}</strong></td>
                                    <td>{{ $structure->due_date ? $structure->due_date->format('d M Y') : '-' }}</td>
                                    <td>
                                        @if($structure->fine_type !== 'none')
                                            {{ $structure->fine_amount }}{{ $structure->fine_type === 'percentage' ? '%' : ' ₹' }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-light-{{ $structure->is_active ? 'success' : 'danger' }}">
                                            {{ $structure->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="common-align gap-2 justify-content-start">
                                            <a class="square-white" href="{{ route('admin.fees.structure.edit', $structure) }}" title="Edit">
                                                <svg><use href="{{ asset('assets/svg/icon-sprite.svg#edit-content') }}"></use></svg>
                                            </a>
                                            <form action="{{ route('admin.fees.structure.duplicate', $structure) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="square-white border-0 bg-transparent p-0" title="Duplicate">
                                                    <svg><use href="{{ asset('assets/svg/icon-sprite.svg#copy') }}"></use></svg>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.fees.structure.destroy', $structure) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="square-white trash-7 border-0 bg-transparent p-0 delete-confirm" title="Delete" data-name="{{ $structure->feeType->name }} - {{ $structure->schoolClass->name }}">
                                                    <svg><use href="{{ asset('assets/svg/icon-sprite.svg#trash1') }}"></use></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4">
                                        <div class="text-muted">
                                            <i data-feather="dollar-sign" style="width: 48px; height: 48px;"></i>
                                            <p class="mt-2 mb-0">No fee structures found.</p>
                                            <a href="{{ route('admin.fees.structure.create') }}" class="btn btn-primary mt-3">Add First Fee Structure</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($feeStructures->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $feeStructures->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
