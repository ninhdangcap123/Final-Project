<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\CheckUsersHelper;
use App\Helpers\DisplayMessageHelper;
use App\Helpers\GetPathHelper;
use App\Helpers\GetSystemInfoHelper;
use App\Helpers\GetUsersHelper;
use App\Helpers\GetUserTypeHelper;
use App\Helpers\JsonHelper;
use App\Helpers\Qs;
use App\Http\Requests\UserRequest;
use App\Repositories\BloodGroup\BloodGroupRepositoryInterface;
use App\Repositories\LocationRepo;
use App\Repositories\MyCourse\MyCourseRepositoryInterface;
use App\Repositories\MyCourseRepo;
use App\Repositories\Nationals\NationalRepositoryInterface;
use App\Repositories\StaffRecord\StaffRecordRepositoryInterface;
use App\Repositories\State\StateRepositoryInterface;
use App\Repositories\Subject\SubjectRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\UserRepo;
use App\Http\Controllers\Controller;
use App\Repositories\UserType\UserTypeRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class UserController extends Controller
{
    protected $user, $user_type, $staff, $my_course, $state, $nal, $subject, $bg;

    public function __construct(UserRepositoryInterface $user, UserTypeRepositoryInterface $user_type, StaffRecordRepositoryInterface $staff,
                                BloodGroupRepositoryInterface $bg, SubjectRepositoryInterface $subject, MyCourseRepositoryInterface $my_course,
                                StateRepositoryInterface $state, NationalRepositoryInterface $nal)
    {
        $this->middleware('teamSA', ['only' => ['index', 'store', 'edit', 'update'] ]);
        $this->middleware('super_admin', ['only' => ['reset_pass','destroy'] ]);

        $this->user = $user;
        $this->my_course = $my_course;
        $this->state = $state;
        $this->nal = $nal;
        $this->bg = $bg;
        $this->user_type = $user_type;
        $this->staff = $staff;
        $this->subject = $subject;
    }

    public function index()
    {
        $ut = $this->user_type->getAll();
        $ut2 = $ut->where('level', '>', 2);

        $d['user_types'] = GetUserTypeHelper::userIsAdmin() ? $ut2 : $ut;
        $d['states'] = $this->state->getStates();
        $d['users'] = $this->user->getPTAUsers();
        $d['nationals'] = $this->nal->getAllNationals();
        $d['blood_groups'] = $this->bg->getAll();
        return view('pages.support_team.users.index', $d);
    }

    public function edit($id)
    {
        $id = DisplayMessageHelper::decodeHash($id);
        $d['user'] = $this->user->find($id);
        $d['states'] = $this->state->getStates();
        $d['users'] = $this->user->getPTAUsers();
        $d['blood_groups'] = $this->bg->getAll();
        $d['nationals'] = $this->nal->getAllNationals();
        return view('pages.support_team.users.edit', $d);
    }

    public function resetPass($id)
    {
        // Redirect if Making Changes to Head of Super Admins
        if(CheckUsersHelper::headSA($id)){
            return back()->with('flash_danger', __('msg.denied'));
        }

        $data['password'] = Hash::make('user');
        $this->user->update($id, $data);
        return back()->with('flash_success', __('msg.pu_reset'));
    }

    public function store(UserRequest $req)
    {
        $user_type = $this->user_type->find($req->user_type)->title;

        $data = $req->except(GetUsersHelper::getStaffRecord());
        $data['name'] = ucwords($req->name);
        $data['user_type'] = $user_type;
        $data['photo'] = GetPathHelper::getDefaultUserImage();
        $data['code'] = strtoupper(Str::random(10));

        $user_is_staff = in_array($user_type, GetUsersHelper::getStaff());
        $user_is_teamSA = in_array($user_type, GetUsersHelper::getTeamSA());

        $staff_id = GetSystemInfoHelper::getAppCode().'/STAFF/'.date('Y/m', strtotime($req->emp_date)).'/'.mt_rand(1000, 9999);
        $data['username'] = $uname = ($user_is_teamSA) ? $req->username : $staff_id;

        $pass = $req->password ?: $user_type;
        $data['password'] = Hash::make($pass);

        if($req->hasFile('photo')) {
            $photo = $req->file('photo');
            $f = GetPathHelper::getFileMetaData($photo);
            $f['name'] = 'photo.' . $f['ext'];
            $f['path'] = $photo->storeAs(GetPathHelper::getUploadPath($user_type).$data['code'], $f['name']);
            $data['photo'] = asset('storage/' . $f['path']);
        }

        /* Ensure that both username and Email are not blank*/
        if(!$uname && !$req->email){
            return back()->with('pop_error', __('msg.user_invalid'));
        }

        $user = $this->user->create($data); // Create User

        /* CREATE STAFF RECORD */
        if($user_is_staff){
            $d2 = $req->only(GetUsersHelper::getStaffRecord());
            $d2['user_id'] = $user->id;
            $d2['code'] = $staff_id;
            $this->staff->create($d2);
        }

        return JsonHelper::jsonStoreOk();
    }

    public function update(UserRequest $req, $id)
    {
        $id = DisplayMessageHelper::decodeHash($id);

        // Redirect if Making Changes to Head of Super Admins
        if(CheckUsersHelper::headSA($id)){
            return JsonHelper::json(__('msg.denied'), FALSE);
        }

        $user = $this->user->find($id);

        $user_type = $user->user_type;
        $user_is_staff = in_array($user_type, GetUsersHelper::getStaff());
        $user_is_teamSA = in_array($user_type, GetUsersHelper::getTeamSA());

        $data = $req->except(GetUsersHelper::getStaffRecord());
        $data['name'] = ucwords($req->name);

        if($user_is_staff && !$user_is_teamSA){
            $data['username'] = GetSystemInfoHelper::getAppCode().'/STAFF/'.date('Y/m', strtotime($req->emp_date)).'/'.mt_rand(1000, 9999);
        }
        else {
            $data['username'] = $user->username;
        }

        if($req->hasFile('photo')) {
            $photo = $req->file('photo');
            $f = GetPathHelper::getFileMetaData($photo);
            $f['name'] = 'photo.' . $f['ext'];
            $f['path'] = $photo->storeAs(GetPathHelper::getUploadPath($user_type).$data['code'], $f['name']);
            $data['photo'] = asset('storage/' . $f['path']);
        }

        $this->user->update($id, $data);   /* UPDATE USER RECORD */

        /* UPDATE STAFF RECORD */
        if($user_is_staff){
            $d2 = $req->only(GetUsersHelper::getStaffRecord());
            $d2['code'] = $data['username'];
            $this->staff->update(['user_id' => $id], $d2);
        }

        return JsonHelper::jsonUpdateOk();
    }

    public function show($user_id)
    {
        $user_id = DisplayMessageHelper::decodeHash($user_id);
        if(!$user_id){return back();}

        $data['user'] = $this->user->find($user_id);

        /* Prevent Other Students from viewing Profile of others*/
        if(Auth::user()->id != $user_id && !CheckUsersHelper::userIsTeamSAT() && !CheckUsersHelper::userIsMyChild(Auth::user()->id, $user_id)){
            return redirect(route('dashboard'))->with('pop_error', __('msg.denied'));
        }

        return view('pages.support_team.users.show', $data);
    }

    public function destroy($id)
    {
        $id = DisplayMessageHelper::decodeHash($id);

        // Redirect if Making Changes to Head of Super Admins
        if(CheckUsersHelper::headSA($id)){
            return back()->with('pop_error', __('msg.denied'));
        }

        $user = $this->user->find($id);

        if($user->user_type == 'teacher' && $this->userTeachesSubject($user)) {
            return back()->with('pop_error', __('msg.del_teacher'));
        }

        $path = GetPathHelper::getUploadPath($user->user_type).$user->code;
        !Storage::exists($path) || Storage::deleteDirectory($path);
        $this->user->delete($user->id);

        return back()->with('flash_success', __('msg.del_ok'));
    }

    protected function userTeachesSubject($user): bool
    {
        $subjects = $this->subject->findSubjectByTeacher($user->id);
        return $subjects->count() > 0;
    }

}
