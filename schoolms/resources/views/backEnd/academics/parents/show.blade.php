@extends('backEnd.master')
@section('title')
@lang('academics.parent_details')
@endsection
@section('mainContent')

<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('academics.parent_details')</h1>
            <div class="bc-pages">
                <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                <a href="#">@lang('academics.academics')</a>
                <a href="{{route('academic.parents.index')}}">@lang('academics.parents')</a>
                <a href="#">@lang('academics.parent_details')</a>
            </div>
        </div>
    </div>
</section>

<section class="admin-visitor-area up_admin_visitor">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-lg-4">
                <div class="white-box">
                    <div class="text-center">
                        @if($parent->guardians_photo || $parent->fathers_photo)
                            <img src="{{ asset($parent->guardians_photo ?? $parent->fathers_photo) }}" alt="Parent Photo"
                                class="img-thumbnail rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto"
                                style="width: 150px; height: 150px;">
                                <span class="text-white display-4">{{ substr($parent->guardians_name ?? $parent->fathers_name ?? 'P', 0, 1) }}</span>
                            </div>
                        @endif
                        <h4 class="mt-3">{{ $parent->guardians_name ?? $parent->fathers_name ?? '-' }}</h4>
                        <p class="text-muted">@lang('common.guardian')</p>

                        @if($parent->parent_user)
                            <span class="badge {{ $parent->parent_user->active_status ? 'badge-success' : 'badge-danger' }}">
                                {{ $parent->parent_user->active_status ? __('common.login_enabled') : __('common.login_disabled') }}
                            </span>
                        @else
                            <span class="badge badge-secondary">@lang('common.no_account')</span>
                        @endif
                    </div>

                    <hr>

                    <div class="parent-info">
                        <p><strong>@lang('student.guardian_phone'):</strong> {{ $parent->guardians_mobile ?? '-' }}</p>
                        <p><strong>@lang('student.guardian_email'):</strong> {{ $parent->guardians_email ?? '-' }}</p>
                        <p><strong>@lang('student.guardian_relation'):</strong> {{ $parent->guardians_relation ?? '-' }}</p>
                        <p><strong>@lang('student.guardian_occupation'):</strong> {{ $parent->guardians_occupation ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="white-box">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="details-tab" data-toggle="tab" href="#details" role="tab">
                                @lang('common.details')
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="children-tab" data-toggle="tab" href="#children" role="tab">
                                @lang('academics.children') ({{ count($children) }})
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content mt-3" id="myTabContent">
                        {{-- Details Tab --}}
                        <div class="tab-pane fade show active" id="details" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="mb-3">@lang('student.father_information')</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>@lang('student.fathers_name'):</strong></td>
                                            <td>{{ $parent->fathers_name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('student.fathers_mobile'):</strong></td>
                                            <td>{{ $parent->fathers_mobile ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('student.fathers_occupation'):</strong></td>
                                            <td>{{ $parent->fathers_occupation ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="mb-3">@lang('student.mother_information')</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>@lang('student.mothers_name'):</strong></td>
                                            <td>{{ $parent->mothers_name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('student.mothers_mobile'):</strong></td>
                                            <td>{{ $parent->mothers_mobile ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('student.mothers_occupation'):</strong></td>
                                            <td>{{ $parent->mothers_occupation ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-12">
                                    <h5 class="mb-3">@lang('student.guardian_information')</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>@lang('student.guardian_name'):</strong></td>
                                            <td>{{ $parent->guardians_name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('student.guardian_relation'):</strong></td>
                                            <td>{{ $parent->guardians_relation ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('student.guardian_phone'):</strong></td>
                                            <td>{{ $parent->guardians_mobile ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('student.guardian_email'):</strong></td>
                                            <td>{{ $parent->guardians_email ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('student.guardian_occupation'):</strong></td>
                                            <td>{{ $parent->guardians_occupation ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('student.guardian_address'):</strong></td>
                                            <td>{{ $parent->guardians_address ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Children Tab --}}
                        <div class="tab-pane fade" id="children" role="tabpanel">
                            <div class="alert alert-info mb-3">
                                <i class="ti-info-alt mr-2"></i>
                                @lang('academics.children_note')
                            </div>

                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>@lang('common.photo')</th>
                                        <th>@lang('student.admission_no')</th>
                                        <th>@lang('common.name')</th>
                                        <th>@lang('student.class')</th>
                                        <th>@lang('student.section')</th>
                                        <th>@lang('common.status')</th>
                                        <th>@lang('common.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($children as $child)
                                    <tr>
                                        <td>
                                            @if($child->student_photo)
                                                <img src="{{ asset($child->student_photo) }}" alt="Student Photo"
                                                    class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center"
                                                    style="width: 40px; height: 40px;">
                                                    <span class="text-white">{{ substr($child->first_name, 0, 1) }}</span>
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $child->admission_no }}</td>
                                        <td>{{ $child->full_name }}</td>
                                        <td>{{ $child->class->class_name ?? '-' }}</td>
                                        <td>{{ $child->section->section_name ?? '-' }}</td>
                                        <td>
                                            <span class="badge {{ $child->active_status ? 'badge-success' : 'badge-danger' }}">
                                                {{ $child->active_status ? __('common.active') : __('common.inactive') }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('student_view', $child->id) }}" class="btn btn-sm btn-primary">
                                                <i class="ti-eye"></i> @lang('common.view')
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center">@lang('common.no_data_found')</td>
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
