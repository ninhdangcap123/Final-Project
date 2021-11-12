<?php

namespace App\Http\Controllers;

use App\Helpers\GetUserTypeHelper;
use App\Repositories\Classes\ClassesRepositoryInterface;
use App\Repositories\LGA\LGARepositoryInterface;
use App\Repositories\LocationRepo;
use App\Repositories\MyCourse\MyCourseRepositoryInterface;
use App\Repositories\MyCourseRepo;
use App\Repositories\Subject\SubjectRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class AjaxController extends Controller
{
    protected $myCourseRepo;
    protected $classRepo;
    protected $lga;
    protected $subjectRepo;

    public function __construct(ClassesRepositoryInterface  $classRepo,
                                SubjectRepositoryInterface  $subjectRepo,
                                MyCourseRepositoryInterface $myCourseRepo,
                                LGARepositoryInterface      $lga)
    {
        $this->myCourseRepo = $myCourseRepo;
        $this->lga = $lga;
        $this->subjectRepo = $subjectRepo;
        $this->classRepo = $classRepo;
    }

    public function getLga($state_id)
    {
        $lgas = $this->lga->getAllLGAs($state_id);
        return $data = $lgas->map(function ($q) {
            return [ 'id' => $q->id, 'name' => $q->name ];
        })->all();
    }

    public function getClassSections($class_id)
    {
        $classes = $this->classRepo->getCourseClasses($class_id);
        return $sections = $classes->map(function ($q) {
            return [ 'id' => $q->id, 'name' => $q->name ];
        })->all();
    }

    public function getClassSubjects($class_id)
    {
        $classes = $this->classRepo->getCourseClasses($class_id);
        $subjects = $this->subjectRepo->findSubjectByCourse($class_id);

        if( GetUserTypeHelper::userIsTeacher() ) {
            $subjects = $this->subjectRepo->findSubjectByTeacher(Auth::user()->id)->where('my_class_id', $class_id);
        }

        $data['classes'] = $classes->map(function ($q) {
            return [ 'id' => $q->id, 'name' => $q->name ];
        })->all();
        $data['subjects'] = $subjects->map(function ($q) {
            return [ 'id' => $q->id, 'name' => $q->name ];
        })->all();

        return $data;
    }

}
