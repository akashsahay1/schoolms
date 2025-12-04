@extends('backEnd.master')
@section('title') 
@lang('academics.class_routine')
@endsection
@section('mainContent')
    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('academics.class_routine')</h1>
                <div class="bc-pages">
                    <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                    <a href="{{route('parent_class_routine', [$student_detail->id])}}">@lang('academics.class_routine')</a>
                </div>
            </div>
        </div>
    </section>
    <section class="mt-20">
        <div class="container-fluid p-0">
            <div class="row mt-40">
                <div class="col-lg-6 col-md-6">
                    <div class="main-title">
                        <h3 class="mb-30">@lang('student.student_information')</h3>
                    </div>
                </div>
                <div class="col-lg-6 pull-right mb-20">
                    <a href="{{route('classRoutinePrint',  [$student_detail->class_id, $student_detail->section_id])}}"
                       class="primary-btn small fix-gr-bg pull-right" target="_blank"><i
                                class="ti-printer"> </i> @lang('common.print')</a>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 mb-30">
                    <!-- Start Student Meta Information -->
                    <div class="student-meta-box ">
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
                                        @lang('common.roll_number')
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
                                            {{$student_detail->class->class_name}}
                                            ({{$student_detail->session->session}})
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
                    <table id="default_table2" class="display school-table " cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            @php
                            $height= 0;
                            $tr = [];
                        @endphp
                        @foreach($sm_weekends as $sm_weekend)
                                @php
                                    $parentRoutine=App\SmWeekend::parentClassRoutine($sm_weekend->id,$student_detail->id);
                                @endphp
                            @if( $parentRoutine->count() > $height)
                                @php
                                    $height =  $parentRoutine->count();
                                @endphp
                            @endif
            
                            <th>{{@$sm_weekend->name}}</th>
                        @endforeach
                        </tr>
                        </thead>
                        <tbody>
                                
                        @php
                        $used = [];
                        $tr=[];
            
                    @endphp
                   
                    @foreach($sm_weekends as $sm_weekend)
                    @php
                      $parentRoutine=App\SmWeekend::parentClassRoutine($sm_weekend->id,$student_detail->id);
                        $i = 0;
                    @endphp
                        @foreach($parentRoutine as $routine)
                            @php
                            if(!in_array($routine->id, $used)){
                                $tr[$i][$sm_weekend->name][$loop->index]['subject']= $routine->subject ? $routine->subject->subject_name :'';
                                $tr[$i][$sm_weekend->name][$loop->index]['subject_code']= $routine->subject ? $routine->subject->subject_code :'';
                                $tr[$i][$sm_weekend->name][$loop->index]['class_room']= $routine->classRoom ? $routine->classRoom->room_no : '';
                                $tr[$i][$sm_weekend->name][$loop->index]['teacher']= $routine->teacherDetail ? $routine->teacherDetail->full_name :'';
                                $tr[$i][$sm_weekend->name][$loop->index]['start_time']=  $routine->start_time;
                                $tr[$i][$sm_weekend->name][$loop->index]['end_time']= $routine->end_time;
                                $tr[$i][$sm_weekend->name][$loop->index]['is_break']= $routine->is_break;
                                $used[] = $routine->id;
                            } 
                                 
                            @endphp
                        @endforeach
            
                        @php
                            
                            $i++;
                        @endphp
            
                    @endforeach
            
                   @for($i = 0; $i < $height; $i++)
                   <tr>
                    @foreach($tr as $days)
                     @foreach($sm_weekends as $sm_weekend)
                        <td>
                            @php
                                 $classes=gv($days,$sm_weekend->name);
                             @endphp
                             @if($classes && gv($classes,$i))              
                               @if($classes[$i]['is_break'])
                              <strong > @lang('academics.break') </strong>
                                 
                               <span class=""> ({{date('h:i A', strtotime(@$classes[$i]['start_time']))  }}  - {{date('h:i A', strtotime(@$classes[$i]['end_time']))  }})  <br> </span> 
                                @else
                                <span class="">  {{date('h:i A', strtotime(@$classes[$i]['start_time']))  }}  - {{date('h:i A', strtotime(@$classes[$i]['end_time']))  }}  <br> </span> 
                                    <span class=""> <strong>   {{ $classes[$i]['subject'] }} </strong> ({{ $classes[$i]['subject_code'] }}) <br>  </span>            
                                    @if ($classes[$i]['class_room'])
                                        <span class=""> <strong>@lang('academics.room') :</strong>     {{ $classes[$i]['class_room'] }}  <br>     </span>
                                    @endif    
                                    @if ($classes[$i]['teacher'])
                                    <span class=""> {{ $classes[$i]['teacher'] }}  <br> </span>
                                    @endif           
                
                                
                                 @endif
            
                            @endif
                            
                        </td>
                        @endforeach
            
              
                                
                    @endforeach
                   </tr>
            
                   @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>


@endsection
