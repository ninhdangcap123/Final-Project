<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\checkUsersHelper;
use App\Helpers\displayMessageHelper;
use App\Helpers\getPathHelper;
use App\Helpers\getSystemInfoHelper;
use App\Helpers\getUsersHelper;
use App\Helpers\jsonHelper;
use App\Helpers\printMarkSheetHelper;
use App\Helpers\Qs;
use App\Helpers\Mk;
use App\Helpers\routeHelper;
use App\Http\Requests\Student\StudentRecordCreate;
use App\Http\Requests\Student\StudentRecordUpdate;
use App\Repositories\LocationRepo;
use App\Repositories\MyClassRepo;
use App\Repositories\StudentRepo;
use App\Repositories\UserRepo;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StudentRecordController extends Controller
{
    protected $loc, $my_class, $user, $student;

   public function __construct(LocationRepo $loc, MyClassRepo $my_class, UserRepo $user, StudentRepo $student)
   {
       $this->middleware('teamSA', ['only' => ['edit','update', 'reset_pass', 'create', 'store', 'graduated'] ]);
       $this->middleware('super_admin', ['only' => ['destroy',] ]);

        $this->loc = $loc;
        $this->my_class = $my_class;
        $this->user = $user;
        $this->student = $student;
   }

    public function reset_pass($st_id)
    {
        $st_id = displayMessageHelper::decodeHash($st_id);
        $data['password'] = Hash::make('student');
        $this->user->update($st_id, $data);
        return back()->with('flash_success', __('msg.p_reset'));
    }

    public function create()
    {
        $data['my_classes'] = $this->my_class->all();
        $data['parents'] = $this->user->getUserByType('parent');
        $data['dorms'] = $this->student->getAllDorms();
        $data['states'] = $this->loc->getStates();
        $data['nationals'] = $this->loc->getAllNationals();
        return view('pages.support_team.students.add', $data);
    }

    public function store(StudentRecordCreate $req)
    {
       $data =  $req->only(getUsersHelper::getUserRecord());
       $sr =  $req->only(getUsersHelper::getStudentData());

        $ct = $this->my_class->findTypeByClass($req->my_class_id)->code;


        $data['user_type'] = 'student';
        $data['name'] = ucwords($req->name);
        $data['code'] = strtoupper(Str::random(10));
        $data['password'] = Hash::make('student');
        $data['photo'] = getPathHelper::getDefaultUserImage();
        $adm_no = $req->adm_no;
        $data['username'] = strtoupper(getSystemInfoHelper::getAppCode().'/'.$ct.'/'.$sr['year_admitted'].'/'.($adm_no ?: mt_rand(1000, 99999)));

        if($req->hasFile('photo')) {
            $photo = $req->file('photo');
            $f = getPathHelper::getFileMetaData($photo);
            $f['name'] = 'photo.' . $f['ext'];
            $f['path'] = $photo->storeAs(getPathHelper::getUploadPath('student').$data['code'], $f['name']);
            $data['photo'] = asset('storage/' . $f['path']);
        }

        $user = $this->user->create($data); // Create User

        $sr['adm_no'] = $data['username'];
        $sr['user_id'] = $user->id;
        $sr['session'] = getSystemInfoHelper::getSetting('current_session');

        $this->student->createRecord($sr); // Create Student
        return jsonHelper::jsonStoreOk();
    }

    public function listByClass($class_id)
    {
        $data['my_class'] = $mc = $this->my_class->getMC(['id' => $class_id])->first();
        $data['students'] = $this->student->findStudentsByClass($class_id);
        $data['sections'] = $this->my_class->getClassSections($class_id);

        return is_null($mc) ? routeHelper::goWithDanger() : view('pages.support_team.students.list', $data);
    }

    public function graduated()
    {
        $data['my_classes'] = $this->my_class->all();
        $data['students'] = $this->student->allGradStudents();

        return view('pages.support_team.students.graduated', $data);
    }

    public function not_graduated($sr_id)
    {
        $d['grad'] = 0;
        $d['grad_date'] = NULL;
        $d['session'] = getSystemInfoHelper::getSetting('current_session');
        $this->student->updateRecord($sr_id, $d);

        return back()->with('flash_success', __('msg.update_ok'));
    }

    public function show($sr_id)
    {
        $sr_id = displayMessageHelper::decodeHash($sr_id);
        if(!$sr_id){return routeHelper::goWithDanger();}

        $data['sr'] = $this->student->getRecord(['id' => $sr_id])->first();

        /* Prevent Other Students/Parents from viewing Profile of others */
        if(Auth::user()->id != $data['sr']->user_id && !checkUsersHelper::userIsTeamSAT() && !checkUsersHelper::userIsMyChild($data['sr']->user_id, Auth::user()->id)){
            return redirect(route('dashboard'))->with('pop_error', __('msg.denied'));
        }

        return view('pages.support_team.students.show', $data);
    }

    public function edit($sr_id)
    {
        $sr_id = displayMessageHelper::decodeHash($sr_id);
        if(!$sr_id){return routeHelper::goWithDanger();}

        $data['sr'] = $this->student->getRecord(['id' => $sr_id])->first();
        $data['my_classes'] = $this->my_class->all();
        $data['parents'] = $this->user->getUserByType('parent');
        $data['dorms'] = $this->student->getAllDorms();
        $data['states'] = $this->loc->getStates();
        $data['nationals'] = $this->loc->getAllNationals();
        return view('pages.support_team.students.edit', $data);
    }

    public function update(StudentRecordUpdate $req, $sr_id)
    {
        $sr_id = displayMessageHelper::decodeHash($sr_id);
        if(!$sr_id){return routeHelper::goWithDanger();}

        $sr = $this->student->getRecord(['id' => $sr_id])->first();
        $d =  $req->only(getUsersHelper::getUserRecord());
        $d['name'] = ucwords($req->name);

        if($req->hasFile('photo')) {
            $photo = $req->file('photo');
            $f = getPathHelper::getFileMetaData($photo);
            $f['name'] = 'photo.' . $f['ext'];
            $f['path'] = $photo->storeAs(getPathHelper::getUploadPath('student').$sr->user->code, $f['name']);
            $d['photo'] = asset('storage/' . $f['path']);
        }

        $this->user->update($sr->user->id, $d); // Update User Details

        $srec = $req->only(getUsersHelper::getStudentData());

        $this->student->updateRecord($sr_id, $srec); // Update St Rec

        /*** If Class/Section is Changed in Same Year, Delete Marks/ExamRecord of Previous Class/Section ****/
        printMarkSheetHelper::deleteOldRecord($sr->user->id, $srec['my_class_id']);

        return jsonHelper::jsonUpdateOk();
    }

    public function destroy($st_id)
    {
        $st_id = displayMessageHelper::decodeHash($st_id);
        if(!$st_id){return routeHelper::goWithDanger();}

        $sr = $this->student->getRecord(['user_id' => $st_id])->first();
        $path = getPathHelper::getUploadPath('student').$sr->user->code;
        Storage::exists($path) ? Storage::deleteDirectory($path) : false;
        $this->user->delete($sr->user->id);

        return back()->with('flash_success', __('msg.del_ok'));
    }

}
