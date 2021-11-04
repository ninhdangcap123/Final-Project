@extends('layouts.master')
@section('page_title', 'Edit Subject - '.$s->name. ' ('.$s->myCourse->name.')')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Edit Subject - {{$s->myCourse->name }}</h6>
            {!! \App\Helpers\GetSystemInfoHelper::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <form class="ajax-update-h" method="post" action="{{ route('subjects.update', $s->id) }}">
                        @csrf @method('PUT')
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">Name <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input name="name" value="{{ $s->name }}" required type="text" class="form-control" placeholder="Name of Subject">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">Short Name</label>
                            <div class="col-lg-9">
                                <input name="slug" value="{{ $s->slug }}"  type="text" class="form-control" placeholder="Short Name">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="my_course_id" class="col-lg-3 col-form-label font-weight-semibold">Class <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <select required data-placeholder="Select Class" class="form-control select" name="my_course_id" id="my_course_id">
                                    @foreach($my_courses as $c)
                                        <option {{ $s->my_course_id == $c->id ? 'selected' : '' }} value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="teacher_id" class="col-lg-3 col-form-label font-weight-semibold">Teacher</label>
                            <div class="col-lg-9">
                                <select data-placeholder="Select Teacher" class="form-control select-search" name="teacher_id" id="teacher_id">
                                    <option value=""></option>
                                    @foreach($teachers as $t)
                                        <option {{ $s->teacher_id == $t->id ? 'selected' : '' }} value="{{ \App\Helpers\DisplayMessageHelper::hash($t->id) }}">{{ $t->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">Submit form <i class="icon-paperplane ml-2"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{--subject Edit Ends--}}

@endsection
