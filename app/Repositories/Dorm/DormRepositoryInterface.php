<?php

namespace App\Repositories\Dorm;

use App\Repositories\RepositoryInterface;

interface DormRepositoryInterface extends RepositoryInterface
{
    public function getAll();
    public function create($attributes = []);
    public function update($id, $attribute);


}
