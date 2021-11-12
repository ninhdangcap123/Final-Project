<?php

namespace App\Repositories\TimeTableRecord;

use App\Models\TimeTableRecord;
use App\Repositories\BaseRepository;

class TimeTableRecordRepository extends BaseRepository implements TimeTableRecordRepositoryInterface
{
    public function getModel()
    {
        // TODO: Implement getModel() method.
        return TimeTableRecord::class;
    }
    public function getAll()
    {
        // TODO: Implement getAll() method.
        return $this->model->orderBy('created_at')->with(['myCourse', 'exam'])->get();
    }
    public function getTTRByIDs($ids)
    {
        // TODO: Implement getTTRByIDs() method.
        return $this->model->orderBy('name')->whereIn('id', $ids)->get();
    }
    public function getRecord($where)
    {
        // TODO: Implement getRecord() method.
        return $this->model->where($where)->get();
    }


}
