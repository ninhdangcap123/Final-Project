<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\JsonHelper;
use App\Helpers\Qs;
use App\Helpers\RouteHelper;
use App\Http\Requests\Subject\SubjectCreate;
use App\Http\Requests\Subject\SubjectUpdate;
use App\Repositories\MyCourseRepo;
use App\Repositories\UserRepo;
use App\Http\Controllers\Controller;

class SubjectController extends Controller
{
    protected $my_course, $user;

    public function __construct(MyCourseRepo $my_course, UserRepo $user)
    {
        $this->middleware('teamSA', ['except' => ['destroy',] ]);
        $this->middleware('super_admin', ['only' => ['destroy',] ]);

        $this->my_course = $my_course;
        $this->user = $user;
    }

    public function index()
    {
        $data['my_courses'] = $this->my_course->all();
        $data['teachers'] = $this->user->getUserByType('teacher');
        $data['subjects'] = $this->my_course->getAllSubjects();

        return view('pages.support_team.subjects.index', $data);
    }

    public function store(SubjectCreate $req)
    {
        $data = $req->all();
        $this->my_course->createSubject($data);

        return JsonHelper::jsonStoreOk();
    }

    public function edit($id)
    {
        $data['s'] = $sub = $this->my_course->findSubject($id);
        $data['my_courses'] = $this->my_course->all();
        $data['teachers'] = $this->user->getUserByType('teacher');

        return is_null($sub) ? RouteHelper::goWithDanger('subjects.index') : view('pages.support_team.subjects.edit', $data);
    }

    public function update(SubjectUpdate $req, $id)
    {
        $data = $req->all();
        $this->my_course->updateSubject($id, $data);

        return JsonHelper::jsonUpdateOk();
    }

    public function destroy($id)
    {
        $this->my_course->deleteSubject($id);
        return back()->with('flash_success', __('msg.del_ok'));
    }
}
