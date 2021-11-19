<?php

namespace App\Repositories\Subject;

use App\Models\Subject;
use App\Repositories\BaseRepository;

class SubjectRepository extends BaseRepository implements SubjectRepositoryInterface
{
    public function getModel()
    {
        // TODO: Implement getModel() method.
        return Subject::class;
    }

    public function findSubjectByCourse($course_id)
    {
        // TODO: Implement findSubjectByCourse() method.
        return $this->getSubject([ 'my_course_id' => $course_id ])->orderBy('name')->get();
    }

    public function getSubject($data)
    {
        // TODO: Implement getSubject() method.
        return $this->model->where($data);
    }

    public function findSubjectByTeacher($teacher_id)
    {
        // TODO: Implement findSubjectByTeacher() method.
        return $this->getSubject([ 'teacher_id' => $teacher_id ])->orderBy('name')->get();
    }


    public function getSubjectsByIDs($ids)
    {
        // TODO: Implement getSubjectsByIDs() method.
        return $this->model->whereIn('id', $ids)->orderBy('name')->get();
    }

    public function getAll()
    {
        return $this->model->orderBy('name', 'asc')->with([ 'myCourse', 'teacher' ])->get();
    }


}
