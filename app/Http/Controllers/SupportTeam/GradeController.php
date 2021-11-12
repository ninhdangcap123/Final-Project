<?php

namespace App\Http\Controllers\SupportTeam;

use App\Http\Controllers\Controller;
use App\Http\Requests\Grade\GradeCreate;
use App\Http\Requests\Grade\GradeUpdate;
use App\Repositories\Exam\ExamRepositoryInterface;
use App\Repositories\ExamRepo;
use App\Repositories\Grade\GradeRepositoryInterface;
use App\Repositories\Major\MajorRepositoryInterface;
use App\Repositories\MyCourse\MyCourseRepositoryInterface;
use App\Repositories\MyCourseRepo;

class GradeController extends Controller
{
    protected $examRepo;
    protected $gradeRepo;
    protected $myCourseRepo;
    protected $majorRepo;

    public function __construct(GradeRepositoryInterface    $gradeRepo,
                                ExamRepositoryInterface     $examRepo,
                                MajorRepositoryInterface    $majorRepo,
                                MyCourseRepositoryInterface $myCourseRepo)
    {
        $this->examRepo = $examRepo;
        $this->myCourseRepo = $myCourseRepo;
        $this->majorRepo = $majorRepo;
        $this->gradeRepo = $gradeRepo;
        $this->middleware('teamSA', [ 'except' => [ 'destroy', ] ]);
        $this->middleware('super_admin', [ 'only' => [ 'destroy', ] ]);
    }

    public function index()
    {
        $data['grades'] = $this->gradeRepo->getAll();
        $data['majors'] = $this->majorRepo->getAll();
        return view('pages.support_team.grades.index', $data);
    }

    public function store(GradeCreate $request)
    {
        $data = $request->validated();
        $this->gradeRepo->create($data);
        return back()->with('flash_success', __('msg.store_ok'));
    }

    public function edit($id)
    {
        $data['majors'] = $this->majorRepo->getAll();
        $data['gr'] = $this->gradeRepo->find($id);
        return view('pages.support_team.grades.edit', $data);
    }

    public function update(GradeUpdate $request, $id)
    {
        $data = $request->validated();
        $this->gradeRepo->update($id, $data);
        return back()->with('flash_success', __('msg.update_ok'));
    }

    public function destroy($id)
    {
        $this->gradeRepo->delete($id);
        return back()->with('flash_success', __('msg.del_ok'));
    }
}
