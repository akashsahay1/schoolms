@extends('backEnd.master')
@section('title')
@lang('academics.edit_teacher')
@endsection
@section('mainContent')

<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('academics.edit_teacher')</h1>
            <div class="bc-pages">
                <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                <a href="#">@lang('academics.academics')</a>
                <a href="{{route('academic.teachers.index')}}">@lang('academics.teachers')</a>
                <a href="#">@lang('academics.edit_teacher')</a>
            </div>
        </div>
    </div>
</section>

<section class="admin-visitor-area up_admin_visitor">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-lg-12">
                <div class="white-box">
                    {{ Form::open(['class' => 'form-horizontal', 'route' => ['academic.teachers.update', $teacher->id], 'method' => 'PUT', 'files' => true]) }}

                    {{-- Basic Information --}}
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="main-title">
                                <h4 class="stu-sub-head">@lang('common.basic_information')</h4>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-25">
                        <div class="col-lg-4">
                            <div class="input-effect">
                                <input class="primary-input form-control" type="text" name="first_name"
                                    value="{{ old('first_name', $teacher->first_name) }}" required>
                                <label>@lang('common.first_name') <span class="text-danger">*</span></label>
                                <span class="focus-border"></span>
                            </div>
                        </div>
                        <div class="col-lg-4 mt-30-md">
                            <div class="input-effect">
                                <input class="primary-input form-control" type="text" name="last_name"
                                    value="{{ old('last_name', $teacher->last_name) }}">
                                <label>@lang('common.last_name')</label>
                                <span class="focus-border"></span>
                            </div>
                        </div>
                        <div class="col-lg-4 mt-30-md">
                            <div class="input-effect">
                                <input class="primary-input form-control" type="email" name="email"
                                    value="{{ old('email', $teacher->email) }}" required>
                                <label>@lang('common.email') <span class="text-danger">*</span></label>
                                <span class="focus-border"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-25">
                        <div class="col-lg-4">
                            <div class="input-effect">
                                <input class="primary-input form-control" type="text" name="staff_no"
                                    value="{{ old('staff_no', $teacher->staff_no) }}">
                                <label>@lang('hr.staff_no')</label>
                                <span class="focus-border"></span>
                            </div>
                        </div>
                        <div class="col-lg-4 mt-30-md">
                            <select class="niceSelect w-100 bb form-control" name="gender_id">
                                <option data-display="@lang('common.gender')" value="">@lang('common.select_gender')</option>
                                @foreach($genders as $gender)
                                <option value="{{$gender->id}}" {{ old('gender_id', $teacher->gender_id) == $gender->id ? 'selected' : '' }}>
                                    {{$gender->base_setup_name}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4 mt-30-md">
                            <div class="input-effect">
                                <input class="primary-input form-control" type="text" name="mobile"
                                    value="{{ old('mobile', $teacher->mobile) }}">
                                <label>@lang('common.mobile')</label>
                                <span class="focus-border"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Department & Designation --}}
                    <div class="row mt-40">
                        <div class="col-lg-12">
                            <div class="main-title">
                                <h4 class="stu-sub-head">@lang('hr.department') & @lang('hr.designation')</h4>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-25">
                        <div class="col-lg-4">
                            <select class="niceSelect w-100 bb form-control" name="department_id">
                                <option data-display="@lang('hr.department')" value="">@lang('common.select') @lang('hr.department')</option>
                                @foreach($departments as $department)
                                <option value="{{$department->id}}" {{ old('department_id', $teacher->department_id) == $department->id ? 'selected' : '' }}>
                                    {{$department->name}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4 mt-30-md">
                            <select class="niceSelect w-100 bb form-control" name="designation_id">
                                <option data-display="@lang('hr.designation')" value="">@lang('common.select') @lang('hr.designation')</option>
                                @foreach($designations as $designation)
                                <option value="{{$designation->id}}" {{ old('designation_id', $teacher->designation_id) == $designation->id ? 'selected' : '' }}>
                                    {{$designation->title}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4 mt-30-md">
                            <select class="niceSelect w-100 bb form-control" name="marital_status">
                                <option data-display="@lang('common.marital_status')" value="">@lang('common.select') @lang('common.marital_status')</option>
                                @foreach($marital_status as $status)
                                <option value="{{$status->id}}" {{ old('marital_status', $teacher->marital_status) == $status->id ? 'selected' : '' }}>
                                    {{$status->base_setup_name}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Dates --}}
                    <div class="row mt-25">
                        <div class="col-lg-4">
                            <div class="row no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <input class="primary-input date form-control" type="text" name="date_of_birth"
                                            value="{{ old('date_of_birth', $teacher->date_of_birth ? date('m/d/Y', strtotime($teacher->date_of_birth)) : '') }}" autocomplete="off">
                                        <label>@lang('common.date_of_birth')</label>
                                        <span class="focus-border"></span>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <button class="" type="button">
                                        <i class="ti-calendar"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mt-30-md">
                            <div class="row no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <input class="primary-input date form-control" type="text" name="date_of_joining"
                                            value="{{ old('date_of_joining', $teacher->date_of_joining ? date('m/d/Y', strtotime($teacher->date_of_joining)) : '') }}" autocomplete="off">
                                        <label>@lang('hr.date_of_joining')</label>
                                        <span class="focus-border"></span>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <button class="" type="button">
                                        <i class="ti-calendar"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mt-30-md">
                            <div class="input-effect">
                                <input class="primary-input form-control" type="text" name="qualification"
                                    value="{{ old('qualification', $teacher->qualification) }}">
                                <label>@lang('hr.qualification')</label>
                                <span class="focus-border"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Contact Information --}}
                    <div class="row mt-40">
                        <div class="col-lg-12">
                            <div class="main-title">
                                <h4 class="stu-sub-head">@lang('common.contact_information')</h4>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-25">
                        <div class="col-lg-6">
                            <div class="input-effect">
                                <textarea class="primary-input form-control" name="current_address" rows="3">{{ old('current_address', $teacher->current_address) }}</textarea>
                                <label>@lang('common.current_address')</label>
                                <span class="focus-border"></span>
                            </div>
                        </div>
                        <div class="col-lg-6 mt-30-md">
                            <div class="input-effect">
                                <textarea class="primary-input form-control" name="permanent_address" rows="3">{{ old('permanent_address', $teacher->permanent_address) }}</textarea>
                                <label>@lang('common.permanent_address')</label>
                                <span class="focus-border"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-25">
                        <div class="col-lg-4">
                            <div class="input-effect">
                                <input class="primary-input form-control" type="text" name="emergency_mobile"
                                    value="{{ old('emergency_mobile', $teacher->emergency_mobile) }}">
                                <label>@lang('hr.emergency_mobile')</label>
                                <span class="focus-border"></span>
                            </div>
                        </div>
                        <div class="col-lg-4 mt-30-md">
                            <div class="input-effect">
                                <input class="primary-input form-control" type="text" name="experience"
                                    value="{{ old('experience', $teacher->experience) }}">
                                <label>@lang('hr.experience')</label>
                                <span class="focus-border"></span>
                            </div>
                        </div>
                        <div class="col-lg-4 mt-30-md">
                            <div class="input-effect">
                                <input class="primary-input form-control" type="number" step="0.01" name="basic_salary"
                                    value="{{ old('basic_salary', $teacher->basic_salary) }}">
                                <label>@lang('hr.basic_salary')</label>
                                <span class="focus-border"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Photo Upload --}}
                    <div class="row mt-40">
                        <div class="col-lg-12">
                            <div class="main-title">
                                <h4 class="stu-sub-head">@lang('common.photo')</h4>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-25">
                        <div class="col-lg-6">
                            @if($teacher->staff_photo)
                            <div class="mb-3">
                                <img src="{{ asset($teacher->staff_photo) }}" alt="Teacher Photo" class="img-thumbnail" style="max-width: 150px;">
                            </div>
                            @endif
                            <div class="input-effect">
                                <input type="file" name="staff_photo" class="form-control">
                                <small class="text-muted">@lang('common.leave_blank_to_keep')</small>
                            </div>
                        </div>
                    </div>

                    {{-- Change Password --}}
                    <div class="row mt-40">
                        <div class="col-lg-12">
                            <div class="main-title">
                                <h4 class="stu-sub-head">@lang('common.change_password')</h4>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-25">
                        <div class="col-lg-6">
                            <div class="input-effect">
                                <input class="primary-input form-control" type="password" name="password">
                                <label>@lang('common.new_password') (@lang('common.leave_blank_to_keep'))</label>
                                <span class="focus-border"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-40">
                        <div class="col-lg-12 text-center">
                            <a href="{{ route('academic.teachers.index') }}" class="primary-btn tr-bg mr-2">
                                @lang('common.cancel')
                            </a>
                            <button type="submit" class="primary-btn fix-gr-bg">
                                <span class="ti-check"></span>
                                @lang('common.update')
                            </button>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
