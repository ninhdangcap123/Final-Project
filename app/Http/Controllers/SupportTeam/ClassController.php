<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\JsonHelper;
use App\Helpers\RouteHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Classes\ClassCreate;
use App\Http\Requests\Classes\ClassUpdate;
use App\Repositories\Classes\ClassesRepositoryInterface;
use App\Repositories\MyCourse\MyCourseRepositoryInterface;
use App\Repositories\MyCourseRepo;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\UserRepo;

class ClassController extends Controller
{
    protected $myCourseRepo;
    protected $userRepo;
    protected $classRepo;

    public function __construct(
        MyCourseRepositoryInterface $myCourseRepo,
        ClassesRepositoryInterface  $classRepo,
        UserRepositoryInterface     $userRepo
    )
    {
        $this->middleware('teamSA', [ 'except' => [ 'destroy', ] ]);
        $this->middleware('super_admin', [ 'only' => [ 'destroy', ] ]);
        $this->myCourseRepo = $myCourseRepo;
        $this->userRepo = $userRepo;
        $this->classRepo = $classRepo;
    }

    public function index()
    {
        $data['my_courses'] = $this->myCourseRepo->getAll();
        $data['classes'] = $this->classRepo->getAll();
        $data['teachers'] = $this->userRepo->getUserByType('teacher');
        return view('pages.support_team.classes.index', $data);
    }

    public function store(ClassCreate $request)
    {
        $data = $request->validated();
        $this->classRepo->create($data);
        return JsonHelper::jsonStoreSuccess();
    }

    public function edit($id)
    {
        $data['s'] = $class = $this->classRepo->find($id);
        $data['teachers'] = $this->userRepo->getUserByType('teacher');

        return is_null($class) ? RouteHelper::goWithDanger('classes.index') : view('pages.support_team.classes.edit', $data);
    }

    public function update(ClassUpdate $request, $id)
    {
        $data = $request->validated();
        $this->classRepo->update($id, $data);

        return JsonHelper::jsonUpdateSuccess();
    }

    public function destroy($id)
    {
        if( $this->classRepo->isActiveClass($id) ) {
            return back()->with('pop_warning', 'Every class must have a default section, You Cannot Delete It');
        }
        $this->classRepo->delete($id);
        return back()->with('flash_success', __('msg.del_ok'));
    }

}
