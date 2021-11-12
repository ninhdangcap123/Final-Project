<?php

namespace App\Repositories\LGA;

use App\Models\Lga;
use App\Repositories\BaseRepository;

class LGARepository extends BaseRepository implements LGARepositoryInterface
{
    public function getModel()
    {
        // TODO: Implement getModel() method.
        return Lga::class;
    }

    public function getAllLGAs($state_id)
    {
        // TODO: Implement getAllLGAs() method.
        return $this->model->where('state_id', $state_id)->orderBy('name', 'asc')->get();
    }

}
