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
    public function create($attributes = [])
    {
        // TODO: Implement create() method.
        return $this->model->create($attributes);
    }
    public function update($id, $attribute)
    {
        return $this->model->find($id)->update($attribute);
    }
    public function delete($id) : bool
    {
        // TODO: Implement delete() method.
        return $this->model->destroy($id);
    }
    public function find($id)
    {
        // TODO: Implement find() method.
        return $this->model->findOrFail($id);
    }

}
