@extends('layouts.app')

@section('title', 'Promotion History')
@section('page-title', 'Promotion History')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.promotions.index') }}">Promotions</a></li>
    <li class="breadcrumb-item active">History</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Filters -->
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Filter Promotion Records</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.promotions.history') }}" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Academic Year</label>
                            <select name="academic_year_id" class="form-select">
                                <option value="">All Years</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                                        {{ $year->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Class</label>
                            <select name="class_id" class="form-select">
                                <option value="">All Classes</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="promoted" {{ request('status') == 'promoted' ? 'selected' : '' }}>Promoted</option>
                                <option value="retained" {{ request('status') == 'retained' ? 'selected' : '' }}>Retained</option>
                                <option value="alumni" {{ request('status') == 'alumni' ? 'selected' : '' }}>Alumni</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary me-2">Apply Filter</button>
                            <a href="{{ route('admin.promotions.history') }}" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Promotion Records -->
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>Promotion Records ({{ $promotions->total() }})</h5>
                        <a href="{{ route('admin.promotions.create') }}" class="btn btn-primary btn-sm">
                            <i class="fa fa-plus me-1"></i> New Promotion
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($promotions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th>#</th>
                                        <th>Student</th>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Attendance</th>
                                        <th>Marks</th>
                                        <th>Grade</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($promotions as $index => $promotion)
                                        <tr>
                                            <td>{{ $promotions->firstItem() + $index }}</td>
                                            <td>
                                                <strong>{{ $promotion->student->first_name ?? '' }} {{ $promotion->student->last_name ?? '' }}</strong>
                                                <br><small class="text-muted">{{ $promotion->student->admission_no ?? '-' }}</small>
                                            </td>
                                            <td>
                                                {{ $promotion->fromClass->name ?? '-' }}
                                                {{ $promotion->fromSection ? '- ' . $promotion->fromSection->name : '' }}
                                                <br><small class="text-muted">{{ $promotion->fromAcademicYear->name ?? '-' }}</small>
                                            </td>
                                            <td>
                                                @if($promotion->toClass)
                                                    {{ $promotion->toClass->name }}
                                                    {{ $promotion->toSection ? '- ' . $promotion->toSection->name : '' }}
                                                    <br><small class="text-muted">{{ $promotion->toAcademicYear->name ?? '-' }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($promotion->attendance_percentage !== null)
                                                    <span class="{{ $promotion->attendance_percentage >= 75 ? 'text-success' : 'text-danger' }}">
                                                        {{ $promotion->attendance_percentage }}%
                                                    </span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($promotion->final_percentage !== null)
                                                    <span class="{{ $promotion->final_percentage >= 33 ? 'text-success' : 'text-danger' }}">
                                                        {{ $promotion->final_percentage }}%
                                                    </span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($promotion->grade)
                                                    <span class="badge bg-{{ in_array($promotion->grade, ['A+', 'A']) ? 'success' : (in_array($promotion->grade, ['F']) ? 'danger' : 'primary') }}">
                                                        {{ $promotion->grade }}
                                                    </span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{!! $promotion->status_badge !!}</td>
                                            <td>{{ $promotion->promoted_at ? $promotion->promoted_at->format('M d, Y') : '-' }}</td>
                                            <td>
                                                @if(!in_array($promotion->status, ['cancelled']))
                                                    <form action="{{ route('admin.promotions.rollback', $promotion) }}" method="POST" class="d-inline delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-sm btn-outline-danger delete-confirm" data-name="promotion for {{ $promotion->student->first_name ?? 'this student' }}" title="Rollback">
                                                            <i class="fa fa-undo"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $promotions->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <svg class="stroke-icon" style="width: 60px; height: 60px; opacity: 0.5;">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#users') }}"></use>
                            </svg>
                            <h6 class="mt-3 text-muted">No Promotion Records Found</h6>
                            <p class="text-muted">No promotions match your filter criteria.</p>
                            <a href="{{ route('admin.promotions.create') }}" class="btn btn-primary">
                                <i class="fa fa-plus me-2"></i> Start New Promotion
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
