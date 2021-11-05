<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\CheckUsersHelper;
use App\Helpers\DisplayMessageHelper;
use App\Helpers\GetPathHelper;
use App\Helpers\GetSystemInfoHelper;
use App\Helpers\GetUsersHelper;
use App\Helpers\JsonHelper;
use App\Helpers\PrintMarkSheetHelper;
use App\Helpers\Qs;
use App\Helpers\Mk;
use App\Helpers\RouteHelper;
use App\Http\Requests\Student\StudentRecordCreate;
use App\Http\Requests\Student\StudentRecordUpdate;
use App\Repositories\Classes\ClassesRepositoryInterface;
use App\Repositories\LocationRepo;
use App\Repositories\Major\MajorRepositoryInterface;
use App\Repositories\MyCourse\MyCourseRepositoryInterface;
use App\Repositories\MyCourseRepo;
use App\Repositories\Nationals\NationalRepositoryInterface;
use App\Repositories\Promotion\PromotionRepositoryInterface;
use App\Repositories\State\StateRepositoryInterface;
use App\Repositories\Student\StudentRepositoryInterface;
use App\Repositories\StudentRepo;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\UserRepo;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StudentRecordController extends Controller
{
    protected $my_course, $promotion, $major, $class, $user, $student, $state, $nal;

   public function __construct(PromotionRepositoryInterface $promotion, ClassesRepositoryInterface $class, MyCourseRepositoryInterface $my_course,
                               UserRepositoryInterface $user, StudentRepositoryInterface $student, StateRepositoryInterface $state,
                               NationalRepositoryInterface $nal, MajorRepositoryInterface $major)
   {
       $this->middleware('teamSA', ['only' => ['edit','update', 'reset_pass', 'create', 'store', 'graduated'] ]);
       $this->middleware('super_admin', ['only' => ['destroy',] ]);


        $this->my_course = $my_course;
        $this->user = $user;
        $this->student = $student;
        $this->state = $state;
        $this->class = $class;
        $this->major = $major;
        $this->nal = $nal;
        $this->promotion = $promotion;
   }

    public function resetPass($st_id)
    {
        $st_id = DisplayMessageHelper::decodeHash($st_id);
        $data['password'] = Hash::make('student');
        $this->user->update($st_id, $data);
        return back()->with('flash_success', __('msg.p_reset'));
    }

    public function create()
    {
        $data['my_courses'] = $this->my_course->getAll();
        $data['parents'] = $this->user->getUserByType('parent');
        $data['dorms'] = $this->student->getAllDorms();
        $data['states'] = $this->state->getStates();
        $data['nationals'] = $this->nal->getAllNationals();
        return view('pages.support_team.students.add', $data);
    }

    public function store(StudentRecordCreate $req)
    {
       $data =  $req->only(GetUsersHelper::getUserRecord());
       $sr =  $req->only(GetUsersHelper::getStudentData());

        $ct = $this->major->findMajorByCourse($req->my_course_id)->code;


        $data['user_type'] = 'student';
        $data['name'] = ucwords($req->name);
        $data['code'] = strtoupper(Str::random(10));
        $data['password'] = Hash::make('student');
        $data['photo'] = GetPathHelper::getDefaultUserImage();
        $adm_no = $req->adm_no;
        $data['username'] = strtoupper(GetSystemInfoHelper::getAppCode().'/'.$ct.'/'.$sr['year_admitted'].'/'.($adm_no ?: mt_rand(1000, 99999)));

        if($req->hasFile('photo')) {
            $photo = $req->file('photo');
            $f = GetPathHelper::getFileMetaData($photo);
            $f['name'] = 'photo.' . $f['ext'];
            $f['path'] = $photo->storeAs(GetPathHelper::getUploadPath('student').$data['code'], $f['name']);
            $data['photo'] = asset('storage/' . $f['path']);
        }

        $user = $this->user->create($data); // Create User

        $sr['adm_no'] = $data['username'];
        $sr['user_id'] = $user->id;
        $sr['session'] = GetSystemInfoHelper::getSetting('current_session');

        $this->student->createRecord($sr); // Create Student
        return JsonHelper::jsonStoreOk();
    }

    public function listByClass($course_id)
    {
        $data['my_course'] = $mc = $this->my_course->getMC(['id' => $course_id])->first();
        $data['students'] = $this->student->findStudentsByCourse($course_id);
        $data['classes'] = $this->class->getCourseClasses($course_id);

        return is_null($mc) ? RouteHelper::goWithDanger() : view('pages.support_team.students.list', $data);
    }

    public function graduated()
    {
        $data['my_courses'] = $this->my_course->getAll();
        $data['students'] = $this->student->allGradStudents();

        return view('pages.support_team.students.graduated', $data);
    }

    public function notGraduated($sr_id)
    {
        $d['grad'] = 0;
        $d['grad_date'] = NULL;
        $d['session'] = GetSystemInfoHelper::getSetting('current_session');
        $this->student->updateRecord($sr_id, $d);

        return back()->with('flash_success', __('msg.update_ok'));
    }

    public function show($sr_id)
    {
        $sr_id = DisplayMessageHelper::decodeHash($sr_id);
        if(!$sr_id){return RouteHelper::goWithDanger();}

        $data['sr'] = $this->student->getRecord(['id' => $sr_id])->first();

        /* Prevent Other Students/Parents from viewing Profile of others */
        if(Auth::user()->id != $data['sr']->user_id && !CheckUsersHelper::userIsTeamSAT() && !CheckUsersHelper::userIsMyChild($data['sr']->user_id, Auth::user()->id)){
            return redirect(route('dashboard'))->with('pop_error', __('msg.denied'));
        }

        return view('pages.support_team.students.show', $data);
    }

    public function edit($sr_id)
    {
        $sr_id = DisplayMessageHelper::decodeHash($sr_id);
        if(!$sr_id){return RouteHelper::goWithDanger();}

        $data['sr'] = $this->student->getRecord(['id' => $sr_id])->first();
        $data['my_courses'] = $this->my_course->getAll();
        $data['parents'] = $this->user->getUserByType('parent');
        $data['dorms'] = $this->student->getAllDorms();
        $data['states'] = $this->state->getStates();
        $data['nationals'] = $this->nal->getAllNationals();
        return view('pages.support_team.students.edit', $data);
    }

    public function update(StudentRecordUpdate $req, $sr_id)
    {
        $sr_id = DisplayMessageHelper::decodeHash($sr_id);
        if(!$sr_id){return RouteHelper::goWithDanger();}

        $sr = $this->student->getRecord(['id' => $sr_id])->first();
        $d =  $req->only(GetUsersHelper::getUserRecord());
        $d['name'] = ucwords($req->name);

        if($req->hasFile('photo')) {
            $photo = $req->file('photo');
            $f = GetPathHelper::getFileMetaData($photo);
            $f['name'] = 'photo.' . $f['ext'];
            $f['path'] = $photo->storeAs(GetPathHelper::getUploadPath('student').$sr->user->code, $f['name']);
            $d['photo'] = asset('storage/' . $f['path']);
        }

        $this->user->update($sr->user->id, $d); // Update User Details

        $srec = $req->only(GetUsersHelper::getStudentData());

        $this->student->updateRecord($sr_id, $srec); // Update St Rec

        /*** If Class/Classes is Changed in Same Year, Delete Marks/ExamRecord of Previous Class/Classes ****/
        PrintMarkSheetHelper::deleteOldRecord($sr->user->id, $srec['my_course_id']);

        return JsonHelper::jsonUpdateOk();
    }

    public function destroy($st_id)
    {
        $st_id = DisplayMessageHelper::decodeHash($st_id);
        if(!$st_id){return RouteHelper::goWithDanger();}

        $sr = $this->student->getRecord(['user_id' => $st_id])->first();
        $path = GetPathHelper::getUploadPath('student').$sr->user->code;
        Storage::exists($path) && Storage::deleteDirectory($path);
        $this->user->delete($sr->user->id);

        return back()->with('flash_success', __('msg.del_ok'));
    }

}
