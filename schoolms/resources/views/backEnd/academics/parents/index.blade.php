@extends('backEnd.master')
@section('title')
@lang('academics.parents')
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
            <h1>@lang('academics.parents')</h1>
            <div class="bc-pages">
                <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                <a href="#">@lang('academics.academics')</a>
                <a href="#">@lang('academics.parents')</a>
            </div>
        </div>
    </div>
</section>

<section class="admin-visitor-area up_admin_visitor">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-lg-12">
                <div class="main-title xs_mt_0 mt_0_sm">
                    <h3 class="mb-30">@lang('common.select_criteria')</h3>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="white-box">
                    {{ Form::open(['class' => 'form-horizontal', 'route' => 'academic.parents.search', 'method' => 'POST']) }}
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="input-effect">
                                <input class="primary-input" type="text" placeholder="@lang('common.search_by_name')" name="search_name">
                                <span class="focus-border"></span>
                            </div>
                        </div>

                        <div class="col-lg-4 mt-30-md">
                            <div class="input-effect">
                                <input class="primary-input" type="text" placeholder="@lang('common.search_by_phone')" name="search_phone">
                                <span class="focus-border"></span>
                            </div>
                        </div>

                        <div class="col-lg-4 mt-30-md">
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

        <div class="row mt-20">
            <div class="col-lg-12">
                <div class="alert alert-info">
                    <i class="ti-info-alt mr-2"></i>
                    @lang('academics.parent_note')
                </div>
            </div>
        </div>

        <div class="row mt-20 full_wide_table">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-4 no-gutters">
                        <div class="main-title">
                            <h3 class="mb-0">@lang('academics.parent_list')</h3>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <table id="table_id" class="display school-table" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>@lang('common.sl')</th>
                                    <th>@lang('student.guardian_name')</th>
                                    <th>@lang('student.fathers_name')</th>
                                    <th>@lang('student.mothers_name')</th>
                                    <th>@lang('student.guardian_phone')</th>
                                    <th>@lang('student.guardian_email')</th>
                                    <th>@lang('academics.children')</th>
                                    <th>@lang('common.login_status')</th>
                                    <th>@lang('common.action')</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($parents as $key => $parent)
                                <tr id="parent-{{$parent->id}}">
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $parent->guardians_name ?? '-' }}</td>
                                    <td>{{ $parent->fathers_name ?? '-' }}</td>
                                    <td>{{ $parent->mothers_name ?? '-' }}</td>
                                    <td>{{ $parent->guardians_mobile ?? $parent->fathers_mobile ?? '-' }}</td>
                                    <td>{{ $parent->guardians_email ?? '-' }}</td>
                                    <td>
                                        <span class="badge badge-primary">{{ $parent->children_count }}</span>
                                    </td>
                                    <td>
                                        @if($parent->parent_user)
                                            <label class="switch">
                                                <input type="checkbox" id="status-{{$parent->id}}"
                                                    class="switch-input-parent"
                                                    data-id="{{$parent->id}}"
                                                    {{ $parent->parent_user->active_status == 1 ? 'checked' : '' }}>
                                                <span class="slider round"></span>
                                            </label>
                                        @else
                                            <span class="badge badge-secondary">@lang('common.no_account')</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                                                @lang('common.select')
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="{{route('academic.parents.show', $parent->id)}}">
                                                    @lang('common.view')
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
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
        // Handle parent login status toggle
        $('.switch-input-parent').on('change', function() {
            var parentId = $(this).data('id');
            var status = $(this).is(':checked') ? 'on' : 'off';

            $.ajax({
                url: "{{ route('academic.parents.toggle-status') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: parentId,
                    status: status
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Login status updated successfully');
                    }
                },
                error: function(xhr) {
                    toastr.error('Failed to update status');
                    // Revert checkbox state
                    $('#status-' + parentId).prop('checked', !$('#status-' + parentId).is(':checked'));
                }
            });
        });
    });
</script>
@endsection
