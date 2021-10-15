<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\jsonHelper;
use App\Helpers\Qs;
use App\Helpers\routeHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dorm\DormCreate;
use App\Http\Requests\Dorm\DormUpdate;
use App\Repositories\DormRepo;

class DormController extends Controller
{
    protected  $dorm;

    public function __construct(DormRepo $dorm)
    {
        $this->middleware('teamSA', ['except' => ['destroy',] ]);
        $this->middleware('super_admin', ['only' => ['destroy',] ]);

        $this->dorm = $dorm;
    }

    public function index()
    {
        $d['dorms'] = $this->dorm->getAll();
        return view('pages.support_team.dorms.index', $d);
    }

    public function store(DormCreate $req)
    {
        $data = $req->only(['name', 'description']);
        $this->dorm->create($data);

        return jsonHelper::jsonStoreOk();
    }

    public function edit($id)
    {
        $d['dorm'] = $dorm = $this->dorm->find($id);

        return !is_null($dorm) ? view('pages.support_team.dorms.edit', $d)
            : routeHelper::goWithDanger('dorms.index');
    }

    public function update(DormUpdate $req, $id)
    {
        $data = $req->only(['name', 'description']);
        $this->dorm->update($id, $data);

        return jsonHelper::jsonUpdateOk();
    }

    public function destroy($id)
    {
        $this->dorm->find($id)->delete();
        return back()->with('flash_success', __('msg.delete_ok'));
    }
}
