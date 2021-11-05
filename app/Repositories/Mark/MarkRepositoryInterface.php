<?php

namespace App\Repositories\Mark;

use App\Repositories\RepositoryInterface;

interface MarkRepositoryInterface extends RepositoryInterface
{
    public function create($attributes = []);
    public function delete($id);
    public function update($id, $attribute);
    public function getExamYears($student_id);
    public function getMark($data);
    public function getSubTotalTerm($st_id, $sub_id, $term, $course_id, $year);
    public function getExamTotalTerm($exam, $st_id, $course_id, $year);
    public function getExamAvgTerm($exam, $st_id, $course_id, $sec_id, $year);
    public function getSubCumTotal($tex3, $st_id, $sub_id, $course_id, $year);
    public function getSubCumAvg($tex3, $st_id, $sub_id, $course_id, $year);
    public function getSubjectMark($exam, $course_id, $sub_id, $st_id, $year);
    public function getSubPos($st_id, $exam, $course_id, $sub_id, $year);
    public function countExSubjects($exam, $st_id, $course_id, $year);
    public function getClassAvg($exam, $course_id, $year);
    public function getPos($st_id, $exam, $course_id, $sec_id, $year);
    public function getSubjectIDs($data);
    public function getStudentIDs($data);
}
