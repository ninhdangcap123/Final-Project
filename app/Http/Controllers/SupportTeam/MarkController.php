<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\CheckExamInfoHelper;
use App\Helpers\CheckUsersHelper;
use App\Helpers\DisplayMessageHelper;
use App\Helpers\GetSystemInfoHelper;
use App\Helpers\GetUserTypeHelper;
use App\Helpers\JsonHelper;
use App\Helpers\Qs;
use App\Helpers\Mk;
use App\Helpers\RouteHelper;
use App\Http\Requests\Mark\MarkSelector;
use App\Models\Setting;
use App\Repositories\Classes\ClassesRepositoryInterface;
use App\Repositories\Exam\ExamRepositoryInterface;
use App\Repositories\ExamRecord\ExamRecordRepositoryInterface;
use App\Repositories\ExamRepo;
use App\Repositories\Major\MajorRepositoryInterface;
use App\Repositories\Mark\MarkRepositoryInterface;
use App\Repositories\MarkRepo;
use App\Repositories\MyCourse\MyCourseRepositoryInterface;
use App\Repositories\MyCourseRepo;
use App\Http\Controllers\Controller;
use App\Repositories\Skill\SkillRepositoryInterface;
use App\Repositories\Student\StudentRepositoryInterface;
use App\Repositories\StudentRepo;
use App\Repositories\Subject\SubjectRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class MarkController extends Controller
{
    protected $my_course, $exam_record, $skill, $class, $exam, $student, $year, $user, $mark, $subject, $major;

    public function __construct(ExamRecordRepositoryInterface $exam_record, SkillRepositoryInterface $skill, MyCourseRepositoryInterface $my_course,
                                ClassesRepositoryInterface $class,SubjectRepositoryInterface $subject, ExamRepositoryInterface $exam,
                                StudentRepositoryInterface $student,MarkRepositoryInterface $mark, MajorRepositoryInterface $major)
    {
        $this->exam =  $exam;
        $this->mark =  $mark;
        $this->subject = $subject;
        $this->student =  $student;
        $this->my_course =  $my_course;
        $this->class = $class;
        $this->major = $major;
        $this->skill = $skill;
        $this->exam_record = $exam_record;
        $this->year =  GetSystemInfoHelper::getSetting('current_session');
    }

    public function index()
    {
        $d['exams'] = $this->exam->getExam(['year' => $this->year]);
        $d['my_courses'] = $this->my_course->getAll();
        $d['classes'] = $this->class->getAll();
        $d['subjects'] = $this->subject->getAll();
        $d['selected'] = false;

        return view('pages.support_team.marks.index', $d);
    }

    public function yearSelector($student_id)
    {
       return $this->verifyStudentExamYear($student_id);
    }

    public function yearSelected(Request $req, $student_id): \Illuminate\Http\RedirectResponse
    {
        if(!$this->verifyStudentExamYear($student_id, $req->year)){
            return $this->noStudentRecord();
        }

        $student_id = DisplayMessageHelper::hash($student_id);
        return redirect()->route('marks.show', [$student_id, $req->year]);
    }

    public function show($student_id, $year)
    {
        /* Prevent Other Students/Parents from viewing Result of others */
        if(Auth::user()->id != $student_id && !CheckUsersHelper::userIsTeamSAT() && !CheckUsersHelper::userIsMyChild($student_id, Auth::user()->id)){
            return redirect(route('dashboard'))->with('pop_error', __('msg.denied'));
        }

        if(CheckExamInfoHelper::examIsLocked() && !CheckUsersHelper::userIsTeamSA()){
            Session::put('marks_url', route('marks.show', [DisplayMessageHelper::hash($student_id), $year]));

            if(!$this->checkPinVerified($student_id)){
                return redirect()->route('pins.enter', DisplayMessageHelper::hash($student_id));
            }
        }

        if(!$this->verifyStudentExamYear($student_id, $year)){
            return $this->noStudentRecord();
        }

        $wh = ['student_id' => $student_id, 'year' => $year ];
        $data['marks'] = $this->mark->getMark($wh);
        $data['exam_records'] = $exr = $this->exam_record->getRecord($wh);
        $data['exams'] = $this->exam->getExam(['year' => $year]);
        $data['sr'] = $this->student->getRecord(['user_id' => $student_id])->first();
        $data['my_course'] = $mc = $this->my_course->getMC(['id' => $exr->first()->my_course_id])->first();
        $data['major'] = $this->major->findMajorByCourse($mc->id);
        $data['subjects'] = $this->subject->findSubjectByCourse($mc->id);
        $data['year'] = $year;
        $data['student_id'] = $student_id;
        $data['skills'] = $this->skill->getSkillByMajor() ?: NULL;

        return view('pages.support_team.marks.show.index', $data);
    }

    public function printView($student_id, $exam_id, $year)
    {
        /* Prevent Other Students/Parents from viewing Result of others */
        if(Auth::user()->id != $student_id && !CheckUsersHelper::userIsTeamSA() && !CheckUsersHelper::userIsMyChild($student_id, Auth::user()->id)){
            return redirect(route('dashboard'))->with('pop_error', __('msg.denied'));
        }

        if(CheckExamInfoHelper::examIsLocked() && !CheckUsersHelper::userIsTeamSA()){
            Session::put('marks_url', route('marks.show', [DisplayMessageHelper::hash($student_id), $year]));

            if(!$this->checkPinVerified($student_id)){
                return redirect()->route('pins.enter', DisplayMessageHelper::hash($student_id));
            }
        }

        if(!$this->verifyStudentExamYear($student_id, $year)){
            return $this->noStudentRecord();
        }

        $wh = ['student_id' => $student_id, 'exam_id' => $exam_id, 'year' => $year ];
        $d['marks'] = $mks = $this->mark->getMark($wh);
        $d['exr'] = $exr = $this->exam_record->getRecord($wh)->first();
        $d['my_course'] = $mc = $this->my_course->find($exr->my_course_id);
        $d['class_id'] = $exr->class_id;
        $d['ex'] = $exam = $this->exam->find($exam_id);
        $d['tex'] = 'tex'.$exam->term;
        $d['sr'] = $sr =$this->student->getRecord(['user_id' => $student_id])->first();
        $d['major'] = $this->major->findMajorByCourse($mc->id);
        $d['subjects'] = $this->subject->findSubjectByCourse($mc->id);

        $d['major'] = $major = $d['major'];
        $d['year'] = $year;
        $d['student_id'] = $student_id;
        $d['exam_id'] = $exam_id;

        $d['skills'] = $this->skill->getSkillByMajor() ?: NULL;
        $d['s'] = Setting::all()->flatMap(function($s){
            return [$s->type => $s->description];
        });

        return view('pages.support_team.marks.print.index', $d);
    }

    public function selector(MarkSelector $req): \Illuminate\Http\RedirectResponse
    {
        $data = $req->only(['exam_id', 'my_course_id', 'class_id', 'subject_id']);
        $d2 = $req->only(['exam_id', 'my_course_id', 'class_id']);
        $d = $req->only(['my_course_id', 'class_id']);
        $d['session'] = $data['year'] = $d2['year'] = $this->year;

        $students = $this->student->getRecord($d)->get();
        if($students->count() < 1){
            return back()->with('pop_error', __('msg.rnf'));
        }

        foreach ($students as $s){
            $data['student_id'] = $d2['student_id'] = $s->user_id;
            $this->mark->create($data);
            $this->exam_record->create($d2);
        }

        return redirect()->route('marks.manage', [$req->exam_id, $req->my_course_id, $req->class_id, $req->subject_id]);
    }

    public function manage($exam_id, $my_course_id, $class_id, $subject_id)
    {
        $data = ['exam_id' => $exam_id, 'my_course_id' => $my_course_id, 'class_id' => $class_id, 'subject_id' => $subject_id, 'year' => $this->year];

        $data['marks'] = $this->mark->getMark($data);
        if($data['marks']->count() < 1){
            return $this->noStudentRecord();
        }

        $data['m'] =  $data['marks']->first();
        $data['exams'] = $this->exam->getAll();
        $data['my_courses'] = $this->my_course->getAll();
        $data['classes'] = $this->class->getAll();
        $data['subjects'] = $this->subject->getAll();
        if(GetUserTypeHelper::userIsTeacher()){
            $data['subjects'] = $this->subject->findSubjectByTeacher(Auth::user()->id)->where('my_courses_id', $my_course_id);
        }
        $data['selected'] = true;
        $data['major'] = $this->major->findMajorByCourse($my_course_id);

        return view('pages.support_team.marks.manage', $data);
    }

    public function update(Request $req, $exam_id, $my_course_id, $class_id, $subject_id)
    {
        $p = ['exam_id' => $exam_id, 'my_course_id' => $my_course_id, 'class_id' => $class_id, 'subject_id' => $subject_id, 'year' => $this->year];

        $d = $d3 = $all_st_ids = [];

        $exam = $this->exam->find($exam_id);
        $marks = $this->mark->getMark($p);
        $major = $this->major->findMajorByCourse($my_course_id);

        $mks = $req->all();

        /** Test, Exam, Grade **/
        foreach($marks->sortBy('user.name') as $mk)
        {
            $all_st_ids[] = $mk->student_id;

                $d['t1'] = $t1 = $mks['t1_'.$mk->id];
                $d['t2'] = $t2 = $mks['t2_'.$mk->id];
                $d['tca'] = $tca = $t1 + $t2;
                $d['exm'] = $exm = $mks['exm_'.$mk->id];


            /** SubTotal Grade, Remark, Cum, CumAvg**/

            $d['tex'.$exam->term] = $total = $tca + $exm;

            if($total > 100){
                $d['tex'.$exam->term] = $d['t1'] = $d['t2'] = $d['t3'] = $d['t4'] = $d['tca'] = $d['exm'] = NULL;
            }


            $grade = $this->mark->getGrade($total, $major->id);
            $d['grade_id'] = $grade ? $grade->id : NULL;

            $this->mark->update($mk->id, $d);
        }

        /** Sub Position Begin  **/

        foreach($marks->sortBy('user.name') as $mk)
        {

            $d2['sub_pos'] = $this->mark->getSubPos($mk->student_id, $exam, $my_course_id, $subject_id, $this->year);

            $this->mark->update($mk->id, $d2);
        }

        /*Sub Position End*/

        /* Exam Record Update */

        unset( $p['subject_id'] );

        foreach ($all_st_ids as $st_id) {

            $p['student_id'] =$st_id;
            $d3['total'] = $this->mark->getExamTotalTerm($exam, $st_id, $my_course_id, $this->year);
            $d3['ave'] = $this->mark->getExamAvgTerm($exam, $st_id, $my_course_id, $class_id, $this->year);
            $d3['class_ave'] = $this->mark->getClassAvg($exam, $my_course_id, $this->year);
            $d3['pos'] = $this->mark->getPos($st_id, $exam, $my_course_id, $class_id, $this->year);

            $this->exam_record->update($p, $d3);
        }
        /*Exam Record End*/

       return JsonHelper::jsonUpdateOk();
    }

    public function commentUpdate(Request $req, $exr_id): \Illuminate\Http\JsonResponse
    {
        $d = CheckUsersHelper::userIsTeamSA() ? $req->only(['t_comment', 'p_comment']) : $req->only(['t_comment']);

        $this->exam_record->update(['id' => $exr_id], $d);
        return JsonHelper::jsonUpdateOk();
    }

    public function skillsUpdate(Request $req, $skill, $exr_id): \Illuminate\Http\JsonResponse
    {
        $d = [];
        if($skill == 'AF' || $skill == 'PS'){
            $sk = strtolower($skill);
            $d[$skill] = implode(',', $req->$sk);
        }

        $this->exam_record->update(['id' => $exr_id], $d);
        return JsonHelper::jsonUpdateOk();
    }

    public function bulk($my_course_id = NULL, $class_id = NULL)
    {
        $data['my_courses'] = $this->my_course->getAll();
        $data['selected'] = false;

        if($my_course_id && $class_id){
            $data['classes'] = $this->class->getAll()->where('my_course_id', $my_course_id);
            $data['students'] = $st = $this->student->getRecord(['my_course_id' => $my_course_id, 'class_id' => $class_id])->get()->sortBy('user.name');
            if($st->count() < 1){
                return redirect()->route('marks.bulk')->with('flash_danger', __('msg.srnf'));
            }
            $data['selected'] = true;
            $data['my_course_id'] = $my_course_id;
            $data['class_id'] = $class_id;
        }

        return view('pages.support_team.marks.bulk', $data);
    }

    public function bulkSelect(Request $req): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('marks.bulk', [$req->my_course_id, $req->class_id]);
    }

    public function tabulation($exam_id = NULL, $my_course_id = NULL, $class_id = NULL)
    {
        $data['my_courses'] = $this->my_course->getAll();
        $data['exams'] = $this->exam->getExam(['year' => $this->year]);
        $data['selected'] = FALSE;


        if($my_course_id && $exam_id && $class_id){

            $wh = ['my_course_id' => $my_course_id, 'class_id' => $class_id, 'exam_id' => $exam_id, 'year' => $this->year];

            $sub_ids = $this->mark->getSubjectIDs($wh);
            $st_ids = $this->mark->getStudentIDs($wh);

            if(count($sub_ids) < 1 OR count($st_ids) < 1) {
                return RouteHelper::goWithDanger('marks.tabulation', __('msg.srnf'));
            }

            $data['subjects'] = $this->subject->getSubjectsByIDs($sub_ids);
            $data['students'] = $this->student->getRecordByUserIDs($st_ids)->get()->sortBy('user.name');
            $data['classes'] = $this->class->getAll();

            $data['selected'] = TRUE;
            $data['my_course_id'] = $my_course_id;
            $data['class_id'] = $class_id;
            $data['exam_id'] = $exam_id;
            $data['year'] = $this->year;
            $data['marks'] = $mks = $this->mark->getMark($wh);
            $data['exr'] = $exr = $this->exam_record->getRecord($wh);

            $data['my_course'] = $mc = $this->my_course->find($my_course_id);
            $data['class']  = $this->class->find($class_id);
            $data['ex'] = $exam = $this->exam->find($exam_id);
            $data['tex'] = 'tex'.$exam->term;

        }

        return view('pages.support_team.marks.tabulation.index', $data);
    }

    public function printTabulation($exam_id, $my_course_id, $class_id)
    {
        $wh = ['my_course_id' => $my_course_id, 'class_id' => $class_id, 'exam_id' => $exam_id, 'year' => $this->year];

        $sub_ids = $this->mark->getSubjectIDs($wh);
        $st_ids = $this->mark->getStudentIDs($wh);

        if(count($sub_ids) < 1 OR count($st_ids) < 1) {
            return RouteHelper::goWithDanger('marks.tabulation', __('msg.srnf'));
        }

        $d['subjects'] = $this->subject->getSubjectsByIDs($sub_ids);
        $d['students'] = $this->student->getRecordByUserIDs($st_ids)->get()->sortBy('user.name');

        $d['my_course_id'] = $my_course_id;
        $d['exam_id'] = $exam_id;
        $d['year'] = $this->year;
        $wh = ['exam_id' => $exam_id, 'my_course_id' => $my_course_id];
        $d['marks'] = $mks = $this->mark->getMark($wh);
        $d['exr'] = $exr = $this->exam_record->getRecord($wh);

        $d['my_course'] = $mc = $this->my_course->find($my_course_id);
        $d['class']  = $this->class->find($class_id);
        $d['ex'] = $exam = $this->exam->find($exam_id);
        $d['tex'] = 'tex'.$exam->term;
        $d['s'] = Setting::all()->flatMap(function($s){
            return [$s->type => $s->description];
        });


        return view('pages.support_team.marks.tabulation.print', $d);
    }

    public function tabulationSelect(Request $req): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('marks.tabulation', [$req->exam_id, $req->my_course_id, $req->class_id]);
    }

    protected function verifyStudentExamYear($student_id, $year = null)
    {
        $years = $this->mark->getExamYears($student_id);
        $student_exists = $this->student->exists($student_id);

        if(!$year){
            if($student_exists && $years->count() > 0)
            {
                $d =['years' => $years, 'student_id' => DisplayMessageHelper::hash($student_id)];

                return view('pages.support_team.marks.select_year', $d);
            }

            return $this->noStudentRecord();
        }

        return $student_exists && $years->contains('year', $year);
    }

    protected function noStudentRecord(): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('dashboard')->with('flash_danger', __('msg.srnf'));
    }

    protected function checkPinVerified($st_id): bool
    {
        return Session::has('pin_verified') && Session::get('pin_verified') == $st_id;
    }

}
