<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\JsonHelper;
use App\Helpers\Qs;
use App\Helpers\RouteHelper;
use App\Http\Requests\Subject\SubjectCreate;
use App\Http\Requests\Subject\SubjectUpdate;
use App\Repositories\MyCourse\MyCourseRepositoryInterface;
use App\Repositories\MyCourseRepo;
use App\Repositories\Subject\SubjectRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\UserRepo;
use App\Http\Controllers\Controller;

class SubjectController extends Controller
{
    protected $my_course, $user, $subject;

    public function __construct(MyCourseRepositoryInterface $my_course, UserRepositoryInterface $user, SubjectRepositoryInterface $subject)
    {
        $this->middleware('teamSA', ['except' => ['destroy',] ]);
        $this->middleware('super_admin', ['only' => ['destroy',] ]);

        $this->my_course = $my_course;
        $this->user = $user;
        $this->subject = $subject;
    }

    public function index()
    {
        $data['my_courses'] = $this->my_course->getAll();
        $data['teachers'] = $this->user->getUserByType('teacher');
        $data['subjects'] = $this->subject->getAll();

        return view('pages.support_team.subjects.index', $data);
    }

    public function store(SubjectCreate $req)
    {
        $data = $req->all();
        $this->subject->create($data);

        return JsonHelper::jsonStoreOk();
    }

    public function edit($id)
    {
        $data['s'] = $sub = $this->subject->find($id);
        $data['my_courses'] = $this->my_course->getAll();
        $data['teachers'] = $this->user->getUserByType('teacher');

        return is_null($sub) ? RouteHelper::goWithDanger('subjects.index') : view('pages.support_team.subjects.edit', $data);
    }

    public function update(SubjectUpdate $req, $id)
    {
        $data = $req->all();
        $this->subject->update($id, $data);

        return JsonHelper::jsonUpdateOk();
    }

    public function destroy($id)
    {
        $this->subject->delete($id);
        return back()->with('flash_success', __('msg.del_ok'));
    }
}
