@extends('backEnd.master')
@section('title')
{{$student_detail->first_name.' '.$student_detail->last_name}} @lang('student.teacher_list')
@endsection
@section('mainContent')
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('student.teacher_list')</h1>
            <div class="bc-pages">
                <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                <a href="{{route('parent_subjects', [$student_detail->id])}}">@lang('student.teacher_list')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area">
    <div class="container-fluid p-0">
       <div class="row mt-40">
            <div class="col-lg-6 col-md-6">
                <div class="main-title">
                    <h3 class="mb-30">@lang('student.student_information')</h3>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 mb-30">
                <!-- Start Student Meta Information -->
                <div class="student-meta-box">
                    <div class="student-meta-top"></div>
                    <img class="student-meta-img img-100" src="{{asset($student_detail->student_photo)}}" alt="">
                    <div class="white-box radius-t-y-0">
                        <div class="single-meta mt-10">
                            <div class="d-flex justify-content-between">
                                <div class="name">
                                    @lang('student.student_name')
                                </div>
                                <div class="value">
                                    {{$student_detail->first_name.' '.$student_detail->last_name}}
                                </div>
                            </div>
                        </div>
                        <div class="single-meta">
                            <div class="d-flex justify-content-between">
                                <div class="name">
                                    @lang('student.admission_no')
                                </div>
                                <div class="value">
                                    {{$student_detail->admission_no}}
                                </div>
                            </div>
                        </div>
                        <div class="single-meta">
                            <div class="d-flex justify-content-between">
                                <div class="name">
                                    @lang('student.roll_number')
                                </div>
                                <div class="value">
                                     {{$student_detail->roll_no}}
                                </div>
                            </div>
                        </div>
                        <div class="single-meta">
                            <div class="d-flex justify-content-between">
                                <div class="name">
                                    @lang('common.class')
                                </div>
                                <div class="value">
                                    @if($student_detail->class !="" && $student_detail->session !="")
                                   {{$student_detail->class->class_name}} ({{$student_detail->session->session}})
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="single-meta">
                            <div class="d-flex justify-content-between">
                                <div class="name">
                                    @lang('common.section')
                                </div>
                                <div class="value">
                                    {{$student_detail->section !=""?$student_detail->section->section_name:""}}
                                </div>
                            </div>
                        </div>
                        <div class="single-meta">
                            <div class="d-flex justify-content-between">
                                <div class="name">
                                    @lang('common.gender')
                                </div>
                                <div class="value">
                                    {{$student_detail->gender !=""?$student_detail->gender->base_setup_name:""}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Student Meta Information -->

            </div>
            <div class="col-lg-9">

                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <table class="display school-table school-table-style table_not_fixed" cellspacing="0" width="100%">

                                <thead>
                                    <tr>
                                        <th> @lang('common.name')</th>
                                        <th> @lang('common.email')</th>
                                        <th> @lang('common.phone')</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($teachers as $value)
                                    <tr>
                                        <td>{{$value->teacher !=""?$value->teacher->full_name:""}}</td>
                                        <td>{{$value->teacher !=""?$value->teacher->email:""}}</td>
                                        <td>{{$value->teacher !=""?$value->teacher->mobile:""}}</td>
                                    </tr>
                                    @endforeach
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
