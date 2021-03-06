@extends('layouts.master')
@section('page_title', 'Tabulation Sheet')
@section('content')
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title"><i class="icon-books mr-2"></i> Tabulation Sheet</h5>
            {!! \App\Helpers\GetSystemInfoHelper::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <form method="post" action="{{ route('marks.tabulation_select') }}">
                @csrf
                <div class="row">

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="exam_id" class="col-form-label font-weight-bold">Exam:</label>
                            <select required id="exam_id" name="exam_id" class="form-control select"
                                    data-placeholder="Select Exam">
                                @foreach($exams as $exm)
                                    <option
                                        {{ ($selected && $exam_id == $exm->id) ? 'selected' : '' }} value="{{ $exm->id }}">{{ $exm->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="my_course_id" class="col-form-label font-weight-bold">Course:</label>
                            <select onchange="getClassSections(this.value)" required id="my_course_id"
                                    name="my_course_id" class="form-control select" data-placeholder="Select Course">
                                <option value=""></option>
                                @foreach($my_courses as $c)
                                    <option
                                        {{ ($selected && $my_course_id == $c->id) ? 'selected' : '' }} value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="section_id" class="col-form-label font-weight-bold">Classes:</label>
                            <select required id="section_id" name="class_id" data-placeholder="Select Course First"
                                    class="form-control select">
                                @if($selected)
                                    @foreach($classes->where('my_course_id', $my_course_id) as $s)
                                        <option
                                            {{ $class_id == $s->id ? 'selected' : '' }} value="{{ $s->id }}">{{ $s->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>


                    <div class="col-md-2 mt-4">
                        <div class="text-right mt-1">
                            <button type="submit" class="btn btn-primary">View Sheet <i
                                    class="icon-paperplane ml-2"></i></button>
                        </div>
                    </div>

                </div>

            </form>
        </div>
    </div>

    {{--if Selction Has Been Made --}}

    @if($selected)
        <div class="card">
            <div class="card-header">
                <h6 class="card-title font-weight-bold">Tabulation Sheet
                                                        for {{ $my_course->name.' '.$class->name.' - '.$ex->name.' ('.$year.')' }}</h6>
            </div>
            <div class="card-body">
                <table class="table table-responsive table-striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>NAMES_OF_STUDENTS_IN_CLASS</th>
                        @foreach($subjects as $sub)
                            <th title="{{ $sub->name }}" rowspan="2">{{ strtoupper($sub->slug ?: $sub->name) }}</th>
                        @endforeach

                        <th style="color: darkred">Total</th>
                        <th style="color: darkblue">Average</th>
                        <th style="color: darkgreen">Position</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($students as $s)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td style="text-align: center">{{ $s->user->name }}</td>
                            @foreach($subjects as $sub)
                                <td>{{ $marks->where('student_id', $s->user_id)->where('subject_id', $sub->id)->first()->$tex ?? '-' ?: '-' }}</td>
                            @endforeach


                            <td style="color: darkred">{{ $exr->where('student_id', $s->user_id)->first()->total ?: '-' }}</td>
                            <td style="color: darkblue">{{ $exr->where('student_id', $s->user_id)->first()->ave ?: '-' }}</td>
                            <td style="color: darkgreen">{!! \App\Helpers\PrintMarkSheetHelper::getSuffix($exr->where('student_id', $s->user_id)->first()->pos) ?: '-' !!}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{--Print Button--}}
                <div class="text-center mt-4">
                    <a target="_blank"
                       href="{{  route('marks.print_tabulation', [$exam_id, $my_course_id, $class_id]) }}"
                       class="btn btn-danger btn-lg"><i class="icon-printer mr-2"></i> Print Tabulation Sheet</a>
                </div>
            </div>
        </div>
    @endif
@endsection
