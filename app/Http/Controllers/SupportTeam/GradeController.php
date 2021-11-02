<?php

namespace App\Http\Controllers\SupportTeam;

use App\Http\Requests\Grade\GradeCreate;
use App\Http\Requests\Grade\GradeUpdate;
use App\Repositories\ExamRepo;
use App\Http\Controllers\Controller;
use App\Repositories\MyCourseRepo;

class GradeController extends Controller
{
    protected $exam, $my_course;

    public function __construct(ExamRepo $exam, MyCourseRepo $my_course)
    {
        $this->exam = $exam;
        $this->my_course = $my_course;

        $this->middleware('teamSA', ['except' => ['destroy',] ]);
        $this->middleware('super_admin', ['only' => ['destroy',] ]);
    }

    public function index()
    {
         $d['grades'] = $this->exam->allGrades();
         $d['majors'] = $this->my_course->getMajor();
        return view('pages.support_team.grades.index', $d);
    }

    public function store(GradeCreate $req)
    {
        $data = $req->all();

        $this->exam->createGrade($data);
        return back()->with('flash_success', __('msg.store_ok'));
    }

    public function edit($id)
    {
        $d['majors'] = $this->my_course->getMajor();
        $d['gr'] = $this->exam->findGrade($id);
        return view('pages.support_team.grades.edit', $d);
    }

    public function update(GradeUpdate $req, $id)
    {
        $data = $req->all();

        $this->exam->updateGrade($id, $data);
        return back()->with('flash_success', __('msg.update_ok'));
    }

    public function destroy($id)
    {
        $this->exam->deleteGrade($id);
        return back()->with('flash_success', __('msg.del_ok'));
    }
}
