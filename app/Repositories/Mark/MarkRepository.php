<?php

namespace App\Repositories\Mark;

use App\Models\Mark;
use App\Repositories\BaseRepository;

class MarkRepository extends BaseRepository implements MarkRepositoryInterface
{
    public function getModel()
    {
        // TODO: Implement getModel() method.
        return Mark::class;
    }

    public function getExamYears($student_id)
    {
        // TODO: Implement getExamYears() method.
        return $this->model->where('student_id', $student_id)->select('year')->distinct()->get();
    }

    public function getMark($data)
    {
        // TODO: Implement getMark() method.
        return $this->model->where($data)->with('grade')->get();
    }

    public function getExamAverageTerm($exam, $student_id, $course_id, $sec_id, $year)
    {
        // TODO: Implement getExamAvgTerm() method.
        $data = [
            'student_id' => $student_id,
            'exam_id' => $exam->id,
            'my_course_id' => $course_id,
            'class_id' => $sec_id,
            'year' => $year ];

        $tex = 'tex'.$exam->term;
        $mark = $this->model->where($data)->where($tex, '>', 0);
        $average = $mark->select($tex)->avg($tex);
        return round($average, 1);
    }

    public function insert($data)
    {
        // TODO: Implement insert() method.
        return $this->model->insert($data);
    }

    public function getClassAverage($exam, $course_id, $year)
    {
        // TODO: Implement getClassAvg() method.
        $data = [
            'exam_id' => $exam->id,
            'my_course_id' => $course_id,
            'year' => $year ];
        $tex = 'tex'.$exam->term;

        $average = $this->model->where($data)->select($tex)->avg($tex);
        return round($average, 1);
    }

   

    public function getExamTotalTerm($exam, $student_id, $course_id, $year)
    {
        // TODO: Implement getExamTotalTerm() method.
        $data = [
            'student_id' => $student_id,
            'exam_id' => $exam->id,
            'my_course_id' => $course_id,
            'year' => $year ];

        $tex = 'tex'.$exam->term;
        $mark = $this->model->where($data);
        return $mark->select($tex)->sum($tex);
    }

    public function getSubjectIDs($data)
    {
        // TODO: Implement getSubjectIDs() method.
        return $this->model->distinct()->select('subject_id')->where($data)->get()->pluck('subject_id');
    }

    public function getStudentIDs($data)
    {
        // TODO: Implement getStudentIDs() method.
        return $this->model->distinct()->select('student_id')->where($data)->get()->pluck('student_id');
    }
}
