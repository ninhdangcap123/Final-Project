<?php

namespace App\Http\Controllers;

use App\Helpers\GetUserTypeHelper;
use App\Helpers\Qs;
use App\Repositories\LocationRepo;
use App\Repositories\MyCourseRepo;
use Illuminate\Support\Facades\Auth;

class AjaxController extends Controller
{
    protected $loc, $my_course;

    public function __construct(LocationRepo $loc, MyCourseRepo $my_course)
    {
        $this->loc = $loc;
        $this->my_course = $my_course;
    }

    public function getLga($state_id)
    {
//        $state_id = Qs::decodeHash($state_id);
//        return ['id' => Qs::hash($q->id), 'name' => $q->name];

        $lgas = $this->loc->getLGAs($state_id);
        return $data = $lgas->map(function($q){
            return ['id' => $q->id, 'name' => $q->name];
        })->all();
    }

    public function getClassSections($class_id)
    {
        $classes = $this->my_course->getClassSections($class_id);
        return $sections = $classes->map(function($q){
            return ['id' => $q->id, 'name' => $q->name];
        })->all();
    }

    public function getClassSubjects($class_id)
    {
        $classes = $this->my_course->getClassSections($class_id);
        $subjects = $this->my_course->findSubjectByClass($class_id);

        if(GetUserTypeHelper::userIsTeacher()){
            $subjects = $this->my_course->findSubjectByTeacher(Auth::user()->id)->where('my_class_id', $class_id);
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
