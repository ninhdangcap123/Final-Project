<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\JsonHelper;
use App\Helpers\Qs;
use App\Helpers\RouteHelper;
use App\Http\Requests\Section\SectionCreate;
use App\Http\Requests\Section\SectionUpdate;
use App\Repositories\MyCourseRepo;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepo;

class SectionController extends Controller
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
        $d['my_courses'] = $this->my_course->all();
        $d['sections'] = $this->my_course->getAllSections();
        $d['teachers'] = $this->user->getUserByType('teacher');

        return view('pages.support_team.sections.index', $d);
    }

    public function store(SectionCreate $req)
    {
        $data = $req->all();
        $this->my_course->createSection($data);

        return JsonHelper::jsonStoreOk();
    }

    public function edit($id)
    {
        $d['s'] = $s = $this->my_course->findSection($id);
        $d['teachers'] = $this->user->getUserByType('teacher');

        return is_null($s) ? RouteHelper::goWithDanger('sections.index') :view('pages.support_team.sections.edit', $d);
    }

    public function update(SectionUpdate $req, $id)
    {
        $data = $req->only(['name', 'teacher_id']);
        $this->my_course->updateSection($id, $data);

        return JsonHelper::jsonUpdateOk();
    }

    public function destroy($id)
    {
        if($this->my_course->isActiveSection($id)){
            return back()->with('pop_warning', 'Every class must have a default section, You Cannot Delete It');
        }

        $this->my_course->deleteSection($id);
        return back()->with('flash_success', __('msg.del_ok'));
    }

}
