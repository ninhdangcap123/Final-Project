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
    public function find($id)
    {
        // TODO: Implement find() method.
        return $this->model->find($id);
    }
    public function update($id, $attribute)
    {
        // TODO: Implement update() method.
        return $this->model->where($id)->update($attribute);
    }
    public function create($attributes = [])
    {
        // TODO: Implement create() method.
        return $this->model->firstOrCreate($attributes);
    }
    public function getRecord($data)
    {
        // TODO: Implement getRecord() method.
        return $this->model->where($data)->get();
    }

}
