<?php

namespace App\Repositories\UserType;

use App\Models\UserType;
use App\Repositories\BaseRepository;

class UserTypeRepository extends BaseRepository implements UserTypeRepositoryInterface
{
    public function getModel()
    {
        // TODO: Implement getModel() method.
        return UserType::class;
    }
    public function getAll()
    {
        // TODO: Implement getAll() method.
        return $this->model->all();
    }
    public function find($id)
    {
        // TODO: Implement find() method.
        return $this->model->find($id);
    }

}
