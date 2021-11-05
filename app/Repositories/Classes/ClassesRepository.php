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
        return $this->model->orderBy('name', 'asc')->with(['myCourse', 'teacher'])->get();
    }
    public function find($id)
    {
        // TODO: Implement find() method.
        return $this->model->find($id);
    }
    public function create($attributes = [])
    {
        // TODO: Implement create() method.
        return $this->model->create($attributes);
    }
    public function update($id, $attribute)
    {
        // TODO: Implement update() method.
        return $this->model->find($id)->update($attribute);
    }
    public function delete($id) : bool
    {
        // TODO: Implement delete() method.
        return $this->model->destroy($id);
    }
    public function isActiveClass($class_id)
    {
        // TODO: Implement isActiveClass() method.
        return $this->model->where(['id' => $class_id, 'active' => 1])->exists();

    }
    public function getCourseClasses($course_id)
    {
        // TODO: Implement getCourseClasses() method.
        return $this->model->where(['my_course_id' => $course_id])->orderBy('name', 'asc')->get();
    }

}
