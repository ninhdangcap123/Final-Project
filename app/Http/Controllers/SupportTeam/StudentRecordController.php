<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\CheckUsersHelper;
use App\Helpers\DisplayMessageHelper;
use App\Helpers\GetPathHelper;
use App\Helpers\GetSystemInfoHelper;
use App\Helpers\GetUsersHelper;
use App\Helpers\JsonHelper;
use App\Helpers\PrintMarkSheetHelper;
use App\Helpers\RouteHelper;
use App\Http\Controllers\Controller;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StudentRecordController extends Controller
{
    protected $myCourseRepo;
    protected $promotionRepo;
    protected $majorRepo;
    protected $classRepo;
    protected $userRepo;
    protected $studentRepo;
    protected $state;
    protected $nationalityRepo;

    public function __construct(PromotionRepositoryInterface $promotionRepo,
                                ClassesRepositoryInterface   $classRepo,
                                MyCourseRepositoryInterface  $myCourseRepo,
                                UserRepositoryInterface      $userRepo,
                                StudentRepositoryInterface   $studentRepo,
                                StateRepositoryInterface     $stateRepo,
                                NationalRepositoryInterface  $nationalityRepo,
                                MajorRepositoryInterface     $majorRepo)
    {
        $this->middleware('teamSA', [ 'only' => [ 'edit', 'update', 'reset_pass', 'create', 'store', 'graduated' ] ]);
        $this->middleware('super_admin', [ 'only' => [ 'destroy', ] ]);
        $this->myCourseRepo = $myCourseRepo;
        $this->userRepo = $userRepo;
        $this->studentRepo = $studentRepo;
        $this->state = $stateRepo;
        $this->classRepo = $classRepo;
        $this->majorRepo = $majorRepo;
        $this->nationalityRepo = $nationalityRepo;
        $this->promotionRepo = $promotionRepo;
    }

    public function resetPass($st_id)
    {
        $st_id = DisplayMessageHelper::decodeHash($st_id);
        $data['password'] = Hash::make('student');
        $this->userRepo->update($st_id, $data);
        return back()->with('flash_success', __('msg.p_reset'));
    }

    public function create()
    {
        $data['my_courses'] = $this->myCourseRepo->getAll();
        $data['parents'] = $this->userRepo->getUserByType('parent');
        $data['dorms'] = $this->studentRepo->getAllDorms();
        $data['states'] = $this->state->getStates();
        $data['nationals'] = $this->nationalityRepo->getAllNationals();
        return view('pages.support_team.students.add', $data);
    }

    public function store(StudentRecordCreate $request)
    {
        $data = $request->only(GetUsersHelper::getUserRecord());
        $studentRecord = $request->only(GetUsersHelper::getStudentData());

        $major = $this->majorRepo->findMajorByCourse($request->my_course_id)->code;


        $data['user_type'] = 'student';
        $data['name'] = ucwords($request->name);
        $data['code'] = strtoupper(Str::random(10));
        $data['password'] = Hash::make('student');
        $data['photo'] = GetPathHelper::getDefaultUserImage();
        $administrationNo = $request->adm_no;
        $data['username'] = strtoupper(GetSystemInfoHelper::getAppCode().'/'.$major.'/'.$studentRecord['year_admitted'].'/'.( $administrationNo ?: mt_rand(1000, 99999) ));

        if( $request->hasFile('photo') ) {
            $photo = $request->file('photo');
            $file = GetPathHelper::getFileMetaData($photo);
            $file['name'] = 'photo.'.$file['ext'];
            $file['path'] = $photo->storeAs(GetPathHelper::getUploadPath('student').$data['code'], $file['name']);
            $data['photo'] = asset('storage/'.$file['path']);
        }

        $user = $this->userRepo->create($data); // Create User

        $studentRecord['adm_no'] = $data['username'];
        $studentRecord['user_id'] = $user->id;
        $studentRecord['session'] = GetSystemInfoHelper::getSetting('current_session');

        $this->studentRepo->createRecord($studentRecord); // Create Student
        return JsonHelper::jsonStoreSuccess();
    }

    public function listByClass($course_id)
    {
        $data['my_course'] = $myCourse = $this->myCourseRepo->getMC([ 'id' => $course_id ])->first();
        $data['students'] = $this->studentRepo->findStudentsByCourse($course_id);
        $data['classes'] = $this->classRepo->getCourseClasses($course_id);

        return is_null($myCourse) ? RouteHelper::goWithDanger() : view('pages.support_team.students.list', $data);
    }

    public function graduated()
    {
        $data['my_courses'] = $this->myCourseRepo->getAll();
        $data['students'] = $this->studentRepo->allGradStudents();
        return view('pages.support_team.students.graduated', $data);
    }

    public function notGraduated($sr_id)
    {
        $data['grad'] = 0;
        $data['grad_date'] = NULL;
        $data['session'] = GetSystemInfoHelper::getSetting('current_session');
        $this->studentRepo->updateRecord($sr_id, $data);

        return back()->with('flash_success', __('msg.update_ok'));
    }

    public function show($sr_id)
    {
        $sr_id = DisplayMessageHelper::decodeHash($sr_id);
        if( !$sr_id ) {
            return RouteHelper::goWithDanger();
        }

        $data['sr'] = $this->studentRepo->getRecord([ 'id' => $sr_id ])->first();

        /* Prevent Other Students/Parents from viewing Profile of others */
        if( Auth::user()->id != $data['sr']->user_id && !CheckUsersHelper::userIsTeamSAT() && !CheckUsersHelper::userIsMyChild($data['sr']->user_id, Auth::user()->id) ) {
            return redirect(route('dashboard'))->with('pop_error', __('msg.denied'));
        }

        return view('pages.support_team.students.show', $data);
    }

    public function edit($sr_id)
    {
        $sr_id = DisplayMessageHelper::decodeHash($sr_id);
        if( !$sr_id ) {
            return RouteHelper::goWithDanger();
        }

        $data['sr'] = $this->studentRepo->getRecord([ 'id' => $sr_id ])->first();
        $data['my_courses'] = $this->myCourseRepo->getAll();
        $data['parents'] = $this->userRepo->getUserByType('parent');
        $data['dorms'] = $this->studentRepo->getAllDorms();
        $data['states'] = $this->state->getStates();
        $data['nationals'] = $this->nationalityRepo->getAllNationals();
        return view('pages.support_team.students.edit', $data);
    }

    public function update(StudentRecordUpdate $request, $sr_id)
    {
        $sr_id = DisplayMessageHelper::decodeHash($sr_id);
        if( !$sr_id ) {
            return RouteHelper::goWithDanger();
        }

        $studentRecord = $this->studentRepo->getRecord([ 'id' => $sr_id ])->first();
        $data = $request->only(GetUsersHelper::getUserRecord());
        $data['name'] = ucwords($request->name);

        if( $request->hasFile('photo') ) {
            $photo = $request->file('photo');
            $file = GetPathHelper::getFileMetaData($photo);
            $file['name'] = 'photo.'.$file['ext'];
            $file['path'] = $photo->storeAs(GetPathHelper::getUploadPath('student').$studentRecord->user->code, $file['name']);
            $data['photo'] = asset('storage/'.$file['path']);
        }

        $this->userRepo->update($studentRecord->user->id, $data); // Update User Details
        $studentData = $request->only(GetUsersHelper::getStudentData());
        $this->studentRepo->updateRecord($sr_id, $studentData); // Update St Rec

        /*** If Class/Classes is Changed in Same Year, Delete Marks/ExamRecord of Previous Class/Classes ****/
        PrintMarkSheetHelper::deleteOldRecord($studentRecord->user->id, $studentData['my_course_id']);

        return JsonHelper::jsonUpdateSuccess();
    }

    public function destroy($st_id)
    {
        $st_id = DisplayMessageHelper::decodeHash($st_id);
        if( !$st_id ) {
            return RouteHelper::goWithDanger();
        }

        $studentRecord = $this->studentRepo->getRecord([ 'user_id' => $st_id ])->first();
        $path = GetPathHelper::getUploadPath('student').$studentRecord->user->code;
        Storage::exists($path) && Storage::deleteDirectory($path);
        $this->userRepo->delete($studentRecord->user->id);

        return back()->with('flash_success', __('msg.del_ok'));
    }

}
