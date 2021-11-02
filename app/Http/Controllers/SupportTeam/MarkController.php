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
use App\Repositories\ExamRepo;
use App\Repositories\MarkRepo;
use App\Repositories\MyCourseRepo;
use App\Http\Controllers\Controller;
use App\Repositories\StudentRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class MarkController extends Controller
{
    protected $my_course, $exam, $student, $year, $user, $mark;

    public function __construct(MyCourseRepo $my_course, ExamRepo $exam, StudentRepo $student, MarkRepo $mark)
    {
        $this->exam =  $exam;
        $this->mark =  $mark;
        $this->student =  $student;
        $this->my_course =  $my_course;
        $this->year =  GetSystemInfoHelper::getSetting('current_session');
    }

    public function index()
    {
        $d['exams'] = $this->exam->getExam(['year' => $this->year]);
        $d['my_classes'] = $this->my_course->all();
        $d['sections'] = $this->my_course->getAllSections();
        $d['subjects'] = $this->my_course->getAllSubjects();
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
        $d['marks'] = $this->exam->getMark($wh);
        $d['exam_records'] = $exr = $this->exam->getRecord($wh);
        $d['exams'] = $this->exam->getExam(['year' => $year]);
        $d['sr'] = $this->student->getRecord(['user_id' => $student_id])->first();
        $d['my_course'] = $mc = $this->my_course->getMC(['id' => $exr->first()->my_class_id])->first();
        $d['major'] = $this->my_course->findTypeByClass($mc->id);
        $d['subjects'] = $this->my_course->findSubjectByClass($mc->id);
        $d['year'] = $year;
        $d['student_id'] = $student_id;
        $d['skills'] = $this->exam->getSkillByClassType() ?: NULL;

        return view('pages.support_team.marks.show.index', $d);
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
        $d['marks'] = $mks = $this->exam->getMark($wh);
        $d['exr'] = $exr = $this->exam->getRecord($wh)->first();
        $d['my_course'] = $mc = $this->my_course->find($exr->my_class_id);
        $d['section_id'] = $exr->section_id;
        $d['ex'] = $exam = $this->exam->find($exam_id);
        $d['tex'] = 'tex'.$exam->term;
        $d['sr'] = $sr =$this->student->getRecord(['user_id' => $student_id])->first();
        $d['major'] = $this->my_course->findTypeByClass($mc->id);
        $d['subjects'] = $this->my_course->findSubjectByClass($mc->id);

        $d['major'] = $major = $d['major'];
        $d['year'] = $year;
        $d['student_id'] = $student_id;
        $d['exam_id'] = $exam_id;

        $d['skills'] = $this->exam->getSkillByClassType() ?: NULL;
        $d['s'] = Setting::all()->flatMap(function($s){
            return [$s->type => $s->description];
        });



        return view('pages.support_team.marks.print.index', $d);
    }

    public function selector(MarkSelector $req): \Illuminate\Http\RedirectResponse
    {
        $data = $req->only(['exam_id', 'my_course_id', 'section_id', 'subject_id']);
        $d2 = $req->only(['exam_id', 'my_course_id', 'section_id']);
        $d = $req->only(['my_course_id', 'section_id']);
        $d['session'] = $data['year'] = $d2['year'] = $this->year;

        $students = $this->student->getRecord($d)->get();
        if($students->count() < 1){
            return back()->with('pop_error', __('msg.rnf'));
        }

        foreach ($students as $s){
            $data['student_id'] = $d2['student_id'] = $s->user_id;
            $this->exam->createMark($data);
            $this->exam->createRecord($d2);
        }

        return redirect()->route('marks.manage', [$req->exam_id, $req->my_class_id, $req->section_id, $req->subject_id]);
    }

    public function manage($exam_id, $class_id, $section_id, $subject_id)
    {
        $d = ['exam_id' => $exam_id, 'my_course_id' => $class_id, 'section_id' => $section_id, 'subject_id' => $subject_id, 'year' => $this->year];

        $d['marks'] = $this->exam->getMark($d);
        if($d['marks']->count() < 1){
            return $this->noStudentRecord();
        }

        $d['m'] =  $d['marks']->first();
        $d['exams'] = $this->exam->all();
        $d['my_courses'] = $this->my_course->all();
        $d['sections'] = $this->my_course->getAllSections();
        $d['subjects'] = $this->my_course->getAllSubjects();
        if(GetUserTypeHelper::userIsTeacher()){
            $d['subjects'] = $this->my_course->findSubjectByTeacher(Auth::user()->id)->where('my_ourses_id', $class_id);
        }
        $d['selected'] = true;
        $d['major'] = $this->my_course->findTypeByClass($class_id);

        return view('pages.support_team.marks.manage', $d);
    }

    public function update(Request $req, $exam_id, $class_id, $section_id, $subject_id)
    {
        $p = ['exam_id' => $exam_id, 'my_course_id' => $class_id, 'section_id' => $section_id, 'subject_id' => $subject_id, 'year' => $this->year];

        $d = $d3 = $all_st_ids = [];

        $exam = $this->exam->find($exam_id);
        $marks = $this->exam->getMark($p);
        $major = $this->my_course->findTypeByClass($class_id);

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

            $this->exam->updateMark($mk->id, $d);
        }

        /** Sub Position Begin  **/

        foreach($marks->sortBy('user.name') as $mk)
        {

            $d2['sub_pos'] = $this->mark->getSubPos($mk->student_id, $exam, $class_id, $subject_id, $this->year);

            $this->exam->updateMark($mk->id, $d2);
        }

        /*Sub Position End*/

        /* Exam Record Update */

        unset( $p['subject_id'] );

        foreach ($all_st_ids as $st_id) {

            $p['student_id'] =$st_id;
            $d3['total'] = $this->mark->getExamTotalTerm($exam, $st_id, $class_id, $this->year);
            $d3['ave'] = $this->mark->getExamAvgTerm($exam, $st_id, $class_id, $section_id, $this->year);
            $d3['class_ave'] = $this->mark->getClassAvg($exam, $class_id, $this->year);
            $d3['pos'] = $this->mark->getPos($st_id, $exam, $class_id, $section_id, $this->year);

            $this->exam->updateRecord($p, $d3);
        }
        /*Exam Record End*/

       return JsonHelper::jsonUpdateOk();
    }

//    public function batch_fix()
//    {
//        $d['exams'] = $this->exam->getExam(['year' => $this->year]);
//        $d['my_classes'] = $this->my_class->all();
//        $d['sections'] = $this->my_class->getAllSections();
//        $d['selected'] = false;
//
//        return view('pages.support_team.marks.batch_fix', $d);
//    }
//
//    public function batch_update(Request $req): \Illuminate\Http\JsonResponse
//    {
//        $exam_id = $req->exam_id;
//        $class_id = $req->my_class_id;
//        $section_id = $req->section_id;
//
//        $w = ['exam_id' => $exam_id, 'my_course_id' => $class_id, 'section_id' => $section_id, 'year' => $this->year];
//
//        $exam = $this->exam->find($exam_id);
//        $exrs = $this->exam->getRecord($w);
//        $marks = $this->exam->getMark($w);
//
//        /** Marks Fix Begin **/
//
//        $class_type = $this->my_class->findTypeByClass($class_id);
//        $tex = 'tex'.$exam->term;
//
//        foreach($marks as $mk){
//
//            $total = $mk->$tex;
//            $d['grade_id'] = $this->mark->getGrade($total, $class_type->id);
//
//            /*      if($exam->term == 3){
//                      $d['cum'] = $this->mark->getSubCumTotal($total, $mk->student_id, $mk->subject_id, $class_id, $this->year);
//                      $d['cum_ave'] = $cav = $this->mark->getSubCumAvg($total, $mk->student_id, $mk->subject_id, $class_id, $this->year);
//                      $grade = $this->mark->getGrade(round($mk->cum_ave), $class_type->id);
//                  }*/
//
//            $this->exam->updateMark($mk->id, $d);
//        }
//
//        /* Marks Fix End*/
//
//        /** Exam Record Update  **/
//        foreach($exrs as $exr){
//
//            $st_id = $exr->student_id;
//
//            $d3['total'] = $this->mark->getExamTotalTerm($exam, $st_id, $class_id, $this->year);
//            $d3['ave'] = $this->mark->getExamAvgTerm($exam, $st_id, $class_id, $section_id, $this->year);
//            $d3['class_ave'] = $this->mark->getClassAvg($exam, $class_id, $this->year);
//            $d3['pos'] = $this->mark->getPos($st_id, $exam, $class_id, $section_id, $this->year);
//
//            $this->exam->updateRecord(['id' => $exr->id], $d3);
//        }
//
//        /** END Exam Record Update END **/
//
//        return Qs::jsonUpdateOk();
//    }

    public function commentUpdate(Request $req, $exr_id): \Illuminate\Http\JsonResponse
    {
        $d = CheckUsersHelper::userIsTeamSA() ? $req->only(['t_comment', 'p_comment']) : $req->only(['t_comment']);

        $this->exam->updateRecord(['id' => $exr_id], $d);
        return JsonHelper::jsonUpdateOk();
    }

    public function skillsUpdate(Request $req, $skill, $exr_id): \Illuminate\Http\JsonResponse
    {
        $d = [];
        if($skill == 'AF' || $skill == 'PS'){
            $sk = strtolower($skill);
            $d[$skill] = implode(',', $req->$sk);
        }

        $this->exam->updateRecord(['id' => $exr_id], $d);
        return JsonHelper::jsonUpdateOk();
    }

    public function bulk($class_id = NULL, $section_id = NULL)
    {
        $d['my_classes'] = $this->my_course->all();
        $d['selected'] = false;

        if($class_id && $section_id){
            $d['sections'] = $this->my_course->getAllSections()->where('my_course_id', $class_id);
            $d['students'] = $st = $this->student->getRecord(['my_course_id' => $class_id, 'section_id' => $section_id])->get()->sortBy('user.name');
            if($st->count() < 1){
                return redirect()->route('marks.bulk')->with('flash_danger', __('msg.srnf'));
            }
            $d['selected'] = true;
            $d['my_course_id'] = $class_id;
            $d['section_id'] = $section_id;
        }

        return view('pages.support_team.marks.bulk', $d);
    }

    public function bulkSelect(Request $req): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('marks.bulk', [$req->my_class_id, $req->section_id]);
    }

    public function tabulation($exam_id = NULL, $course_id = NULL, $section_id = NULL)
    {
        $d['my_classes'] = $this->my_course->all();
        $d['exams'] = $this->exam->getExam(['year' => $this->year]);
        $d['selected'] = FALSE;

        if($course_id && $exam_id && $section_id){

            $wh = ['my_course_id' => $course_id, 'section_id' => $section_id, 'exam_id' => $exam_id, 'year' => $this->year];

            $sub_ids = $this->mark->getSubjectIDs($wh);
            $st_ids = $this->mark->getStudentIDs($wh);

            if(count($sub_ids) < 1 OR count($st_ids) < 1) {
                return RouteHelper::goWithDanger('marks.tabulation', __('msg.srnf'));
            }

            $d['subjects'] = $this->my_course->getSubjectsByIDs($sub_ids);
            $d['students'] = $this->student->getRecordByUserIDs($st_ids)->get()->sortBy('user.name');
            $d['sections'] = $this->my_course->getAllSections();

            $d['selected'] = TRUE;
            $d['my_course_id'] = $course_id;
            $d['section_id'] = $section_id;
            $d['exam_id'] = $exam_id;
            $d['year'] = $this->year;
            $d['marks'] = $mks = $this->exam->getMark($wh);
            $d['exr'] = $exr = $this->exam->getRecord($wh);

            $d['my_course'] = $mc = $this->my_course->find($course_id);
            $d['section']  = $this->my_course->findSection($section_id);
            $d['ex'] = $exam = $this->exam->find($exam_id);
            $d['tex'] = 'tex'.$exam->term;

        }

        return view('pages.support_team.marks.tabulation.index', $d);
    }

    public function printTabulation($exam_id, $course_id, $section_id)
    {
        $wh = ['my_course_id' => $course_id, 'section_id' => $section_id, 'exam_id' => $exam_id, 'year' => $this->year];

        $sub_ids = $this->mark->getSubjectIDs($wh);
        $st_ids = $this->mark->getStudentIDs($wh);

        if(count($sub_ids) < 1 OR count($st_ids) < 1) {
            return RouteHelper::goWithDanger('marks.tabulation', __('msg.srnf'));
        }

        $d['subjects'] = $this->my_course->getSubjectsByIDs($sub_ids);
        $d['students'] = $this->student->getRecordByUserIDs($st_ids)->get()->sortBy('user.name');

        $d['my_course_id'] = $course_id;
        $d['exam_id'] = $exam_id;
        $d['year'] = $this->year;
        $wh = ['exam_id' => $exam_id, 'my_course_id' => $course_id];
        $d['marks'] = $mks = $this->exam->getMark($wh);
        $d['exr'] = $exr = $this->exam->getRecord($wh);

        $d['my_course'] = $mc = $this->my_course->find($course_id);
        $d['section']  = $this->my_course->findSection($section_id);
        $d['ex'] = $exam = $this->exam->find($exam_id);
        $d['tex'] = 'tex'.$exam->term;
        $d['s'] = Setting::all()->flatMap(function($s){
            return [$s->type => $s->description];
        });


        return view('pages.support_team.marks.tabulation.print', $d);
    }

    public function tabulationSelect(Request $req): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('marks.tabulation', [$req->exam_id, $req->my_course_id, $req->section_id]);
    }

    protected function verifyStudentExamYear($student_id, $year = null)
    {
        $years = $this->exam->getExamYears($student_id);
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
