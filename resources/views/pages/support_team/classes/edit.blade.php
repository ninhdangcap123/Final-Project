@extends('layouts.master')
@section('page_title', 'Edit Classes of '.$s->myCourse->name)
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Edit Classes of {{ $s->myCourse->name }}</h6>
            {!! \App\Helpers\GetSystemInfoHelper::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <form class="ajax-update" method="post" action="{{ route('classes.update', $s->id) }}">
                        @csrf @method('PUT')
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label font-weight-semibold">Name <span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input name="name" value="{{ $s->name }}" required type="text" class="form-control"
                                       placeholder="Name of Class">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="my_class_id"
                                   class="col-lg-3 col-form-label font-weight-semibold">Course </label>
                            <div class="col-lg-9">
                                <input class="form-control" id="my_class_id" disabled="disabled" type="text"
                                       value="{{ $s->myCourse->name }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="teacher_id" class="col-lg-3 col-form-label font-weight-semibold">Teacher</label>
                            <div class="col-lg-9">
                                <select data-placeholder="Select Teacher" class="form-control select-search"
                                        name="teacher_id" id="teacher_id">
                                    <option value=""></option>
                                    @foreach($teachers as $t)
                                        <option
                                            {{ $s->teacher_id == $t->id ? 'selected' : '' }} value="{{ \App\Helpers\DisplayMessageHelper::hash($t->id) }}">{{ $t->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">Submit form <i
                                    class="icon-paperplane ml-2"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{--Classes Edit Ends--}}

@endsection
