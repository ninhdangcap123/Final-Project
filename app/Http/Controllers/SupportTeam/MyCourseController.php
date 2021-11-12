<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\JsonHelper;
use App\Helpers\RouteHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\MyCourse\CourseCreate;
use App\Http\Requests\MyCourse\CourseUpdate;
use App\Repositories\Classes\ClassesRepositoryInterface;
use App\Repositories\Major\MajorRepositoryInterface;
use App\Repositories\MyCourse\MyCourseRepositoryInterface;
use App\Repositories\MyCourseRepo;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\UserRepo;

class MyCourseController extends Controller
{
    protected $myCourseRepo;
    protected $userRepo;
    protected $classRepo;
    protected $majorRepo;

    public function __construct(MyCourseRepositoryInterface $myCourseRepo,
                                MajorRepositoryInterface    $majorRepo,
                                ClassesRepositoryInterface  $classRepo,
                                UserRepositoryInterface     $userRepo)
    {
        $this->middleware('teamSA', [ 'except' => [ 'destroy', ] ]);
        $this->middleware('super_admin', [ 'only' => [ 'destroy', ] ]);
        $this->myCourseRepo = $myCourseRepo;
        $this->userRepo = $userRepo;
        $this->majorRepo = $majorRepo;
        $this->classRepo = $classRepo;
    }

    public function index()
    {
        $data['my_courses'] = $this->myCourseRepo->getAll();
        $data['majors'] = $this->majorRepo->getAll();
        return view('pages.support_team.courses.index', $data);
    }

    public function store(CourseCreate $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validated();
        $myCourse = $this->myCourseRepo->create($data);

        // Create Default Classes
        $section =
            [
                'my_course_id' => $myCourse->id,
                'name' => 'A',
                'active' => 1,
                'teacher_id' => NULL,
            ];

        $this->classRepo->create($section);

        return JsonHelper::jsonStoreSuccess();
    }

    public function edit($id)
    {
        $data['course'] = $course = $this->myCourseRepo->find($id);

        return is_null($course) ? RouteHelper::goWithDanger('courses.index') :
            view('pages.support_team.courses.edit', $data);
    }

    public function update(CourseUpdate $request, $id)
    {
        $data = $request->only([ 'name' ]);
        $this->myCourseRepo->update($id, $data);

        return JsonHelper::jsonUpdateSuccess();
    }

    public function destroy($id)
    {
        $this->myCourseRepo->delete($id);
        return back()->with('flash_success', __('msg.del_ok'));
    }

}
