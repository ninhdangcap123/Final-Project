<?php

namespace App\Repositories\Student;

use App\Models\Dorm;
use App\Models\StudentRecord;
use App\Repositories\BaseRepository;

class StudentRepository extends BaseRepository implements StudentRepositoryInterface
{
    public function getModel()
    {
        // TODO: Implement getModel() method.
        return StudentRecord::class;
    }

    public function createRecord($attributes = [])
    {
        // TODO: Implement createRecord() method.
        return $this->model->create($attributes);
    }

    public function updateRecord($id, array $attribute)
    {
        // TODO: Implement updateRecord() method.
        return $this->model->find($id)->update($attribute);

    }

    public function updateStudent($promotion, $attribute)
    {
        // TODO: Implement update() method.

        return $this->model->where($promotion)->update($attribute);

    }

    public function where($id)
    {
        return $this->model->where([ 'user_id'=> $id])->get();
    }

    public function getAllDorms()
    {
        // TODO: Implement getAllDorms() method.
        return Dorm::orderBy('name', 'asc')->get();
    }

    public function findStudentsByCourse($course_id)
    {
        // TODO: Implement findStudentsByCourse() method.
        return $this->activeStudents()->where([ 'my_course_id' => $course_id ])->with([ 'myCourse', 'user' ])->get()->sortBy('user.name');
    }

    public function activeStudents()
    {
        // TODO: Implement activeStudents() method.
        return $this->model->where([ 'grad' => 0 ]);
    }

    public function allGradStudents()
    {
        // TODO: Implement allGradStudents() method.
        return $this->gradStudents()->with([
            'myCourse',
            'classes',
            'user'
        ])->get()->sortBy('user.name');
    }

    public function gradStudents()
    {
        // TODO: Implement gradStudents() method.
        return $this->model->where([ 'grad' => 1 ])->orderByDesc('grad_date');
    }

    public function findStudentsByClass($class_id)
    {
        // TODO: Implement findStudentsByClass() method.
        return $this->activeStudents()->where('class_id', $class_id)->with([ 'user', 'myCourse' ])->get();
    }

    public function getRecordByUserIDs($ids)
    {
        // TODO: Implement getRecordByUserIDs() method.
        return $this->activeStudents()->whereIn('user_id', $ids)->with('user')->sortBy('user.name')->get();
    }

    public function findByUserId($st_id)
    {
        // TODO: Implement findByUserId() method.
        return $this->getRecord([ 'user_id' => $st_id ]);
    }

    public function getRecord($data)
    {
        // TODO: Implement getRecord() method.
        return $this->activeStudents()->where($data)->with('user');
    }

    public function getRecordSortBy($data){
        return $this->activeStudents()->where($data)->with('user')->get()->sortBy('user.name');
    }

    public function getAll()
    {
        // TODO: Implement getAll() method.
        return $this->activeStudents()->with('user');
    }

    public function getGradRecord($data = [])
    {
        // TODO: Implement getGradRecord() method.
        return $this->gradStudents()->where($data)->with('user');
    }

    public function exists($student_id)
    {
        // TODO: Implement exists() method.
        return $this->getRecord([ 'user_id' => $student_id ])->exists();

    }


}
