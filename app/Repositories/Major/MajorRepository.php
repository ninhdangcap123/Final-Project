<?php

namespace App\Repositories\Major;

use App\Models\Major;
use App\Models\MyCourse;
use App\Repositories\BaseRepository;

class MajorRepository extends BaseRepository implements MajorRepositoryInterface
{
    public function getModel()
    {
        // TODO: Implement getModel() method.
        return Major::class;
    }

    public function getAll()
    {
        // TODO: Implement getAll() method.
        return $this->model->orderBy('name', 'asc')->get();
    }

    public function findMajor($id)
    {
        // TODO: Implement find() method.
        return $this->model->find($id);

    }

    public function findMajorByCourse($course_id)
    {
        // TODO: Implement findMajorByCourse() method.
        return $this->model->find($this->findMyCourse($course_id)->major_id);
    }

    public function findMyCourse($id)
    {
        // TODO: Implement findMyCourse() method.
        return MyCourse::find($id);
    }

}
