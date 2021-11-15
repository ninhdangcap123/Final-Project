<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\GetSystemInfoHelper;
use App\Helpers\JsonHelper;
use App\Http\Controllers\Controller;
use App\Models\Mark;
use App\Models\StudentRecord;
use App\Repositories\Classes\ClassesRepositoryInterface;
use App\Repositories\MyCourse\MyCourseRepositoryInterface;
use App\Repositories\MyCourseRepo;
use App\Repositories\Promotion\PromotionRepositoryInterface;
use App\Repositories\Student\StudentRepositoryInterface;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    protected $myCourseRepo;
    protected $studentRepo;
    protected $classRepo;
    protected $promotionRepo;

    public function __construct(
        MyCourseRepositoryInterface  $myCourseRepo,
        PromotionRepositoryInterface $promotionRepo,
        ClassesRepositoryInterface   $classRepo,
        StudentRepositoryInterface   $studentRepo
    )
    {
        $this->middleware('teamSA');
        $this->myCourseRepo = $myCourseRepo;
        $this->studentRepo = $studentRepo;
        $this->classRepo = $classRepo;
        $this->promotionRepo = $promotionRepo;
    }

    public function promotion($fromCourse = NULL, $fromSection = NULL, $toCourse = NULL, $toSection = NULL)
    {
        $data['old_year'] = $oldYear = GetSystemInfoHelper::getSetting('current_session');
        $oldYear = explode('-', $oldYear);
        $data['new_year'] = ++$oldYear[0].'-'.++$oldYear[1];
        $data['my_courses'] = $this->myCourseRepo->getAll();
        $data['classes'] = $this->classRepo->getAll();
        $data['selected'] = false;

        if( $fromCourse && $fromSection && $toCourse && $toSection ) {
            $data['selected'] = true;
            $data['fromCourse'] = $fromCourse;
            $data['fromSection'] = $fromSection;
            $data['toCourse'] = $toCourse;
            $data['toSection'] = $toSection;
            $data['students'] = $students = $this->studentRepo->getRecord([
                'my_course_id' => $fromCourse,
                'class_id' => $fromSection,
                'session' => $data['old_year']
            ])->get();

            if( $students->count() < 1 ) {
                return redirect()->route('students.promotion')->with('flash_success', __('msg.nstp'));
            }
        }

        return view('pages.support_team.students.promotion.index', $data);
    }

    public function selector(Request $request)
    {
        return redirect()->route('students.promotion', [
            $request->fromCourse,
            $request->fromSection,
            $request->toCourse,
            $request->toSection
        ]);
    }

    public function promote(Request $request, $fromCourse, $fromSection, $toCourse, $toSection)
    {
        $oldSession = GetSystemInfoHelper::getSetting('current_session');
        $data = [];
        $oldYear = explode('-', $oldSession);
        $newYear = ++$oldYear[0].'-'.++$oldYear[1];
        $students = $this->studentRepo->getRecord([
            'my_course_id' => $fromCourse,
            'class_id' => $fromSection,
            'session' => $oldSession
        ])->get()->sortBy('user.name');

        if( $students->count() < 1 ) {
            return redirect()->route('students.promotion')->with('flash_danger', __('msg.srnf'));
        }

        foreach( $students as $student ) {
            $promote = 'p-'.$student->id;
            $promote = $request->$promote;
            if( $promote === 'P' ) { // Promote
                $data['my_course_id'] = $toCourse;
                $data['class_id'] = $toSection;
                $data['session'] = $newYear;
            }
            if( $promote === 'D' ) { // Don't Promote
                $data['my_course_id'] = $fromCourse;
                $data['class_id'] = $fromSection;
                $data['session'] = $newYear;
            }
            if( $promote === 'G' ) { // Graduated
                $data['my_course_id'] = $fromCourse;
                $data['class_id'] = $fromSection;
                $data['grad'] = 1;
                $data['grad_date'] = $oldSession;
            }

            $this->studentRepo->updateRecord($student->id, $data);

//            Insert New Promotion Data
            $promotes['from_course'] = $fromCourse;
            $promotes['from_section'] = $fromSection;
            $promotes['grad'] = ( $promote === 'G' ) ? 1 : 0;
            $promotes['to_course'] = in_array($promote, [ 'D', 'G' ]) ? $fromCourse : $toCourse;
            $promotes['to_section'] = in_array($promote, [ 'D', 'G' ]) ? $fromSection : $toSection;
            $promotes['student_id'] = $student->user_id;
            $promotes['from_session'] = $oldSession;
            $promotes['to_session'] = $newYear;
            $promotes['status'] = $promote;

            $this->promotionRepo->create($promotes);
        }
        return redirect()->route('students.promotion')->with('flash_success', __('msg.update_ok'));
    }

    public function manage()
    {
        $data['promotions'] = $this->promotionRepo->getAll();
        $data['old_year'] = GetSystemInfoHelper::getCurrentSession();
        $data['new_year'] = GetSystemInfoHelper::getNextSession();

        return view('pages.support_team.students.promotion.reset', $data);
    }

    public function reset($promotion_id)
    {

        $this->resetSingle($promotion_id);

        return redirect()->route('students.promotion_manage')->with('flash_success', __('msg.update_ok'));
    }

    protected function resetSingle($promotion_id)
    {
        $promotion = $this->promotionRepo->find($promotion_id);

        $data['my_course_id'] = $promotion->from_course;
        $data['class_id'] = $promotion->from_section;
        $data['session'] = $promotion->from_session;
        $data['grad'] = 0;
        $data['grad_date'] = null;


        $this->studentRepo->updateStudent(['user_id'=> $promotion->student_id] , $data);
//        $new = $this->studentRepo->where($promotion->student_id)->toArray();
//        $this->studentRepo->update($new[0]['id'], $data);

        return $this->promotionRepo->delete($promotion_id);
    }

    public function resetAll(): \Illuminate\Http\JsonResponse
    {
        $nextSession = GetSystemInfoHelper::getNextSession();
        $where = [ 'from_session' => GetSystemInfoHelper::getCurrentSession(), 'to_session' => $nextSession ];
        $promotions = $this->promotionRepo->getPromotions($where);

        if( $promotions->count() ) {
            foreach( $promotions as $promotion ) {
                $this->resetSingle($promotion->id);
                $this->deleteOldMarks($promotion->student_id, $nextSession);
            }
        }
        return JsonHelper::jsonUpdateSuccess();
    }

    protected function deleteOldMarks($student_id, $year)
    {
        Mark::where([ 'student_id' => $student_id, 'year' => $year ])->delete();
    }
}
