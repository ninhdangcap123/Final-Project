<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\GetSystemInfoHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Exam\ExamCreate;
use App\Http\Requests\Exam\ExamUpdate;
use App\Repositories\Exam\ExamRepositoryInterface;
use App\Repositories\ExamRepo;

class ExamController extends Controller
{
    protected $examRepo;

    public function __construct(ExamRepositoryInterface $examRepo)
    {
        $this->middleware('teamSA', [ 'except' => [ 'destroy', ] ]);
        $this->middleware('super_admin', [ 'only' => [ 'destroy', ] ]);
        $this->examRepo = $examRepo;
    }

    public function index()
    {
        $data['exams'] = $this->examRepo->getAll();
        return view('pages.support_team.exams.index', $data);
    }

    public function store(ExamCreate $request)
    {
        $data = $request->only([ 'name', 'term' ]);
        $data['year'] = GetSystemInfoHelper::getSetting('current_session');
        $this->examRepo->create($data);
        return back()->with('flash_success', __('msg.store_ok'));
    }

    public function edit($id)
    {
        $data['ex'] = $this->examRepo->find($id);
        return view('pages.support_team.exams.edit', $data);
    }

    public function update(ExamUpdate $request, $id)
    {
        $data = $request->only([ 'name', 'term' ]);
        $this->examRepo->update($id, $data);
        return back()->with('flash_success', __('msg.update_ok'));
    }

    public function destroy($id)
    {
        $this->examRepo->delete($id);
        return back()->with('flash_success', __('msg.del_ok'));
    }
}
