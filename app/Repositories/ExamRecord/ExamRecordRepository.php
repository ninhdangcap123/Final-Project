<?php

namespace App\Repositories\ExamRecord;

use App\Models\ExamRecord;
use App\Repositories\BaseRepository;

class ExamRecordRepository extends BaseRepository implements ExamRecordRepositoryInterface
{
    public function getModel()
    {
        // TODO: Implement getModel() method.
        return ExamRecord::class;
    }

    public function getRecord($data)
    {
        // TODO: Implement getRecord() method.
        return $this->model->where($data)->get();
    }

}
