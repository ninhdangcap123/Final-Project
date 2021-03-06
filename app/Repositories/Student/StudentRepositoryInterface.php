<?php

namespace App\Repositories\Student;

use App\Repositories\RepositoryInterface;

interface StudentRepositoryInterface extends RepositoryInterface
{
    public function activeStudents();

    public function gradStudents();

    public function where($id);

    public function createRecord($attributes = []);

    public function updateRecord($id, array $attribute);

    public function getRecord($data);

    public function getRecordByUserIDs($ids);

    public function getRecordSortBy($data);

    public function findByUserId($st_id);

    public function getAll();

    public function updateStudent($id, $attribute);

    public function getGradRecord($data = []);

    public function exists($student_id);

    public function getAllDorms();

    public function findStudentsByCourse($course_id);

    public function allGradStudents();

    public function findStudentsByClass($class_id);


}
