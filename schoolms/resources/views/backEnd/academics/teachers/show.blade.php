@extends('backEnd.master')
@section('title')
@lang('academics.teacher_details')
@endsection
@section('mainContent')

<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('academics.teacher_details')</h1>
            <div class="bc-pages">
                <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                <a href="#">@lang('academics.academics')</a>
                <a href="{{route('academic.teachers.index')}}">@lang('academics.teachers')</a>
                <a href="#">@lang('academics.teacher_details')</a>
            </div>
        </div>
    </div>
</section>

<section class="admin-visitor-area up_admin_visitor">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-lg-3">
                <div class="white-box">
                    <div class="text-center">
                        @if($teacher->staff_photo)
                            <img src="{{ asset($teacher->staff_photo) }}" alt="Teacher Photo"
                                class="img-thumbnail rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto"
                                style="width: 150px; height: 150px;">
                                <span class="text-white display-4">{{ substr($teacher->first_name, 0, 1) }}</span>
                            </div>
                        @endif
                        <h4 class="mt-3">{{ $teacher->full_name }}</h4>
                        <p class="text-muted">{{ $teacher->designations->title ?? 'Teacher' }}</p>

                        <span class="badge {{ $teacher->active_status ? 'badge-success' : 'badge-danger' }}">
                            {{ $teacher->active_status ? __('common.active') : __('common.inactive') }}
                        </span>
                    </div>

                    <hr>

                    <div class="teacher-info">
                        <p><strong>@lang('hr.staff_no'):</strong> {{ $teacher->staff_no }}</p>
                        <p><strong>@lang('common.email'):</strong> {{ $teacher->email }}</p>
                        <p><strong>@lang('common.mobile'):</strong> {{ $teacher->mobile ?? '-' }}</p>
                        <p><strong>@lang('hr.department'):</strong> {{ $teacher->departments->name ?? '-' }}</p>
                    </div>

                    <div class="mt-3 text-center">
                        <a href="{{ route('academic.teachers.edit', $teacher->id) }}" class="primary-btn small fix-gr-bg">
                            <i class="ti-pencil"></i> @lang('common.edit')
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="white-box">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" role="tab">
                                @lang('common.profile')
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="payroll-tab" data-toggle="tab" href="#payroll" role="tab">
                                @lang('hr.payroll')
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="leave-tab" data-toggle="tab" href="#leave" role="tab">
                                @lang('leave.leave')
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="documents-tab" data-toggle="tab" href="#documents" role="tab">
                                @lang('common.documents')
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content mt-3" id="myTabContent">
                        {{-- Profile Tab --}}
                        <div class="tab-pane fade show active" id="profile" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="mb-3">@lang('common.personal_information')</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>@lang('common.first_name'):</strong></td>
                                            <td>{{ $teacher->first_name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('common.last_name'):</strong></td>
                                            <td>{{ $teacher->last_name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('common.gender'):</strong></td>
                                            <td>{{ $teacher->genders->base_setup_name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('common.date_of_birth'):</strong></td>
                                            <td>{{ $teacher->date_of_birth ? date('d M, Y', strtotime($teacher->date_of_birth)) : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('common.marital_status'):</strong></td>
                                            <td>{{ $teacher->maritalStatus->base_setup_name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('hr.fathers_name'):</strong></td>
                                            <td>{{ $teacher->fathers_name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('hr.mothers_name'):</strong></td>
                                            <td>{{ $teacher->mothers_name ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="mb-3">@lang('common.employment_information')</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>@lang('hr.date_of_joining'):</strong></td>
                                            <td>{{ $teacher->date_of_joining ? date('d M, Y', strtotime($teacher->date_of_joining)) : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('hr.qualification'):</strong></td>
                                            <td>{{ $teacher->qualification ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('hr.experience'):</strong></td>
                                            <td>{{ $teacher->experience ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('hr.contract_type'):</strong></td>
                                            <td>{{ $teacher->contract_type ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('hr.basic_salary'):</strong></td>
                                            <td>{{ generalSetting()->currency_symbol }}{{ number_format($teacher->basic_salary, 2) }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="mb-3">@lang('common.contact_information')</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>@lang('common.email'):</strong></td>
                                            <td>{{ $teacher->email }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('common.mobile'):</strong></td>
                                            <td>{{ $teacher->mobile ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('hr.emergency_mobile'):</strong></td>
                                            <td>{{ $teacher->emergency_mobile ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('common.current_address'):</strong></td>
                                            <td>{{ $teacher->current_address ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('common.permanent_address'):</strong></td>
                                            <td>{{ $teacher->permanent_address ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Payroll Tab --}}
                        <div class="tab-pane fade" id="payroll" role="tabpanel">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>@lang('hr.payslip_id')</th>
                                        <th>@lang('common.month')</th>
                                        <th>@lang('hr.gross_salary')</th>
                                        <th>@lang('hr.net_salary')</th>
                                        <th>@lang('common.status')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payrollDetails as $payroll)
                                    <tr>
                                        <td>{{ $payroll->payroll_id ?? $payroll->id }}</td>
                                        <td>{{ date('M Y', strtotime($payroll->payroll_month)) }}</td>
                                        <td>{{ generalSetting()->currency_symbol }}{{ number_format($payroll->gross_salary, 2) }}</td>
                                        <td>{{ generalSetting()->currency_symbol }}{{ number_format($payroll->net_salary, 2) }}</td>
                                        <td>
                                            <span class="badge badge-{{ $payroll->payroll_status == 'P' ? 'success' : 'warning' }}">
                                                {{ $payroll->payroll_status == 'P' ? __('common.paid') : __('common.pending') }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">@lang('common.no_data_found')</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Leave Tab --}}
                        <div class="tab-pane fade" id="leave" role="tabpanel">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>@lang('leave.leave_type')</th>
                                        <th>@lang('common.from_date')</th>
                                        <th>@lang('common.to_date')</th>
                                        <th>@lang('common.days')</th>
                                        <th>@lang('common.status')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($leaveDetails as $leave)
                                    <tr>
                                        <td>{{ $leave->leaveType->type ?? '-' }}</td>
                                        <td>{{ date('d M, Y', strtotime($leave->leave_from)) }}</td>
                                        <td>{{ date('d M, Y', strtotime($leave->leave_to)) }}</td>
                                        <td>{{ $leave->total_days ?? '-' }}</td>
                                        <td>
                                            @if($leave->approve_status == 'P')
                                                <span class="badge badge-warning">@lang('common.pending')</span>
                                            @elseif($leave->approve_status == 'A')
                                                <span class="badge badge-success">@lang('common.approved')</span>
                                            @else
                                                <span class="badge badge-danger">@lang('common.rejected')</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">@lang('common.no_data_found')</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Documents Tab --}}
                        <div class="tab-pane fade" id="documents" role="tabpanel">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>@lang('common.title')</th>
                                        <th>@lang('common.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($documents as $document)
                                    <tr>
                                        <td>{{ $document->title }}</td>
                                        <td>
                                            @if($document->file)
                                                <a href="{{ asset($document->file) }}" target="_blank" class="btn btn-sm btn-primary">
                                                    <i class="ti-download"></i> @lang('common.download')
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="2" class="text-center">@lang('common.no_data_found')</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
