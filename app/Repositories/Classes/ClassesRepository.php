<?php

namespace App\Repositories\Classes;

use App\Models\Classes;
use App\Repositories\BaseRepository;

class ClassesRepository extends BaseRepository implements ClassesRepositoryInterface
{
    public function getModel()
    {
        // TODO: Implement getModel() method.
        return Classes::class;
    }

    public function getAll()
    {
        // TODO: Implement getAll() method.
        return $this->model->orderBy('name', 'asc')->with([ 'myCourse', 'teacher' ])->get();
    }

    public function isActiveClass($class_id)
    {
        // TODO: Implement isActiveClass() method.
        return $this->model->where([ 'id' => $class_id, 'active' => 1 ])->exists();

    }

    public function getCourseClasses($course_id)
    {
        // TODO: Implement getCourseClasses() method.
        return $this->model->where([ 'my_course_id' => $course_id ])->orderBy('name', 'asc')->get();
    }

}
