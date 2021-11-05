<?php

namespace App\Repositories\TimeSlot;

use App\Models\TimeSlot;
use App\Models\TimeTableRecord;
use App\Repositories\BaseRepository;

class TimeSlotRepository extends BaseRepository implements TimeSlotRepositoryInterface
{
    public function getModel()
    {
        // TODO: Implement getModel() method.
        return TimeSlot::class;
    }
    public function getTimeSlot($where)
    {
        // TODO: Implement getTimeSlot() method.
        return $this->model->orderBy('timestamp_from')->where($where);
    }
    public function getTimeSlotByTTR($ttr_id)
    {
        // TODO: Implement getTimeSlotByTTR() method.
        return $this->getTimeSlot(['ttr_id' => $ttr_id])->get();
    }
    public function getDistinctTTR($remove_ttr = NULL)
    {
        // TODO: Implement getDistinctTTR() method.
        return $remove_ttr ? $this->model->where('ttr_id', '<>', $remove_ttr)->distinct()->select('ttr_id')->pluck('ttr_id') : $this->model->distinct()->select('ttr_id')->pluck('ttr_id');
    }
    public function getExistingTS($remove_ttr = NULL)
    {
        // TODO: Implement getExistingTS() method.
        $ids  = $this->getDistinctTTR($remove_ttr);
        return $this->getTTRByIDs($ids);
    }
    public function getTTRByIDs($ids)
    {
        return TimeTableRecord::orderBy('name')->whereIn('id', $ids)->get();
    }
    public function create($attributes = [])
    {
        // TODO: Implement create() method.
        return $this->model->create($attributes);
    }
    public function delete($id) : bool
    {
        // TODO: Implement delete() method.
        return $this->model->destroy($id);
    }
    public function deleteTimeSlotByIDs($where)
    {
        // TODO: Implement deleteTimeSlotByIDs() method.
        return $this->model->where($where)->delete();
    }
    public function update($id, $attribute)
    {
        // TODO: Implement update() method.
        return $this->model->find($id)->update($attribute);
    }
    public function find($id)
    {
        // TODO: Implement find() method.
        return $this->model->findOrFail($id);
    }


}
