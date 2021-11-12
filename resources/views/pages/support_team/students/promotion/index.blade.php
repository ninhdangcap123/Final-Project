@extends('layouts.master')
@section('page_title', 'Student Promotion')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title font-weight-bold">Student Promotion From <span
                    class="text-danger">{{ $old_year }}</span> TO <span class="text-success">{{ $new_year }}</span>
                                                    Session</h5>
            {!! \App\Helpers\GetSystemInfoHelper::getPanelOptions() !!}
        </div>

        <div class="card-body">
            @include('pages.support_team.students.promotion.selector')
        </div>
    </div>

    @if($selected)
        <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title font-weight-bold">Promote Students From <span
                        class="text-teal">{{ $my_courses->where('id', $fromCourse)->first()->name.' '.$classes->where('id', $fromSection)->first()->name }}</span>
                                                        TO <span
                        class="text-purple">{{ $my_courses->where('id', $toCourse)->first()->name.' '.$classes->where('id', $toSection)->first()->name }}</span>
                </h5>
                {!! \App\Helpers\GetSystemInfoHelper::getPanelOptions() !!}
            </div>

            <div class="card-body">
                @include('pages.support_team.students.promotion.promote')
            </div>
        </div>
    @endif


    {{--Student Promotion End--}}

@endsection
