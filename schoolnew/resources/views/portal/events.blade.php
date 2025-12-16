@extends('layouts.portal')

@section('title', 'Events')
@section('page-title', 'Events Calendar')

@section('breadcrumb')
    <li class="breadcrumb-item active">Events</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Month Navigation -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        @php
                            $prevMonth = $month == 1 ? 12 : $month - 1;
                            $prevYear = $month == 1 ? $year - 1 : $year;
                            $nextMonth = $month == 12 ? 1 : $month + 1;
                            $nextYear = $month == 12 ? $year + 1 : $year;
                        @endphp
                        <a href="{{ route('portal.events', ['month' => $prevMonth, 'year' => $prevYear]) }}" class="btn btn-outline-primary">
                            <i class="fa fa-chevron-left"></i> Previous
                        </a>
                        <h4 class="mb-0">{{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}</h4>
                        <a href="{{ route('portal.events', ['month' => $nextMonth, 'year' => $nextYear]) }}" class="btn btn-outline-primary">
                            Next <i class="fa fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Calendar -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>Sun</th>
                                    <th>Mon</th>
                                    <th>Tue</th>
                                    <th>Wed</th>
                                    <th>Thu</th>
                                    <th>Fri</th>
                                    <th>Sat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($calendarData as $week)
                                    <tr>
                                        @foreach($week as $day)
                                            <td class="{{ !$day['inMonth'] ? 'text-muted bg-light' : '' }} {{ $day['isToday'] ? 'border-primary border-2' : '' }}" style="height: 100px; vertical-align: top;">
                                                @if($day['inMonth'])
                                                    <div class="fw-bold mb-1">{{ $day['day'] }}</div>
                                                    @foreach($day['events']->take(2) as $event)
                                                        <a href="{{ route('portal.events.show', $event) }}" class="badge d-block mb-1 text-truncate" style="background-color: {{ $event->color }}; color: white;">
                                                            {{ Str::limit($event->title, 15) }}
                                                        </a>
                                                    @endforeach
                                                    @if($day['events']->count() > 2)
                                                        <small class="text-muted">+{{ $day['events']->count() - 2 }} more</small>
                                                    @endif
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Events -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Upcoming Events</h5>
                </div>
                <div class="card-body">
                    @if($upcomingEvents->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($upcomingEvents as $event)
                                <li class="list-group-item px-0">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3 text-center" style="min-width: 50px;">
                                            <div class="bg-light rounded p-2">
                                                <div class="fw-bold text-primary">{{ $event->start_date->format('d') }}</div>
                                                <div class="small text-muted">{{ $event->start_date->format('M') }}</div>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">
                                                <a href="{{ route('portal.events.show', $event) }}" class="text-dark">{{ $event->title }}</a>
                                            </h6>
                                            <small class="text-muted">
                                                <span class="badge" style="background-color: {{ $event->color }}; color: white;">{{ $event->getTypeLabel() }}</span>
                                                @if($event->venue)
                                                    <span class="ms-2"><i class="fa fa-map-marker"></i> {{ $event->venue }}</span>
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted mb-0">No upcoming events</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
