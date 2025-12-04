@extends('backEnd.master')
@section('title')
@lang('common.users')
@endsection
@section('mainContent')
@push('css')
<style type="text/css">
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked + .slider {
        background: linear-gradient(90deg, #7c32ff 0%, #c738d8 51%, #7c32ff 100%);
    }

    input:focus + .slider {
        box-shadow: 0 0 1px linear-gradient(90deg, #7c32ff 0%, #c738d8 51%, #7c32ff 100%);
    }

    input:checked + .slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
</style>
@endpush

<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('common.users')</h1>
            <div class="bc-pages">
                <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                <a href="#">@lang('common.administration')</a>
                <a href="#">@lang('common.users')</a>
            </div>
        </div>
    </div>
</section>

<section class="admin-visitor-area up_admin_visitor">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-lg-8 col-md-6 col-6">
                <div class="main-title xs_mt_0 mt_0_sm">
                    <h3 class="mb-30">@lang('common.select_criteria')</h3>
                </div>
            </div>

            <div class="col-lg-4 text-md-right text-left col-md-6 mb-30-lg col-6 text_sm_right">
                <a href="{{route('admin.users.create')}}" class="primary-btn small fix-gr-bg">
                    <span class="ti-plus pr-2"></span>
                    @lang('common.add_user')
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="white-box">
                    {{ Form::open(['class' => 'form-horizontal', 'route' => 'admin.users.search', 'method' => 'POST']) }}
                    <div class="row">
                        <div class="col-lg-4">
                            <select class="niceSelect w-100 bb form-control" name="role_id" id="role_id">
                                <option data-display="@lang('hr.role')" value="">@lang('common.select')</option>
                                @foreach($roles as $role)
                                <option value="{{$role->id}}">{{$role->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-4 mt-30-md">
                            <div class="input-effect">
                                <input class="primary-input" type="text" placeholder="@lang('common.search_by_name')" name="search_name">
                                <span class="focus-border"></span>
                            </div>
                        </div>

                        <div class="col-lg-4 mt-30-md">
                            <div class="input-effect">
                                <input class="primary-input" type="text" placeholder="@lang('common.search_by_email')" name="search_email">
                                <span class="focus-border"></span>
                            </div>
                        </div>

                        <div class="col-lg-12 mt-20 text-right">
                            <button type="submit" class="primary-btn small fix-gr-bg">
                                <span class="ti-search pr-2"></span>
                                @lang('common.search')
                            </button>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>

        <div class="row mt-40 full_wide_table">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-4 no-gutters">
                        <div class="main-title">
                            <h3 class="mb-0">@lang('common.user_list')</h3>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <table id="table_id" class="display school-table" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>@lang('common.sl')</th>
                                    <th>@lang('common.name')</th>
                                    <th>@lang('common.email')</th>
                                    <th>@lang('common.phone')</th>
                                    <th>@lang('hr.role')</th>
                                    <th>@lang('common.status')</th>
                                    <th>@lang('common.action')</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($users as $key => $user)
                                <tr id="user-{{$user->id}}">
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $user->full_name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone }}</td>
                                    <td>{{ $user->roles->name ?? '-' }}</td>
                                    <td>
                                        @if ($user->role_id != 1 || $user->id == Auth::user()->id)
                                            <label class="switch">
                                                <input type="checkbox" id="status-{{$user->id}}" class="switch-input-user"
                                                    data-id="{{$user->id}}"
                                                    {{ $user->active_status == 1 ? 'checked' : '' }}
                                                    {{ $user->role_id == 1 ? 'disabled' : '' }}>
                                                <span class="slider round"></span>
                                            </label>
                                        @else
                                            <span class="badge badge-success">Active</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                                                @lang('common.select')
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="{{route('admin.users.edit', $user->id)}}">
                                                    @lang('common.edit')
                                                </a>
                                                @if ($user->role_id != 1 && $user->id != Auth::user()->id)
                                                    <a class="dropdown-item" href="#" data-toggle="modal"
                                                        data-target="#deleteUserModal{{$user->id}}">
                                                        @lang('common.delete')
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Delete Modal -->
                                <div class="modal fade admin-query" id="deleteUserModal{{$user->id}}">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title">@lang('common.confirmation_required')</h4>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="text-center">
                                                    <h4 class="text-danger">@lang('common.are_you_sure_to_delete') {{ $user->full_name }}?</h4>
                                                </div>

                                                <div class="mt-40 d-flex justify-content-between">
                                                    <button type="button" class="primary-btn tr-bg" data-dismiss="modal">
                                                        @lang('common.cancel')
                                                    </button>
                                                    <form action="{{route('admin.users.destroy', $user->id)}}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="primary-btn fix-gr-bg" type="submit">
                                                            @lang('common.delete')
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Handle user status toggle
        $('.switch-input-user').on('change', function() {
            var userId = $(this).data('id');
            var status = $(this).is(':checked') ? 'on' : 'off';

            $.ajax({
                url: "{{ route('admin.users.toggle-status') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: userId,
                    status: status
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Status updated successfully');
                    }
                },
                error: function(xhr) {
                    toastr.error('Failed to update status');
                    // Revert checkbox state
                    $('#status-' + userId).prop('checked', !$('#status-' + userId).is(':checked'));
                }
            });
        });
    });
</script>
@endsection
