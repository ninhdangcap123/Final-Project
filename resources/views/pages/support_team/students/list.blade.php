@extends('layouts.master')
@section('page_title', 'Student Information - '.$my_class->name)
@section('content')

    <div class="card">
        <div class="card-header header-elements-inline">
            <h6 class="card-title">Students List</h6>
            {!! \App\Helpers\getSystemInfoHelper::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#all-students" class="nav-link active" data-toggle="tab">All {{ $my_class->name }} Students</a></li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Classes</a>
                    <div class="dropdown-menu dropdown-menu-right">
                        @foreach($sections as $s)
                            <a href="#s{{ $s->id }}" class="dropdown-item" data-toggle="tab">{{ $my_class->name.' '.$s->name }}</a>
                        @endforeach
                    </div>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="all-students">
                    <table class="table datatable-button-html5-columns">
                        <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>ADM_No</th>
                            <th>Class</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($students as $s)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><img class="rounded-circle" style="height: 40px; width: 40px;" src="{{ $s->user->photo }}" alt="photo"></td>
                                <td>{{ $s->user->name }}</td>
                                <td>{{ $s->adm_no }}</td>
                                <td>{{ $s->section->name }}</td>
                                <td>{{ $s->user->email }}</td>
                                <td class="text-center">
                                    <div class="list-icons">
                                        <div class="dropdown">
                                            <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                <i class="icon-menu9"></i>
                                            </a>

                                            <div class="dropdown-menu dropdown-menu-left">
                                                <a href="{{ route('students.show', \App\Helpers\displayMessageHelper::hash($s->id)) }}" class="dropdown-item"><i class="icon-eye"></i> View Profile</a>
                                                @if(\App\Helpers\checkUsersHelper::userIsTeamSA())
                                                    <a href="{{ route('students.edit', \App\Helpers\displayMessageHelper::hash($s->id)) }}" class="dropdown-item"><i class="icon-pencil"></i> Edit</a>
                                                    <a href="{{ route('st.reset_pass', \App\Helpers\displayMessageHelper::hash($s->user->id)) }}" class="dropdown-item"><i class="icon-lock"></i> Reset password</a>
                                                @endif
                                                <a target="_blank" href="{{ route('marks.year_selector', \App\Helpers\displayMessageHelper::hash($s->user->id)) }}" class="dropdown-item"><i class="icon-check"></i> Marksheet</a>

                                                {{--Delete--}}
                                                @if(\App\Helpers\getUserTypeHelper::userIsSuperAdmin())
                                                    <a id="{{ \App\Helpers\displayMessageHelper::hash($s->user->id) }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                                    <form method="post" id="item-delete-{{ \App\Helpers\displayMessageHelper::hash($s->user->id) }}" action="{{ route('students.destroy', \App\Helpers\displayMessageHelper::hash($s->user->id)) }}" class="hidden">@csrf @method('delete')</form>
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

                @foreach($sections as $se)
                    <div class="tab-pane fade" id="s{{$se->id}}">                         <table class="table datatable-button-html5-columns">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>ADM_No</th>
                                <th>Email</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($students->where('section_id', $se->id) as $sr)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><img class="rounded-circle" style="height: 40px; width: 40px;" src="{{ $sr->user->photo }}" alt="photo"></td>
                                    <td>{{ $sr->user->name }}</td>
                                    <td>{{ $sr->adm_no }}</td>
                                    <td>{{ $sr->user->email }}</td>
                                    <td class="text-center">
                                        <div class="list-icons">
                                            <div class="dropdown">
                                                <a href="#" class="list-icons-item" data-toggle="dropdown">
                                                    <i class="icon-menu9"></i>
                                                </a>

                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a href="{{ route('students.show', \App\Helpers\displayMessageHelper::hash($sr->id)) }}" class="dropdown-item"><i class="icon-eye"></i> View Info</a>
                                                    @if(\App\Helpers\checkUsersHelper::userIsTeamSA())
                                                        <a href="{{ route('students.edit', \App\Helpers\displayMessageHelper::hash($sr->id)) }}" class="dropdown-item"><i class="icon-pencil"></i> Edit</a>
                                                        <a href="{{ route('st.reset_pass', \App\Helpers\displayMessageHelper::hash($sr->user->id)) }}" class="dropdown-item"><i class="icon-lock"></i> Reset password</a>
                                                    @endif
                                                    <a href="#" class="dropdown-item"><i class="icon-check"></i> Marksheet</a>

                                                    {{--Delete--}}
                                                    @if(\App\Helpers\getUserTypeHelper::userIsSuperAdmin())
                                                        <a id="{{ \App\Helpers\displayMessageHelper::hash($sr->user->id) }}" onclick="confirmDelete(this.id)" href="#" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                                        <form method="post" id="item-delete-{{ \App\Helpers\displayMessageHelper::hash($sr->user->id) }}" action="{{ route('students.destroy', \App\Helpers\displayMessageHelper::hash($sr->user->id)) }}" class="hidden">@csrf @method('delete')</form>
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

    {{--Student List Ends--}}

@endsection
