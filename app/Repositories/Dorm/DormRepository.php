<?php

namespace App\Repositories\Dorm;

use App\Models\Dorm;
use App\Repositories\BaseRepository;

class DormRepository extends BaseRepository implements DormRepositoryInterface
{
    public function getModel()
    {
        // TODO: Implement getModel() method.
        return Dorm::class;
    }

    public function getAll()
    {
        // TODO: Implement getAll() method.
        return $this->model->orderBy('name', 'asc')->get();
    }

}
