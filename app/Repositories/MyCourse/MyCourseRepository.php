<?php

namespace App\Repositories\MyCourse;

use App\Models\MyCourse;
use App\Repositories\BaseRepository;

class MyCourseRepository extends BaseRepository implements MyCourseRepositoryInterface
{
    public function getModel()
    {
        // TODO: Implement getModel() method.
        return MyCourse::class;
    }
    public function getAll()
    {
        // TODO: Implement getAll() method.
        return $this->model->orderBy('name', 'asc')->with('major')->get();
    }
    public function getMC($data)
    {
        // TODO: Implement getMC() method.
        return $this->model->where($data)->with('classes');
    }

    public function find($id)
    {
        // TODO: Implement find() method.
        return $this->model->find($id);
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

    public function delete($id) : bool
    {
        // TODO: Implement delete() method.
        return $this->model->find($id)->delete();
    }


}
