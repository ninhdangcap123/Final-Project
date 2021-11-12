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


}
