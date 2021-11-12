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
        return $this->model->with([ 'subject', 'timeSlot' ])->orderBy('timestamp_from')->where($where)->get();
    }


}
