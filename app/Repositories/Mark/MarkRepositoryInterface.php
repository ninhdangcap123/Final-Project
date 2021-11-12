<?php

namespace App\Repositories\Mark;

use App\Repositories\RepositoryInterface;

interface MarkRepositoryInterface extends RepositoryInterface
{

    public function getExamYears($student_id);
    public function getMark($data);
    public function getSubjectTotalTerm($student_id, $subject_id, $term, $course_id, $year);
    public function getExamTotalTerm($exam, $student_id, $course_id, $year);
    public function getExamAverageTerm($exam, $student_id, $course_id, $sec_id, $year);
    public function getSubCumTotal($tex3, $student_id, $subject_id, $course_id, $year);
    public function getSubCumAvg($tex3, $student_id, $subject_id, $course_id, $year);
    public function getSubjectMark($exam, $course_id, $subject_id, $student_id, $year);
    public function getSubjectPosition($student_id, $exam, $course_id, $subject_id, $year);
    public function countExamSubjects($exam, $student_id, $course_id, $year);
    public function getClassAverage($exam, $course_id, $year);
    public function getPosition($student_id, $exam, $course_id, $class_id, $year);
    public function getSubjectIDs($data);
    public function getStudentIDs($data);
}
