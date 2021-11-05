<?php

namespace App\Repositories\Nationals;

use App\Models\Nationality;
use App\Repositories\BaseRepository;

class NationalRepository extends BaseRepository implements NationalRepositoryInterface
{
    public function getModel()
    {
        // TODO: Implement getModel() method.
        return Nationality::class;
    }
    public function getAllNationals()
    {
        // TODO: Implement getAllNationals() method.
        return $this->model->orderBy('name','asc')->get();

    }

}
