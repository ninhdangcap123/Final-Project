<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\JsonHelper;
use App\Helpers\Qs;
use App\Helpers\RouteHelper;
use App\Http\Requests\Classes\ClassCreate;
use App\Http\Requests\Classes\ClassUpdate;
use App\Repositories\Classes\ClassesRepositoryInterface;
use App\Repositories\MyCourse\MyCourseRepositoryInterface;
use App\Repositories\MyCourseRepo;
use App\Http\Controllers\Controller;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\UserRepo;

class ClassController extends Controller
{
    protected $my_course, $user, $class;

    public function __construct(MyCourseRepositoryInterface $my_course, ClassesRepositoryInterface $class, UserRepositoryInterface $user)
    {
        $this->middleware('teamSA', ['except' => ['destroy',] ]);
        $this->middleware('super_admin', ['only' => ['destroy',] ]);

        $this->my_course = $my_course;
        $this->user = $user;
        $this->class = $class;
    }

    public function index()
    {
        $d['my_courses'] = $this->my_course->getAll();
        $d['classes'] = $this->class->getAll();
        $d['teachers'] = $this->user->getUserByType('teacher');

        return view('pages.support_team.classes.index', $d);
    }

    public function store(ClassCreate $req)
    {
        $data = $req->all();
        $this->class->create($data);

        return JsonHelper::jsonStoreOk();
    }

    public function edit($id)
    {
        $d['s'] = $s = $this->class->find($id);
        $d['teachers'] = $this->user->getUserByType('teacher');

        return is_null($s) ? RouteHelper::goWithDanger('classes.index') :view('pages.support_team.classes.edit', $d);
    }

    public function update(ClassUpdate $req, $id)
    {
        $data = $req->only(['name', 'teacher_id']);
        $this->class->update($id, $data);

        return JsonHelper::jsonUpdateOk();
    }

    public function destroy($id)
    {
        if($this->class->isActiveClass($id)){
            return back()->with('pop_warning', 'Every class must have a default section, You Cannot Delete It');
        }

        $this->class->delete($id);
        return back()->with('flash_success', __('msg.del_ok'));
    }

}
