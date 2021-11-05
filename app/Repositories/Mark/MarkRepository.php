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
    public function create($attributes = [])
    {
        // TODO: Implement create() method.
        return $this->model->firstOrCreate($attributes);
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

    public function getSubTotalTerm($st_id, $sub_id, $term, $course_id, $year)
    {
        // TODO: Implement getSubTotalTerm() method.
        $d = ['student_id' => $st_id, 'subject_id' => $sub_id, 'my_course_id' => $course_id, 'year' => $year];

        $tex = 'tex'.$term;
        $sub_total = $this->model->where($d)->select($tex)->get()->where($tex, '>', 0);
        return $sub_total->count() > 0 ? $sub_total->first()->$tex : NULL;
    }

    public function getExamTotalTerm($exam, $st_id, $course_id, $year)
    {
        // TODO: Implement getExamTotalTerm() method.
        $d = ['student_id' => $st_id, 'exam_id' => $exam->id, 'my_course_id' => $course_id, 'year' => $year];

        $tex = 'tex'.$exam->term;
        $mk = $this->model->where($d);
        return $mk->select($tex)->sum($tex);
    }

    public function getExamAvgTerm($exam, $st_id, $course_id, $sec_id, $year)
    {
        // TODO: Implement getExamAvgTerm() method.
        $d = ['student_id' => $st_id, 'exam_id' => $exam->id, 'my_course_id' => $course_id, 'class_id' => $sec_id, 'year' => $year];

        $tex = 'tex'.$exam->term;

        $mk = $this->model->where($d)->where($tex, '>', 0);
        $avg = $mk->select($tex)->avg($tex);
        return round($avg, 1);
    }

    public function getSubCumTotal($tex3, $st_id, $sub_id, $course_id, $year)
    {
        // TODO: Implement getSubCumTotal() method.
        $tex1 = $this->getSubTotalTerm($st_id, $sub_id, 1, $course_id, $year);
        $tex2 = $this->getSubTotalTerm($st_id, $sub_id, 2, $course_id, $year);
        return $tex1 + $tex2 + $tex3;
    }

    public function getSubCumAvg($tex3, $st_id, $sub_id, $course_id, $year)
    {
        // TODO: Implement getSubCumAvg() method.
        $count = 0;
        $tex1 = $this->getSubTotalTerm($st_id, $sub_id, 1, $course_id, $year);
        $count = $tex1 ? $count + 1 : $count;
        $tex2 = $this->getSubTotalTerm($st_id, $sub_id, 2, $course_id, $year);
        $count = $tex2 ? $count + 1 : $count;
        $count = $tex3 ? $count + 1 : $count;
        $total = $tex1 + $tex2 + $tex3;

        return ($total > 0) ? round($total/$count, 1) : 0;

    }

    public function getSubjectMark($exam, $course_id, $sub_id, $st_id, $year)
    {
        // TODO: Implement getSubjectMark() method.
        $d = [ 'exam_id' => $exam->id, 'my_course_id' => $course_id, 'subject_id' => $sub_id, 'student_id' => $st_id, 'year' => $year ];
        $tex = 'tex'.$exam->term;

        return $this->model->where($d)->select($tex)->get()->first()->$tex;
    }

    public function getSubPos($st_id, $exam, $course_id, $sub_id, $year)
    {
        // TODO: Implement getSubPos() method.
        $d = ['exam_id' => $exam->id, 'my_course_id' => $course_id, 'subject_id' => $sub_id, 'year' => $year];
        $tex = 'tex'.$exam->term;

        $sub_mk = $this->getSubjectMark($exam, $course_id, $sub_id, $st_id, $year);

        $sub_mks = $this->model->where($d)->whereNotNull($tex)->orderBy($tex, 'DESC')->select($tex)->get()->pluck($tex);
        return $sub_pos = $sub_mks->count() > 0 ? $sub_mks->search($sub_mk) + 1 : NULL;
    }

    public function countExSubjects($exam, $st_id, $course_id, $year)
    {
        // TODO: Implement countExSubjects() method.
        $d = [ 'exam_id' => $exam->id, 'my_course_id' => $course_id, 'student_id' => $st_id, 'year' => $year ];
        $tex = 'tex'.$exam->term;

        if($exam->term == 3){ unset($d['exam_id']); }

        return $this->model->where($d)->whereNotNull($tex)->count();
    }

    public function getClassAvg($exam, $course_id, $year)
    {
        // TODO: Implement getClassAvg() method.
        $d = [ 'exam_id' => $exam->id, 'my_course_id' => $course_id, 'year' => $year ];
        $tex = 'tex'.$exam->term;

        $avg = $this->model->where($d)->select($tex)->avg($tex);
        return round($avg, 1);
    }

    public function getPos($st_id, $exam, $course_id, $sec_id, $year)
    {
        // TODO: Implement getPos() method.
        $d = ['student_id' => $st_id, 'exam_id' => $exam->id, 'my_course_id' => $course_id, 'class_id' => $sec_id, 'year' => $year ]; $all_mks = [];
        $tex = 'tex'.$exam->term;

        $my_mk = $this->model->where($d)->select($tex)->sum($tex);

        unset($d['student_id']);
        $mk = $this->model->where($d);
        $students = $mk->select('student_id')->distinct()->get();
        foreach($students as $s){
            $all_mks[] = $this->getExamTotalTerm($exam, $s->student_id, $course_id, $year);
        }
        rsort($all_mks);
        return array_search($my_mk, $all_mks) + 1;
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
