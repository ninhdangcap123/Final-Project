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
    public function find($id)
    {
        // TODO: Implement find() method.
        return $this->model->find($id);
    }
    public function update($id, $attribute)
    {
        // TODO: Implement update() method.
        return $this->model->find($id)->update($attribute);
    }
}
