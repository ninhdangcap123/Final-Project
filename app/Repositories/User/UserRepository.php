<?php

namespace App\Repositories\User;

use App\Repositories\BaseRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\User;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function getModel()
    {
        // TODO: Implement getModel() method.
        return User::class;
    }
    public function update($id, $attribute)
    {
        // TODO: Implement update() method.
        return $this->model->find($id)->update($attribute);
    }
    public function delete($id) : bool
    {
        // TODO: Implement delete() method.
        return $this->model->destroy($id);
    }
    public function create($attributes = [])
    {
        // TODO: Implement create() method.
        return $this->model->create($attributes);
    }
    public function getUserByType($type){
        return $this->model->where(['user_type' => $type])->orderBy('name', 'asc')->get();
    }
    public function find($id)
    {
        // TODO: Implement find() method.
        return $this->model->find($id);
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
