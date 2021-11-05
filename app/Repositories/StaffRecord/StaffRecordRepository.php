<?php

namespace App\Repositories\StaffRecord;

use App\Models\StaffRecord;
use App\Repositories\BaseRepository;


class StaffRecordRepository extends BaseRepository implements StaffRecordRepositoryInterface
{
    public function getModel()
    {
        // TODO: Implement getModel() method.
        return StaffRecord::class;
    }
    public function create($attributes = [])
    {
        // TODO: Implement create() method.
        return $this->model->create($attributes);
    }
    public function update($id, $attribute)
    {
        // TODO: Implement update() method.
        return $this->model->where($id)->update($attribute);
    }

}
