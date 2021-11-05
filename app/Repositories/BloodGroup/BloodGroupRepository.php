<?php

namespace App\Repositories\BloodGroup;

use App\Models\BloodGroup;
use App\Repositories\BaseRepository;

class BloodGroupRepository extends BaseRepository implements BloodGroupRepositoryInterface
{
    public function getModel()
    {
        // TODO: Implement getModel() method.
        return BloodGroup::class;
    }
    public function getAll()
    {
        // TODO: Implement getAll() method.
        return $this->model->orderby('name')->get();
    }


}
