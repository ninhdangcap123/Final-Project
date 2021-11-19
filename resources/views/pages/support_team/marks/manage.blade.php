@extends('layouts.master')
@section('page_title', 'Manage Marks')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title font-weight-bold">Fill The Form To Manage Marks</h6>
            {!! \App\Helpers\GetSystemInfoHelper::getPanelOptions() !!}
        </div>

        <div class="card-body">
            @include('pages.support_team.marks.selector')
        </div>
    </div>

    <div class="card">

        <div class="card-header">
            <div class="row">
                <div class="col-md-4"><h6 class="card-title">
                        <strong>Subject: </strong> {{ $m->subject->name }}</h6>
                </div>

                <div class="col-md-4"><h6 class="card-title">
                        <strong>Class: </strong> {{ $m->myCourse->name.' '.$m->classes->name }}</h6></div>

                <div class="col-md-4"><h6 class="card-title">
                        <strong>Exam: </strong> {{ $m->exam->name.' - '.$m->year }}
                    </h6></div>
            </div>
        </div>

        <div class="card-body">
            @include('pages.support_team.marks.edit')
            {{--@include('pages.support_team.marks.random')--}}
        </div>
    </div>

    {{--Marks Manage End--}}

@endsection
