<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\JsonHelper;
use App\Helpers\RouteHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subject\SubjectCreate;
use App\Http\Requests\Subject\SubjectUpdate;
use App\Repositories\MyCourse\MyCourseRepositoryInterface;
use App\Repositories\MyCourseRepo;
use App\Repositories\Subject\SubjectRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\UserRepo;

class SubjectController extends Controller
{
    protected $myCourseRepo;
    protected $userRepo;
    protected $subjectRepo;

    public function __construct(MyCourseRepositoryInterface $myCourseRepo,
                                UserRepositoryInterface     $userRepo,
                                SubjectRepositoryInterface  $subjectRepo)
    {
        $this->middleware('teamSA', [ 'except' => [ 'destroy', ] ]);
        $this->middleware('super_admin', [ 'only' => [ 'destroy', ] ]);
        $this->myCourseRepo = $myCourseRepo;
        $this->userRepo = $userRepo;
        $this->subjectRepo = $subjectRepo;
    }

    public function index()
    {
        $data['my_courses'] = $this->myCourseRepo->getAll();
        $data['teachers'] = $this->userRepo->getUserByType('teacher');
        $data['subjects'] = $this->subjectRepo->getAll();

        return view('pages.support_team.subjects.index', $data);
    }

    public function store(SubjectCreate $request)
    {
        $data = $request->validated();
        $this->subjectRepo->create($data);
        return JsonHelper::jsonStoreSuccess();
    }

    public function edit($id)
    {
        $data['s'] = $subject = $this->subjectRepo->find($id);
        $data['my_courses'] = $this->myCourseRepo->getAll();
        $data['teachers'] = $this->userRepo->getUserByType('teacher');

        return is_null($subject) ? RouteHelper::goWithDanger('subjects.index') : view('pages.support_team.subjects.edit', $data);
    }

    public function update(SubjectUpdate $request, $id)
    {
        $data = $request->validated();
        $this->subjectRepo->update($id, $data);

        return JsonHelper::jsonUpdateSuccess();
    }

    public function destroy($id)
    {
        $this->subjectRepo->delete($id);
        return back()->with('flash_success', __('msg.del_ok'));
    }
}
