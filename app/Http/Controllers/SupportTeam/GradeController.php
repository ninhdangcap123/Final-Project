<?php

namespace App\Http\Controllers\SupportTeam;

use App\Http\Requests\Grade\GradeCreate;
use App\Http\Requests\Grade\GradeUpdate;
use App\Repositories\Exam\ExamRepositoryInterface;
use App\Repositories\ExamRepo;
use App\Http\Controllers\Controller;
use App\Repositories\Grade\GradeRepositoryInterface;
use App\Repositories\Major\MajorRepositoryInterface;
use App\Repositories\MyCourse\MyCourseRepositoryInterface;
use App\Repositories\MyCourseRepo;

class GradeController extends Controller
{
    protected $exam, $grade, $my_course, $major;

    public function __construct(GradeRepositoryInterface $grade, ExamRepositoryInterface $exam,
                                MajorRepositoryInterface $major, MyCourseRepositoryInterface $my_course)
    {
        $this->exam = $exam;
        $this->my_course = $my_course;
        $this->major = $major;
        $this->grade = $grade;

        $this->middleware('teamSA', ['except' => ['destroy',] ]);
        $this->middleware('super_admin', ['only' => ['destroy',] ]);
    }

    public function index()
    {
         $d['grades'] = $this->grade->getAll();
         $d['majors'] = $this->major->getAll();
        return view('pages.support_team.grades.index', $d);
    }

    public function store(GradeCreate $req)
    {
        $data = $req->all();

        $this->grade->create($data);
        return back()->with('flash_success', __('msg.store_ok'));
    }

    public function edit($id)
    {
        $data['majors'] = $this->major->getAll();
        $data['gr'] = $this->grade->find($id);
        return view('pages.support_team.grades.edit', $data);
    }

    public function update(GradeUpdate $req, $id)
    {
        $data = $req->all();

        $this->grade->update($id, $data);
        return back()->with('flash_success', __('msg.update_ok'));
    }

    public function destroy($id)
    {
        $this->grade->delete($id);
        return back()->with('flash_success', __('msg.del_ok'));
    }
}
