<?php

namespace App\Http\Controllers;

use App\Helpers\GetUserTypeHelper;
use App\Helpers\Qs;
use App\Repositories\Classes\ClassesRepositoryInterface;
use App\Repositories\LGA\LGARepositoryInterface;
use App\Repositories\LocationRepo;
use App\Repositories\MyCourse\MyCourseRepositoryInterface;
use App\Repositories\MyCourseRepo;
use App\Repositories\Subject\SubjectRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class AjaxController extends Controller
{
    protected $loc, $my_course, $class, $lga, $subject;

    public function __construct(ClassesRepositoryInterface $class, SubjectRepositoryInterface $subject, MyCourseRepositoryInterface $my_course,
                                LGARepositoryInterface $lga)
    {

        $this->my_course = $my_course;
        $this->lga = $lga;
        $this->subject = $subject;
        $this->class = $class;
    }

    public function getLga($state_id)
    {
//        $state_id = Qs::decodeHash($state_id);
//        return ['id' => Qs::hash($q->id), 'name' => $q->name];

        $lgas = $this->lga->getAllLGAs($state_id);
        return $data = $lgas->map(function($q){
            return ['id' => $q->id, 'name' => $q->name];
        })->all();
    }

    public function getClassSections($class_id)
    {
        $classes = $this->class->getCourseClasses($class_id);
        return $sections = $classes->map(function($q){
            return ['id' => $q->id, 'name' => $q->name];
        })->all();
    }

    public function getClassSubjects($class_id)
    {
        $classes = $this->class->getCourseClasses($class_id);
        $subjects = $this->subject->findSubjectByCourse($class_id);

        if(GetUserTypeHelper::userIsTeacher()){
            $subjects = $this->subject->findSubjectByTeacher(Auth::user()->id)->where('my_class_id', $class_id);
        }

        $d['classes'] = $classes->map(function($q){
            return ['id' => $q->id, 'name' => $q->name];
        })->all();
        $d['subjects'] = $subjects->map(function($q){
            return ['id' => $q->id, 'name' => $q->name];
        })->all();

        return $d;
    }

}
