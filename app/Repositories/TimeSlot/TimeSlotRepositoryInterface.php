<?php

namespace App\Repositories\TimeSlot;

use App\Repositories\RepositoryInterface;

interface TimeSlotRepositoryInterface extends RepositoryInterface
{
    public function getTimeSlot($where);
    public function getTimeSlotByTTR($ttr_id);
    public function getDistinctTTR($remove_ttr = NULL);
    public function getExistingTS($remove_ttr = NULL);
    public function create($attributes = []);
    public function delete($id);
    public function deleteTimeSlotByIDs($where);
    public function update($id, $attribute);
    public function find($id);
    public function getTTRByIDs($ids);

}
