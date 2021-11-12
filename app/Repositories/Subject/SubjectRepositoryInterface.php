<?php

namespace App\Repositories\Subject;

use App\Repositories\RepositoryInterface;

interface SubjectRepositoryInterface extends RepositoryInterface
{

    public function getAll();

    public function findSubjectByCourse($course_id);

    public function findSubjectByTeacher($teacher_id);

    public function getSubject($data);

    public function getSubjectsByIDs($ids);


}
