<?php

namespace App\Repositories\Exam;

use App\Models\Exam;
use App\Repositories\BaseRepository;

class ExamRepository extends BaseRepository implements ExamRepositoryInterface
{
    public function getModel()
    {
        // TODO: Implement getModel() method.
        return Exam::class;
    }
    public function getAll()
    {
        // TODO: Implement getAll() method.
        return $this->model->orderBy('name', 'asc')->orderBy('year', 'desc')->get();
    }

    public function getExam($data)
    {
        // TODO: Implement getExam() method.
        return $this->model->where($data)->get();
    }

}
