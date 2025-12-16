@extends('layouts.app')

@section('title', 'Academic Years')

@section('page-title', 'Academic Years')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Academic Years</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div></div>
            <div>
                <a href="{{ route('admin.academic-years.create') }}" class="btn btn-primary">
                    <i class="icofont icofont-plus"></i> Add Academic Year
                </a>
            </div>
        </div>
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
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($academicYears as $year)
                                    <tr>
                                        <td>{{ $year->name }}</td>
                                        <td>{{ $year->start_date->format('M d, Y') }}</td>
                                        <td>{{ $year->end_date->format('M d, Y') }}</td>
                                        <td>
                                            @if($year->is_active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $year->description ?? '-' }}</td>
                                        <td>
                                            @if(!$year->is_active && auth()->user()->can('academic_year_update'))
                                                <form action="{{ route('admin.academic-years.set-active', $year) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-info" 
                                                            onclick="return confirm('Are you sure you want to activate this academic year?')">
                                                        <i class="icofont icofont-ui-check"></i> Set Active
                                                    </button>
                                                </form>
                                            @endif

                                            @can('academic_year_update')
                                                <a href="{{ route('admin.academic-years.edit', $year) }}" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="icofont icofont-edit"></i>
                                                </a>
                                            @endcan

                                            @can('academic_year_delete')
                                                <form action="{{ route('admin.academic-years.destroy', $year) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                            onclick="return confirm('Are you sure you want to delete this academic year?')"
                                                            @if($year->is_active) disabled @endif>
                                                        <i class="icofont icofont-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No academic years found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $academicYears->links() }}
                    </div>
                </div>
            </div>
    </div>
</div>
@endsection