<?php

namespace App\Repositories\State;

use App\Models\State;
use App\Repositories\BaseRepository;

class StateRepository extends BaseRepository implements StateRepositoryInterface
{
    public function getModel()
    {
        // TODO: Implement getModel() method.
        return State::class;
    }

    public function getStates()
    {
        // TODO: Implement getAllStates() method.
        return $this->model->all();
    }


}
