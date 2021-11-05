<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\JsonHelper;
use App\Helpers\Qs;
use App\Helpers\RouteHelper;
use App\Http\Requests\MyCourse\CourseCreate;
use App\Http\Requests\MyCourse\CourseUpdate;
use App\Repositories\Classes\ClassesRepositoryInterface;
use App\Repositories\Major\MajorRepositoryInterface;
use App\Repositories\MyCourse\MyCourseRepositoryInterface;
use App\Repositories\MyCourseRepo;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\UserRepo;
use App\Http\Controllers\Controller;

class MyCourseController extends Controller
{
    protected $my_course, $user, $class, $major;

    public function __construct(MyCourseRepositoryInterface $my_course, MajorRepositoryInterface $major, ClassesRepositoryInterface $class,
                                UserRepositoryInterface $user)
    {
        $this->middleware('teamSA', ['except' => ['destroy',] ]);
        $this->middleware('super_admin', ['only' => ['destroy',] ]);

        $this->my_course = $my_course;
        $this->user = $user;
        $this->major = $major;
        $this->class = $class;
    }

    public function index()
    {
        $data['my_courses'] = $this->my_course->getAll();
        $data['majors'] = $this->major->getAll();

        return view('pages.support_team.courses.index', $data);
    }

    public function store(CourseCreate $req): \Illuminate\Http\JsonResponse
    {
        $data = $req->all();
        $my_course = $this->my_course->create($data);

        // Create Default Classes
        $section =
            [
            'my_course_id' => $my_course->id,
            'name' => 'A',
            'active' => 1,
            'teacher_id' => NULL,
            ];

        $this->class->create($section);

        return JsonHelper::jsonStoreOk();
    }

    public function edit($id)
    {
        $data['course'] = $course = $this->my_course->find($id);

        return is_null($course) ? RouteHelper::goWithDanger('courses.index') :
            view('pages.support_team.courses.edit', $data) ;
    }

    public function update(CourseUpdate $req, $id)
    {
        $data = $req->only(['name']);
        $this->my_course->update($id, $data);

        return JsonHelper::jsonUpdateOk();
    }

    public function destroy($id)
    {
        $this->my_course->delete($id);
        return back()->with('flash_success', __('msg.del_ok'));
    }

}
