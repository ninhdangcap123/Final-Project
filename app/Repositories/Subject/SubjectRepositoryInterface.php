<?php

namespace App\Repositories\Subject;

use App\Repositories\RepositoryInterface;

interface SubjectRepositoryInterface extends RepositoryInterface
{
    public function create($attributes = []);
    public function update($id, $attribute);
    public function getAll();
    public function delete($id);
    public function find($id);
    public function findSubjectByCourse($course_id);
    public function findSubjectByTeacher($teacher_id);
    public function getSubject($data);
    public function getSubjectsByIDs($ids);


}
