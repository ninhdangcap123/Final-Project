<?php

namespace App\Repositories\User;

use App\Repositories\RepositoryInterface;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function update($id, $attribute);
    public function delete($id);
    public function create($attributes = []);
    public function getUserByType($type);
    public function find($id);
    public function getAll();
    public function getPTAUsers();

}
