@extends('layouts.master')
@section('page_title', 'Manage Class Sections')
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Manage Courses Classes</h6>
            {!! \App\Helpers\GetSystemInfoHelper::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#new-section" class="nav-link active" data-toggle="tab">Create New
                                                                                                      Classes</a></li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Manage Classes</a>
                    <div class="dropdown-menu dropdown-menu-right">
                        @foreach($my_courses as $c)
                            <a href="#c{{ $c->id }}" class="dropdown-item" data-toggle="tab">{{ $c->name }}</a>
                        @endforeach
                    </div>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane show  active fade" id="new-section">
                    <div class="row">
                        <div class="col-md-6">
                            <form class="ajax-store" method="post" action="{{ route('classes.store') }}">
                                @csrf
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label font-weight-semibold">Name <span
                                            class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <input name="name" value="{{ old('name') }}" required type="text"
                                               class="form-control" placeholder="Name of Classes">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="my_course_id" class="col-lg-3 col-form-label font-weight-semibold">Select
                                                                                                                   Class
                                        <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <select required data-placeholder="Select Class" class="form-control select"
                                                name="my_course_id" id="my_course_id">
                                            @foreach($my_courses as $c)
                                                <option
                                                    {{ old('my_course_id') == $c->id ? 'selected' : '' }} value="{{ $c->id }}">{{ $c->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="teacher_id"
                                           class="col-lg-3 col-form-label font-weight-semibold">Teacher</label>
                                    <div class="col-lg-9">
                                        <select data-placeholder="Select Teacher" class="form-control select-search"
                                                name="teacher_id" id="teacher_id">
                                            <option value=""></option>
                                            @foreach($teachers as $t)
                                                <option
                                                    {{ old('teacher_id') == \App\Helpers\DisplayMessageHelper::hash($t->id) ? 'selected' : '' }} value="{{ \App\Helpers\DisplayMessageHelper::hash($t->id) }}">{{ $t->name }}</option>
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

                @foreach($my_courses as $d)
                    <div class="tab-pane fade" id="c{{ $d->id }}">
                        <table class="table datatable-button-html5-columns">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Name</th>
                                <th>Class</th>
                                <th>Teacher</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($classes->where('my_course.id', $d->id) as $s)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $s->name }} @if($s->active)<i class='icon-check'> </i>@endif</td>
                                    <td>{{ $s->myCourse->name }}</td>

                                    @if($s->teacher_id)
                                        <td><a target="_blank"
                                               href="{{ route('users.show', \App\Helpers\DisplayMessageHelper::hash($s->teacher_id)) }}">{{ $s->teacher->name }}</a>
                                        </td>
                                    @else
                                        <td> -</td>
                                    @endif

                                    <td class="text-center">
                                        <div class="list-icons">
                                            <div class="dropdown">
                                                <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                    <i class="icon-menu9"></i>
                                                </a>

                                                <div class="dropdown-menu dropdown-menu-left">
                                                    {{--edit--}}
                                                    @if(\App\Helpers\CheckUsersHelper::userIsTeamSA())
                                                        <a href="{{ route('classes.edit', $s->id) }}"
                                                           class="dropdown-item"><i class="icon-pencil"></i> Edit</a>
                                                    @endif
                                                    {{--Delete--}}
                                                    @if(\App\Helpers\GetUserTypeHelper::userIsSuperAdmin())
                                                        <a id="{{ $s->id }}" onclick="confirmDelete(this.id)" href="#"
                                                           class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                                        <form method="post" id="item-delete-{{ $s->id }}"
                                                              action="{{ route('classes.destroy', $s->id) }}"
                                                              class="hidden">@csrf @method('delete')</form>
                                                    @endif

                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach

            </div>
        </div>
    </div>

    {{--Classes List Ends--}}

@endsection
