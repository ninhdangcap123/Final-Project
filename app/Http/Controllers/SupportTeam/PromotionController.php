<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\GetSystemInfoHelper;
use App\Helpers\JsonHelper;
use App\Helpers\Qs;
use App\Http\Controllers\Controller;
use App\Models\Mark;
use App\Repositories\Classes\ClassesRepositoryInterface;
use App\Repositories\MyCourse\MyCourseRepositoryInterface;
use App\Repositories\MyCourseRepo;
use App\Repositories\Promotion\PromotionRepositoryInterface;
use App\Repositories\Student\StudentRepositoryInterface;
use App\Repositories\StudentRepo;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    protected $my_course, $student, $class, $promotion;

    public function __construct(MyCourseRepositoryInterface $my_course, PromotionRepositoryInterface $promotion, ClassesRepositoryInterface $class,
                                StudentRepositoryInterface $student)
    {
        $this->middleware('teamSA');

        $this->my_course = $my_course;
        $this->student = $student;
        $this->class = $class;
        $this->promotion = $promotion;
    }

    public function promotion($fc = NULL, $fs = NULL, $tc = NULL, $ts = NULL)
    {
        $data['old_year'] = $old_yr = GetSystemInfoHelper::getSetting('current_session');
        $old_yr = explode('-', $old_yr);
        $data['new_year'] = ++$old_yr[0].'-'.++$old_yr[1];
        $data['my_courses'] = $this->my_course->getAll();
        $data['classes'] = $this->class->getAll();
        $data['selected'] = false;

        if($fc && $fs && $tc && $ts){
            $data['selected'] = true;
            $data['fc'] = $fc;
            $data['fs'] = $fs;
            $data['tc'] = $tc;
            $data['ts'] = $ts;
            $data['students'] = $sts = $this->student->getRecord(['my_course_id' => $fc, 'class_id' => $fs, 'session' => $data['old_year']])->get();

            if($sts->count() < 1){
                return redirect()->route('students.promotion')->with('flash_success', __('msg.nstp'));
            }
        }

        return view('pages.support_team.students.promotion.index', $data);
    }

    public function selector(Request $req)
    {
        return redirect()->route('students.promotion', [$req->fc, $req->fs, $req->tc, $req->ts]);
    }

    public function promote(Request $req, $fc, $fs, $tc, $ts)
    {
        $oy = GetSystemInfoHelper::getSetting('current_session'); $data = [];
        $old_yr = explode('-', $oy);
        $ny = ++$old_yr[0].'-'.++$old_yr[1];
        $students = $this->student->getRecord(['my_course_id' => $fc, 'class_id' => $fs, 'session' => $oy ])->get()->sortBy('user.name');

        if($students->count() < 1){
            return redirect()->route('students.promotion')->with('flash_danger', __('msg.srnf'));
        }

        foreach($students as $st){
            $p = 'p-'.$st->id;
            $p = $req->$p;
            if($p === 'P'){ // Promote
                $data['my_course_id'] = $tc;
                $data['class_id'] = $ts;
                $data['session'] = $ny;
            }
            if($p === 'D'){ // Don't Promote
                $data['my_course_id'] = $fc;
                $data['class_id'] = $fs;
                $data['session'] = $ny;
            }
            if($p === 'G'){ // Graduated
                $data['my_course_id'] = $fc;
                $data['class_id'] = $fs;
                $data['grad'] = 1;
                $data['grad_date'] = $oy;
            }

            $this->student->updateRecord($st->id, $data);

//            Insert New Promotion Data
            $promote['from_course'] = $fc;
            $promote['from_section'] = $fs;
            $promote['grad'] = ($p === 'G') ? 1 : 0;
            $promote['to_course'] = in_array($p, ['D', 'G']) ? $fc : $tc;
            $promote['to_section'] = in_array($p, ['D', 'G']) ? $fs : $ts;
            $promote['student_id'] = $st->user_id;
            $promote['from_session'] = $oy;
            $promote['to_session'] = $ny;
            $promote['status'] = $p;

            $this->promotion->create($promote);
        }
        return redirect()->route('students.promotion')->with('flash_success', __('msg.update_ok'));
    }

    public function manage()
    {
        $data['promotions'] = $this->promotion->getAll();
        $data['old_year'] = GetSystemInfoHelper::getCurrentSession();
        $data['new_year'] = GetSystemInfoHelper::getNextSession();

        return view('pages.support_team.students.promotion.reset', $data);
    }

    public function reset($promotion_id)
    {
        $this->resetSingle($promotion_id);

        return redirect()->route('students.promotion_manage')->with('flash_success', __('msg.update_ok'));
    }

    public function resetAll(): \Illuminate\Http\JsonResponse
    {
        $next_session = GetSystemInfoHelper::getNextSession();
        $where = ['from_session' => GetSystemInfoHelper::getCurrentSession(), 'to_session' => $next_session];
        $proms = $this->promotion->getPromotions($where);

        if ($proms->count()){
          foreach ($proms as $prom){
              $this->resetSingle($prom->id);
              $this->deleteOldMarks($prom->student_id, $next_session);
          }
        }

        return JsonHelper::jsonUpdateOk();
    }

    protected function deleteOldMarks($student_id, $year)
    {
        Mark::where(['student_id' => $student_id, 'year' => $year])->delete();
    }

    protected function resetSingle($promotion_id)
    {
        $prom = $this->promotion->find($promotion_id);

        $data['my_course_id'] = $prom->from_course;
        $data['class_id'] = $prom->from_section;
        $data['session'] = $prom->from_session;
        $data['grad'] = 0;
        $data['grad_date'] = null;

        $this->student->update(['user_id' => $prom->student_id], $data);

        return $this->promotion->delete($promotion_id);
    }
}
