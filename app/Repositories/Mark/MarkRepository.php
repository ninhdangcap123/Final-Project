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
        $data = [ 'student_id' => $student_id, 'exam_id' => $exam->id, 'my_course_id' => $course_id, 'class_id' => $sec_id, 'year' => $year ];

        $tex = 'tex'.$exam->term;

        $mark = $this->model->where($data)->where($tex, '>', 0);
        $average = $mark->select($tex)->avg($tex);
        return round($average, 1);
    }

    public function getSubCumTotal($tex3, $student_id, $subject_id, $course_id, $year)
    {
        // TODO: Implement getSubCumTotal() method.
        $tex1 = $this->getSubjectTotalTerm($student_id, $subject_id, 1, $course_id, $year);
        $tex2 = $this->getSubjectTotalTerm($student_id, $subject_id, 2, $course_id, $year);
        return $tex1 + $tex2 + $tex3;
    }

    public function getSubjectTotalTerm($student_id, $subject_id, $term, $course_id, $year)
    {
        // TODO: Implement getSubTotalTerm() method.
        $data = [ 'student_id' => $student_id, 'subject_id' => $subject_id, 'my_course_id' => $course_id, 'year' => $year ];

        $tex = 'tex'.$term;
        $subjectTotal = $this->model->where($data)->select($tex)->get()->where($tex, '>', 0);
        return $subjectTotal->count() > 0 ? $subjectTotal->first()->$tex : NULL;
    }

    public function getSubCumAvg($tex3, $student_id, $subject_id, $course_id, $year)
    {
        // TODO: Implement getSubCumAvg() method.
        $count = 0;
        $tex1 = $this->getSubjectTotalTerm($student_id, $subject_id, 1, $course_id, $year);
        $count = $tex1 ? $count + 1 : $count;
        $tex2 = $this->getSubjectTotalTerm($student_id, $subject_id, 2, $course_id, $year);
        $count = $tex2 ? $count + 1 : $count;
        $count = $tex3 ? $count + 1 : $count;
        $total = $tex1 + $tex2 + $tex3;
        return ( $total > 0 ) ? round($total / $count, 1) : 0;

    }

    public function getSubjectPosition($student_id, $exam, $course_id, $subject_id, $year)
    {
        // TODO: Implement getSubPos() method.
        $data = [ 'exam_id' => $exam->id, 'my_course_id' => $course_id, 'subject_id' => $subject_id, 'year' => $year ];
        $tex = 'tex'.$exam->term;

        $subjectMark = $this->getSubjectMark($exam, $course_id, $subject_id, $student_id, $year);

        $subjectMarks = $this->model->where($data)->whereNotNull($tex)->orderBy($tex, 'DESC')->select($tex)->get()->pluck($tex);
        return $sub_pos = $subjectMarks->count() > 0 ? $subjectMarks->search($subjectMark) + 1 : NULL;
    }

    public function getSubjectMark($exam, $course_id, $subject_id, $student_id, $year)
    {
        // TODO: Implement getSubjectMark() method.
        $data = [ 'exam_id' => $exam->id, 'my_course_id' => $course_id, 'subject_id' => $subject_id, 'student_id' => $student_id, 'year' => $year ];
        $tex = 'tex'.$exam->term;

        return $this->model->where($data)->select($tex)->get()->first()->$tex;
    }

    public function countExamSubjects($exam, $student_id, $course_id, $year)
    {
        // TODO: Implement countExSubjects() method.
        $data = [ 'exam_id' => $exam->id, 'my_course_id' => $course_id, 'student_id' => $student_id, 'year' => $year ];
        $tex = 'tex'.$exam->term;

        if( $exam->term == 3 ) {
            unset($data['exam_id']);
        }

        return $this->model->where($data)->whereNotNull($tex)->count();
    }

    public function getClassAverage($exam, $course_id, $year)
    {
        // TODO: Implement getClassAvg() method.
        $data = [ 'exam_id' => $exam->id, 'my_course_id' => $course_id, 'year' => $year ];
        $tex = 'tex'.$exam->term;

        $average = $this->model->where($data)->select($tex)->avg($tex);
        return round($average, 1);
    }

    public function getPosition($student_id, $exam, $course_id, $class_id, $year)
    {
        // TODO: Implement getPos() method.
        $data = [ 'student_id' => $student_id, 'exam_id' => $exam->id, 'my_course_id' => $course_id, 'class_id' => $class_id, 'year' => $year ];
        $allMarks = [];
        $tex = 'tex'.$exam->term;

        $myMark = $this->model->where($data)->select($tex)->sum($tex);

        unset($data['student_id']);
        $mark = $this->model->where($data);
        $students = $mark->select('student_id')->distinct()->get();
        foreach( $students as $student ) {
            $allMarks[] = $this->getExamTotalTerm($exam, $student->student_id, $course_id, $year);
        }
        rsort($allMarks);
        return array_search($myMark, $allMarks) + 1;
    }

    public function getExamTotalTerm($exam, $student_id, $course_id, $year)
    {
        // TODO: Implement getExamTotalTerm() method.
        $data = [ 'student_id' => $student_id, 'exam_id' => $exam->id, 'my_course_id' => $course_id, 'year' => $year ];

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
