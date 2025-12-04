@extends('backEnd.master')
@section('title')
@lang('common.edit_user')
@endsection
@section('mainContent')

<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('common.edit_user')</h1>
            <div class="bc-pages">
                <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                <a href="#">@lang('common.administration')</a>
                <a href="{{route('admin.users.index')}}">@lang('common.users')</a>
                <a href="#">@lang('common.edit_user')</a>
            </div>
        </div>
    </div>
</section>

<section class="admin-visitor-area up_admin_visitor">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-lg-12">
                <div class="white-box">
                    <div class="main-title">
                        <h3 class="mb-30">@lang('common.edit_user')</h3>
                    </div>

                    {{ Form::open(['class' => 'form-horizontal', 'route' => ['admin.users.update', $user->id], 'method' => 'PUT']) }}
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="input-effect">
                                <input class="primary-input form-control{{ $errors->has('full_name') ? ' is-invalid' : '' }}"
                                    type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" required>
                                <label>@lang('common.full_name') <span class="text-danger">*</span></label>
                                <span class="focus-border"></span>
                                @if ($errors->has('full_name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('full_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-6 mt-30-md">
                            <div class="input-effect">
                                <input class="primary-input form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                    type="email" name="email" value="{{ old('email', $user->email) }}" required>
                                <label>@lang('common.email') <span class="text-danger">*</span></label>
                                <span class="focus-border"></span>
                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row mt-25">
                        <div class="col-lg-6">
                            <div class="input-effect">
                                <input class="primary-input form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}"
                                    type="text" name="phone" value="{{ old('phone', $user->phone) }}">
                                <label>@lang('common.phone')</label>
                                <span class="focus-border"></span>
                                @if ($errors->has('phone'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-6 mt-30-md">
                            <select class="niceSelect w-100 bb form-control{{ $errors->has('role_id') ? ' is-invalid' : '' }}"
                                name="role_id" required {{ $user->role_id == 1 ? 'disabled' : '' }}>
                                <option data-display="@lang('common.select_role')" value="">@lang('common.select_role') *</option>
                                @foreach($roles as $role)
                                <option value="{{$role->id}}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                    {{$role->name}}
                                </option>
                                @endforeach
                            </select>
                            @if ($user->role_id == 1)
                                <input type="hidden" name="role_id" value="{{ $user->role_id }}">
                                <small class="text-muted">@lang('common.admin_role_cannot_change')</small>
                            @endif
                            @if ($errors->has('role_id'))
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('role_id') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="row mt-25">
                        <div class="col-lg-6">
                            <div class="input-effect">
                                <input class="primary-input form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                    type="password" name="password">
                                <label>@lang('common.new_password') (@lang('common.leave_blank_to_keep'))</label>
                                <span class="focus-border"></span>
                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-6 mt-30-md">
                            <div class="input-effect">
                                <input class="primary-input form-control"
                                    type="password" name="password_confirmation">
                                <label>@lang('common.confirm_password')</label>
                                <span class="focus-border"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-40">
                        <div class="col-lg-12 text-center">
                            <a href="{{ route('admin.users.index') }}" class="primary-btn tr-bg mr-2">
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
