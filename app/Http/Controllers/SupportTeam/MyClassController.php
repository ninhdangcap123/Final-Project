<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\jsonHelper;
use App\Helpers\Qs;
use App\Helpers\routeHelper;
use App\Http\Requests\MyClass\ClassCreate;
use App\Http\Requests\MyClass\ClassUpdate;
use App\Repositories\MyClassRepo;
use App\Repositories\UserRepo;
use App\Http\Controllers\Controller;

class MyClassController extends Controller
{
    protected $my_class, $user;

    public function __construct(MyClassRepo $my_class, UserRepo $user)
    {
        $this->middleware('teamSA', ['except' => ['destroy',] ]);
        $this->middleware('super_admin', ['only' => ['destroy',] ]);

        $this->my_class = $my_class;
        $this->user = $user;
    }

    public function index()
    {
        $data['my_classes'] = $this->my_class->all();
        $data['majors'] = $this->my_class->getMajor();

        return view('pages.support_team.classes.index', $data);
    }

    public function store(ClassCreate $req): \Illuminate\Http\JsonResponse
    {
        $data = $req->all();
        $myCourse = $this->my_class->create($data);

        // Create Default Section
        $section =
            [
            'my_class_id' => $myCourse->id,
            'name' => 'A',
            'active' => 1,
            'teacher_id' => NULL,
            ];

        $this->my_class->createSection($section);

        return jsonHelper::jsonStoreOk();
    }

    public function edit($id)
    {
        $data['course'] = $course = $this->my_class->find($id);

        return is_null($course) ? routeHelper::goWithDanger('classes.index') :
            view('pages.support_team.classes.edit', $data) ;
    }

    public function update(ClassUpdate $req, $id)
    {
        $data = $req->only(['name']);
        $this->my_class->update($id, $data);

        return jsonHelper::jsonUpdateOk();
    }

    public function destroy($id)
    {
        $this->my_class->delete($id);
        return back()->with('flash_success', __('msg.del_ok'));
    }

}
