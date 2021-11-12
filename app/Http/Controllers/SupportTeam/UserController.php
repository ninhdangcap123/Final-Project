<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\CheckUsersHelper;
use App\Helpers\DisplayMessageHelper;
use App\Helpers\GetPathHelper;
use App\Helpers\GetSystemInfoHelper;
use App\Helpers\GetUsersHelper;
use App\Helpers\GetUserTypeHelper;
use App\Helpers\JsonHelper;
use App\Http\Controllers\Controller;
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
use App\Repositories\UserType\UserTypeRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class UserController extends Controller
{
    protected $userRepo;
    protected $userTypeRepo;
    protected $staffRepo;
    protected $myCourseRepo;
    protected $stateRepo;
    protected $nationalityRepo;
    protected $subjectRepo;
    protected $bloodGroup;

    public function __construct(UserRepositoryInterface        $userRepo,
                                UserTypeRepositoryInterface    $userTypeRepo,
                                StaffRecordRepositoryInterface $staffRepo,
                                BloodGroupRepositoryInterface  $bloodGroup,
                                SubjectRepositoryInterface     $subjectRepo,
                                MyCourseRepositoryInterface    $myCourseRepo,
                                StateRepositoryInterface       $stateRepo,
                                NationalRepositoryInterface    $nationalityRepo)
    {
        $this->middleware('teamSA', [ 'only' => [ 'index', 'store', 'edit', 'update' ] ]);
        $this->middleware('super_admin', [ 'only' => [ 'reset_pass', 'destroy' ] ]);
        $this->userRepo = $userRepo;
        $this->myCourseRepo = $myCourseRepo;
        $this->stateRepo = $stateRepo;
        $this->nationalityRepo = $nationalityRepo;
        $this->bloodGroup = $bloodGroup;
        $this->userTypeRepo = $userTypeRepo;
        $this->staffRepo = $staffRepo;
        $this->subjectRepo = $subjectRepo;
    }

    public function index()
    {
        $allUserTypes = $this->userTypeRepo->getAll();
        $userType = $allUserTypes->where('level', '>', 2);
        $data['user_types'] = GetUserTypeHelper::userIsAdmin() ? $userType : $allUserTypes;
        $data['states'] = $this->stateRepo->getStates();
        $data['users'] = $this->userRepo->getPTAUsers();
        $data['nationals'] = $this->nationalityRepo->getAllNationals();
        $data['blood_groups'] = $this->bloodGroup->getAll();
        return view('pages.support_team.users.index', $data);
    }

    public function edit($id)
    {
        $id = DisplayMessageHelper::decodeHash($id);
        $data['user'] = $this->userRepo->find($id);
        $data['states'] = $this->stateRepo->getStates();
        $data['users'] = $this->userRepo->getPTAUsers();
        $data['blood_groups'] = $this->bloodGroup->getAll();
        $data['nationals'] = $this->nationalityRepo->getAllNationals();
        return view('pages.support_team.users.edit', $data);
    }

    public function resetPass($id)
    {
        // Redirect if Making Changes to Head of Super Admins
        if( CheckUsersHelper::headSA($id) ) {
            return back()->with('flash_danger', __('msg.denied'));
        }
        $data['password'] = Hash::make('user');
        $this->userRepo->update($id, $data);
        return back()->with('flash_success', __('msg.pu_reset'));
    }

    public function store(UserRequest $request)
    {
        $userType = $this->userTypeRepo->find($request->user_type)->title;

        $data = $request->except(GetUsersHelper::getStaffRecord());
        $data['name'] = ucwords($request->name);
        $data['user_type'] = $userType;
        $data['photo'] = GetPathHelper::getDefaultUserImage();
        $data['code'] = strtoupper(Str::random(10));

        $userIsStaff = in_array($userType, GetUsersHelper::getStaff());
        $userIsTeamSA = in_array($userType, GetUsersHelper::getTeamSA());

        $staffId = GetSystemInfoHelper::getAppCode().'/STAFF/'.date('Y/m', strtotime($request->emp_date)).'/'.mt_rand(1000, 9999);
        $data['username'] = $userName = ( $userIsTeamSA ) ? $request->username : $staffId;

        $password = $request->password ?: $userType;
        $data['password'] = Hash::make($password);

        if( $request->hasFile('photo') ) {
            $photo = $request->file('photo');
            $file = GetPathHelper::getFileMetaData($photo);
            $file['name'] = 'photo.'.$file['ext'];
            $file['path'] = $photo->storeAs(GetPathHelper::getUploadPath($userType).$data['code'], $file['name']);
            $data['photo'] = asset('storage/'.$file['path']);
        }

        /* Ensure that both username and Email are not blank*/
        if( !$userName && !$request->email ) {
            return back()->with('pop_error', __('msg.user_invalid'));
        }

        $user = $this->userRepo->create($data); // Create User

        /* CREATE STAFF RECORD */
        if( $userIsStaff ) {
            $data2 = $request->only(GetUsersHelper::getStaffRecord());
            $data2['user_id'] = $user->id;
            $data2['code'] = $staffId;
            $this->staffRepo->create($data2);
        }

        return JsonHelper::jsonStoreSuccess();
    }

    public function update(UserRequest $request, $id)
    {
        $id = DisplayMessageHelper::decodeHash($id);

        // Redirect if Making Changes to Head of Super Admins
        if( CheckUsersHelper::headSA($id) ) {
            return JsonHelper::json(__('msg.denied'), FALSE);
        }

        $user = $this->userRepo->find($id);

        $userType = $user->user_type;
        $userIsStaff = in_array($userType, GetUsersHelper::getStaff());
        $userIsTeamSA = in_array($userType, GetUsersHelper::getTeamSA());

        $data = $request->except(GetUsersHelper::getStaffRecord());
        $data['name'] = ucwords($request->name);

        if( $userIsStaff && !$userIsTeamSA ) {
            $data['username'] = GetSystemInfoHelper::getAppCode().'/STAFF/'.date('Y/m', strtotime($request->emp_date)).'/'.mt_rand(1000, 9999);
        } else {
            $data['username'] = $user->username;
        }

        if( $request->hasFile('photo') ) {
            $photo = $request->file('photo');
            $file = GetPathHelper::getFileMetaData($photo);
            $file['name'] = 'photo.'.$file['ext'];
            $file['path'] = $photo->storeAs(GetPathHelper::getUploadPath($userType).$data['code'], $file['name']);
            $data['photo'] = asset('storage/'.$file['path']);
        }

        $this->userRepo->update($id, $data);   /* UPDATE USER RECORD */

        /* UPDATE STAFF RECORD */
        if( $userIsStaff ) {
            $data2 = $request->only(GetUsersHelper::getStaffRecord());
            $data2['code'] = $data['username'];
            $this->staffRepo->update([ 'user_id' => $id ], $data2);
        }

        return JsonHelper::jsonUpdateSuccess();
    }

    public function show($user_id)
    {
        $user_id = DisplayMessageHelper::decodeHash($user_id);
        if( !$user_id ) {
            return back();
        }

        $data['user'] = $this->userRepo->find($user_id);

        /* Prevent Other Students from viewing Profile of others*/
        if( Auth::user()->id != $user_id
            && !CheckUsersHelper::userIsTeamSAT()
            && !CheckUsersHelper::userIsMyChild(Auth::user()->id, $user_id) ) {
            return redirect(route('dashboard'))->with('pop_error', __('msg.denied'));
        }

        return view('pages.support_team.users.show', $data);
    }

    public function destroy($id)
    {
        $id = DisplayMessageHelper::decodeHash($id);

        // Redirect if Making Changes to Head of Super Admins
        if( CheckUsersHelper::headSA($id) ) {
            return back()->with('pop_error', __('msg.denied'));
        }

        $user = $this->userRepo->find($id);
        if( $user->user_type == 'teacher' && $this->userTeachesSubject($user) ) {
            return back()->with('pop_error', __('msg.del_teacher'));
        }

        $path = GetPathHelper::getUploadPath($user->user_type).$user->code;
        !Storage::exists($path) || Storage::deleteDirectory($path);
        $this->userRepo->delete($user->id);

        return back()->with('flash_success', __('msg.del_ok'));
    }

    protected function userTeachesSubject($user): bool
    {
        $subjects = $this->subjectRepo->findSubjectByTeacher($user->id);
        return $subjects->count() > 0;
    }

}
