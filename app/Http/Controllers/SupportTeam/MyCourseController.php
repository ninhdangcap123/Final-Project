<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\JsonHelper;
use App\Helpers\Qs;
use App\Helpers\RouteHelper;
use App\Http\Requests\MyCourse\CourseCreate;
use App\Http\Requests\MyCourse\CourseUpdate;
use App\Repositories\MyCourseRepo;
use App\Repositories\UserRepo;
use App\Http\Controllers\Controller;

class MyCourseController extends Controller
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
        $data['majors'] = $this->my_course->getMajor();

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

        $this->my_course->createSection($section);

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
