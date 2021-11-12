<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\JsonHelper;
use App\Helpers\Qs;
use App\Helpers\RouteHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dorm\DormCreate;
use App\Http\Requests\Dorm\DormUpdate;
use App\Repositories\Dorm\DormRepositoryInterface;
use App\Repositories\DormRepo;

class DormController extends Controller
{
    protected  $dormRepo;

    public function __construct(DormRepositoryInterface $dormRepo)
    {
        $this->middleware('teamSA', ['except' => ['destroy',] ]);
        $this->middleware('super_admin', ['only' => ['destroy',] ]);
        $this->dormRepo = $dormRepo;
    }

    public function index()
    {
        $data['dorms'] = $this->dormRepo->getAll();
        return view('pages.support_team.dorms.index', $data);
    }

    public function store(DormCreate $request)
    {
        $data = $request->only(['name', 'description']);
        $this->dormRepo->create($data);
        return JsonHelper::jsonStoreSuccess();
    }

    public function edit($id)
    {
        $data['dorm'] = $dorm = $this->dormRepo->find($id);

        return !is_null($dorm) ? view('pages.support_team.dorms.edit', $data)
            : RouteHelper::goWithDanger('dorms.index');
    }

    public function update(DormUpdate $request, $id)
    {
        $data = $request->only(['name', 'description']);
        $this->dormRepo->update($id, $data);
        return JsonHelper::jsonUpdateSuccess();
    }

    public function destroy($id)
    {
        $this->dormRepo->find($id)->delete();
        return back()->with('flash_success', __('msg.delete_ok'));
    }
}
