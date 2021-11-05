<?php

namespace App\Repositories\TimeTable;

use App\Models\TimeTable;
use App\Repositories\BaseRepository;

class TimeTableRepository extends BaseRepository implements TimeTableRepositoryInterface
{
    public function getModel()
    {
        // TODO: Implement getModel() method.
        return TimeTable::class;
    }
    public function getTimeTable($where)
    {
        // TODO: Implement getTimeTable() method.
        return $this->model->with(['subject', 'time_slot'])->orderBy('timestamp_from')->where($where)->get();
    }
    public function create($attributes = [])
    {
        // TODO: Implement create() method.
        return $this->model->create($attributes);
    }
    public function update($id, $attribute)
    {
        // TODO: Implement update() method.
        return $this->model->find($id)->update($attribute);
    }
    public function delete($id) :  bool
    {
        // TODO: Implement delete() method.
        return $this->model->destroy($id);
    }

}
