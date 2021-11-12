<?php

namespace App\Repositories\User;

use App\Repositories\BaseRepository;
use App\User;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function getModel()
    {
        // TODO: Implement getModel() method.
        return User::class;
    }

    public function create($attributes = [])
    {
        // TODO: Implement create() method.
        return $this->model->create($attributes);
    }

    public function getUserByType($type)
    {
        return $this->model->where([ 'user_type' => $type ])->orderBy('name', 'asc')->get();
    }

    public function getAll()
    {
        // TODO: Implement getAll() method.
        return $this->model->orderBy('name', 'asc')->get();
    }

    public function getPTAUsers()
    {
        // TODO: Implement getPTAUsers() method.
        return $this->model->where('user_type', '<>', 'student')->orderBy('name', 'asc')->get();

    }
}
