<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\CheckExamInfoHelper;
use App\Helpers\CheckUsersHelper;
use App\Helpers\DisplayMessageHelper;
use App\Helpers\GetSystemInfoHelper;
use App\Helpers\GetUserTypeHelper;
use App\Helpers\JsonHelper;
use App\Helpers\RouteHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Mark\MarkSelector;
use App\Http\Requests\Mark\MarkUpdate;
use App\Models\Setting;
use App\Repositories\Classes\ClassesRepositoryInterface;
use App\Repositories\Exam\ExamRepositoryInterface;
use App\Repositories\ExamRecord\ExamRecordRepositoryInterface;
use App\Repositories\ExamRepo;
use App\Repositories\Grade\GradeRepositoryInterface;
use App\Repositories\Major\MajorRepositoryInterface;
use App\Repositories\Mark\MarkRepositoryInterface;
use App\Repositories\MarkRepo;
use App\Repositories\MyCourse\MyCourseRepositoryInterface;
use App\Repositories\MyCourseRepo;
use App\Repositories\Skill\SkillRepositoryInterface;
use App\Repositories\Student\StudentRepositoryInterface;
use App\Repositories\StudentRepo;
use App\Repositories\Subject\SubjectRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class MarkController extends Controller
{
    protected $myCourseRepo;
    protected $examRecordRepo;
    protected $skillRepo;
    protected $classRepo;
    protected $examRepo;
    protected $studentRepo;
    protected $year;
//    protected $user;
    protected $markRepo;
    protected $subjectRepo;
    protected $majorRepo;
    protected $gradeRepo;

    public function __construct(
        ExamRecordRepositoryInterface $examRecordRepo,
        SkillRepositoryInterface      $skillRepo,
        MyCourseRepositoryInterface   $myCourseRepo,
        ClassesRepositoryInterface    $classRepo,
        SubjectRepositoryInterface    $subjectRepo,
        ExamRepositoryInterface       $examRepo,
        StudentRepositoryInterface    $studentRepo,
        MarkRepositoryInterface       $markRepo,
        MajorRepositoryInterface      $majorRepo,
        GradeRepositoryInterface      $gradeRepo
    )
    {
        $this->examRepo = $examRepo;
        $this->markRepo = $markRepo;
        $this->gradeRepo = $gradeRepo;
        $this->subjectRepo = $subjectRepo;
        $this->studentRepo = $studentRepo;
        $this->myCourseRepo = $myCourseRepo;
        $this->classRepo = $classRepo;
        $this->majorRepo = $majorRepo;
        $this->skillRepo = $skillRepo;
        $this->examRecordRepo = $examRecordRepo;
        $this->year = GetSystemInfoHelper::getSetting('current_session');
    }

    public function index()
    {
        $data['exams'] = $this->examRepo->getExam([ 'year' => $this->year ]);
        $data['my_courses'] = $this->myCourseRepo->getAll();
        $data['classes'] = $this->classRepo->getAll();
        $data['subjects'] = $this->subjectRepo->getAll();
        $data['selected'] = false;

        return view('pages.support_team.marks.index', $data);
    }

    public function yearSelector($student_id)
    {
        return $this->verifyStudentExamYear($student_id);
    }

    protected function verifyStudentExamYear($student_id, $year = null)
    {
        $years = $this->markRepo->getExamYears($student_id);
        $studentExists = $this->studentRepo->exists($student_id);

        if( !$year ) {
            if( $studentExists && $years->count() > 0 ) {
                $data = [ 'years' => $years, 'student_id' => DisplayMessageHelper::hash($student_id) ];

                return view('pages.support_team.marks.select_year', $data);
            }

            return $this->noStudentRecord();
        }

        return $studentExists && $years->contains('year', $year);
    }

    protected function noStudentRecord(): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('dashboard')->with('flash_danger', __('msg.srnf'));
    }

    public function yearSelected(Request $request, $student_id): \Illuminate\Http\RedirectResponse
    {
        if( !$this->verifyStudentExamYear($student_id, $request->year) ) {
            return $this->noStudentRecord();
        }
        $student_id = DisplayMessageHelper::hash($student_id);
        return redirect()->route('marks.show', [ $student_id, $request->year ]);
    }

    public function show($student_id, $year)
    {
        /* Prevent Other Students/Parents from viewing Result of others */
        if( Auth::user()->id != $student_id && !CheckUsersHelper::userIsTeamSAT() && !CheckUsersHelper::userIsMyChild($student_id, Auth::user()->id) ) {
            return redirect(route('dashboard'))->with('pop_error', __('msg.denied'));
        }
        if( CheckExamInfoHelper::examIsLocked() && !CheckUsersHelper::userIsTeamSA() ) {
            Session::put('marks_url', route('marks.show', [ DisplayMessageHelper::hash($student_id), $year ]));

            if( !$this->checkPinVerified($student_id) ) {
                return redirect()->route('pins.enter', DisplayMessageHelper::hash($student_id));
            }
        }
        if( !$this->verifyStudentExamYear($student_id, $year) ) {
            return $this->noStudentRecord();
        }

        $where = [
            'student_id' => $student_id,
            'year' => $year
        ];
        $data['marks'] = $this->markRepo->getMark($where);
        $data['exam_records'] = $exr = $this->examRecordRepo->getRecord($where);
        $data['exams'] = $this->examRepo->getExam([ 'year' => $year ]);
        $data['sr'] = $this->studentRepo->getRecord([ 'user_id' => $student_id ])->first();
        $data['my_course'] = $mc = $this->myCourseRepo->getMC([ 'id' => $exr->first()->my_course_id ])->first();
        $data['major'] = $this->majorRepo->findMajorByCourse($mc->id);
        $data['subjects'] = $this->subjectRepo->findSubjectByCourse($mc->id);
        $data['year'] = $year;
        $data['student_id'] = $student_id;
        $data['skills'] = $this->skillRepo->getSkillByMajor() ?: NULL;

        return view('pages.support_team.marks.show.index', $data);
    }

    protected function checkPinVerified($st_id): bool
    {
        return Session::has('pin_verified') && Session::get('pin_verified') == $st_id;
    }

    public function printView($student_id, $exam_id, $year)
    {
        /* Prevent Other Students/Parents from viewing Result of others */
        if( Auth::user()->id != $student_id && !CheckUsersHelper::userIsTeamSA() && !CheckUsersHelper::userIsMyChild($student_id, Auth::user()->id) ) {
            return redirect(route('dashboard'))->with('pop_error', __('msg.denied'));
        }

        if( CheckExamInfoHelper::examIsLocked() && !CheckUsersHelper::userIsTeamSA() ) {
            Session::put('marks_url', route('marks.show', [ DisplayMessageHelper::hash($student_id), $year ]));

            if( !$this->checkPinVerified($student_id) ) {
                return redirect()->route('pins.enter', DisplayMessageHelper::hash($student_id));
            }
        }

        if( !$this->verifyStudentExamYear($student_id, $year) ) {
            return $this->noStudentRecord();
        }

        $where = [
            'student_id' => $student_id,
            'exam_id' => $exam_id,
            'year' => $year
        ];
        $data['marks'] = $marks = $this->markRepo->getMark($where);
        $data['exr'] = $examRecord = $this->examRecordRepo->getRecord($where)->first();
        $data['my_course'] = $myCourse = $this->myCourseRepo->find($examRecord->my_course_id);
        $data['class_id'] = $examRecord->class_id;
        $data['ex'] = $exam = $this->examRepo->find($exam_id);
        $data['tex'] = 'tex'.$exam->term;
        $data['sr'] = $sr = $this->studentRepo->getRecord([ 'user_id' => $student_id ])->first();
        $data['major'] = $this->majorRepo->findMajorByCourse($myCourse->id);
        $data['subjects'] = $this->subjectRepo->findSubjectByCourse($myCourse->id);

        $data['major'] = $major = $data['major'];
        $data['year'] = $year;
        $data['student_id'] = $student_id;
        $data['exam_id'] = $exam_id;

        $data['skills'] = $this->skillRepo->getSkillByMajor() ?: NULL;
        $data['s'] = Setting::all()->flatMap(function ($s) {
            return [ $s->type => $s->description ];
        });

        return view('pages.support_team.marks.print.index', $data);
    }

    public function selector(MarkSelector $request): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validated();
        $data2 = $request->only([ 'exam_id', 'my_course_id', 'class_id' ]);
        $data3 = $request->only([ 'my_course_id', 'class_id' ]);
        $data3['session'] = $data['year'] = $data2['year'] = $this->year;

        $students = $this->studentRepo->getRecord($data3)->get();

        if( $students->count() < 1 ) {
            return back()->with('pop_error', __('msg.rnf'));
        }

        foreach( $students as $student ) {
            $data['student_id'] = $data2['student_id'] = $student->user_id;

            $this->markRepo->create($data);
            $this->examRecordRepo->create($data2);
        }

        return redirect()->route('marks.manage', [ $request->exam_id, $request->my_course_id, $request->class_id, $request->subject_id ]);
    }

    public function manage($exam_id, $my_course_id, $class_id, $subject_id)
    {
        $data = [
            'exam_id' => $exam_id,
            'my_course_id' => $my_course_id,
            'class_id' => $class_id,
            'subject_id' => $subject_id,
            'year' => $this->year
        ];

        $data['marks'] = $this->markRepo->getMark($data);
        if( $data['marks']->count() < 1 ) {
            return $this->noStudentRecord();
        }

        $data['m'] = $data['marks']->first();
        $data['exams'] = $this->examRepo->getAll();
        $data['my_courses'] = $this->myCourseRepo->getAll();
        $data['classes'] = $this->classRepo->getAll();
        $data['subjects'] = $this->subjectRepo->getAll();
        $data['selected'] = true;
        $data['major'] = $this->majorRepo->findMajorByCourse($my_course_id);

        return view('pages.support_team.marks.manage', $data);
    }


    public function update(MarkUpdate $request, $exam_id, $my_course_id, $class_id, $subject_id)
    {

        $input = [
            'exam_id' => $exam_id,
            'my_course_id' => $my_course_id,
            'class_id' => $class_id,
            'subject_id' => $subject_id,
            'year' => $this->year
        ];

        $data = $data3 = $all_student_ids = [];
        $findExam = $this->examRepo->find($exam_id);
        $marks = $this->markRepo->getMark($input);
        $major = $this->majorRepo->findMajorByCourse($my_course_id);
        $allMarks = $request->all();

        /** Test, Exam, Grade **/

        foreach( $marks->sortBy('studentRecord.user_id') as $mark )
        {
            $all_student_ids[] = $mark->student_id;
            $data['t1'] = $t1 = $allMarks['t1_'.$mark->id];
            $data['t2'] = $t2 = $allMarks['t2_'.$mark->id];
            $data['exm'] = $exam = $allMarks['exm_'.$mark->id];
            /** SubTotal Grade, Remark, Cum, CumAvg**/
            $data['tex'.$findExam->term] = $total = $t1 + $t2 + $exam;

            $grade = $this->gradeRepo->getGrade($total, $major->id);
            $data['grade_id'] = $grade ? $grade->id : NULL;
            $this->markRepo->update($mark->id, $data);



        }

        unset($input['subject_id']);
        foreach( $all_student_ids as $student_id ) {

            $input['student_id'] = $student_id;
            $data3['total'] = $this->markRepo->getExamTotalTerm($findExam, $student_id, $my_course_id, $this->year);
            $data3['ave'] = $this->markRepo->getExamAverageTerm($findExam, $student_id, $my_course_id, $class_id, $this->year);
            $data3['class_ave'] = $this->markRepo->getClassAverage($findExam, $my_course_id, $this->year);


            $this->examRecordRepo->updateRecord($input, $data3);
        }



        return JsonHelper::jsonUpdateSuccess();
    }

    public function commentUpdate(Request $request, $exr_id): \Illuminate\Http\JsonResponse
    {
        $data = CheckUsersHelper::userIsTeamSA() ? $request->only([ 't_comment', 'p_comment' ]) : $request->only([ 't_comment' ]);
        $this->examRecordRepo->updateRecord([ 'id' => $exr_id ], $data);
        return JsonHelper::jsonUpdateSuccess();
    }

    public function skillsUpdate(Request $request, $skill, $exr_id): \Illuminate\Http\JsonResponse
    {
        $data = [];
        if( $skill == 'AF' || $skill == 'PS' ) {
            $skills = strtolower($skill);
            $data[$skill] = implode(',', $request->$skills);
        }
        $this->examRecordRepo->updateRecord([ 'id' => $exr_id ], $data);
        return JsonHelper::jsonUpdateSuccess();
    }

    public function bulk($my_course_id = NULL, $class_id = NULL)
    {
        $data['my_courses'] = $this->myCourseRepo->getAll();
        $data['selected'] = false;
        if( $my_course_id && $class_id ) {
            $data['classes'] = $this->classRepo->where($my_course_id);
            $data['students'] = $student = $this->studentRepo->getRecord([
                'my_course_id' => $my_course_id,
                'class_id' => $class_id
            ])->get()->sortBy('user.name');
            if( $student->count() < 1 ) {
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
        return redirect()->route('marks.bulk', [ $req->my_course_id, $req->class_id ]);
    }

    public function tabulation($exam_id = NULL, $my_course_id = NULL, $class_id = NULL)
    {
        $data['my_courses'] = $this->myCourseRepo->getAll();
        $data['exams'] = $this->examRepo->getExam([ 'year' => $this->year ]);
        $data['selected'] = FALSE;

        if( $my_course_id && $exam_id && $class_id ) {

            $where = [
                'my_course_id' => $my_course_id,
                'class_id' => $class_id,
                'exam_id' => $exam_id,
                'year' => $this->year
            ];

            $subjectIDs = $this->markRepo->getSubjectIDs($where);
            $studentIDs = $this->markRepo->getStudentIDs($where);

            if( count($subjectIDs) < 1 or count($studentIDs) < 1 ) {
                return RouteHelper::goWithDanger('marks.tabulation', __('msg.srnf'));
            }

            $data['subjects'] = $this->subjectRepo->getSubjectsByIDs($subjectIDs);
            $data['students'] = $this->studentRepo->getRecordByUserIDs($studentIDs);
            $data['classes'] = $this->classRepo->getAll();

            $data['selected'] = TRUE;
            $data['my_course_id'] = $my_course_id;
            $data['class_id'] = $class_id;
            $data['exam_id'] = $exam_id;
            $data['year'] = $this->year;
            $data['marks'] = $this->markRepo->getMark($where);
            $data['exr'] = $this->examRecordRepo->getRecord($where);

            $data['my_course'] = $mc = $this->myCourseRepo->find($my_course_id);
            $data['class'] = $this->classRepo->find($class_id);
            $data['ex'] = $exam = $this->examRepo->find($exam_id);
            $data['tex'] = 'tex'.$exam->term;
        }
        return view('pages.support_team.marks.tabulation.index', $data);
    }

    public function printTabulation($exam_id, $my_course_id, $class_id)
    {
        $where = [
            'my_course_id' => $my_course_id,
            'class_id' => $class_id,
            'exam_id' => $exam_id,
            'year' => $this->year
        ];

        $subjectIDs = $this->markRepo->getSubjectIDs($where);
        $studentIDs = $this->markRepo->getStudentIDs($where);

        if( count($subjectIDs) < 1 or count($studentIDs) < 1 ) {
            return RouteHelper::goWithDanger('marks.tabulation', __('msg.srnf'));
        }

        $data['subjects'] = $this->subjectRepo->getSubjectsByIDs($subjectIDs);
        $data['students'] = $this->studentRepo->getRecordByUserIDs($studentIDs);

        $data['my_course_id'] = $my_course_id;
        $data['exam_id'] = $exam_id;
        $data['year'] = $this->year;
        $where = [ 'exam_id' => $exam_id, 'my_course_id' => $my_course_id ];
        $data['marks'] = $this->markRepo->getMark($where);
        $data['exr'] = $this->examRecordRepo->getRecord($where);

        $data['my_course'] = $this->myCourseRepo->find($my_course_id);
        $data['class'] = $this->classRepo->find($class_id);
        $data['ex'] = $exam = $this->examRepo->find($exam_id);
        $data['tex'] = 'tex'.$exam->term;
        $data['s'] = Setting::all()->flatMap(function ($s) {
            return [ $s->type => $s->description ];
        });
        return view('pages.support_team.marks.tabulation.print', $data);
    }

    public function tabulationSelect(Request $request): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('marks.tabulation', [ $request->exam_id, $request->my_course_id, $request->class_id ]);
    }

}
