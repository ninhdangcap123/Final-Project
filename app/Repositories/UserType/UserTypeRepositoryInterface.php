<?php

namespace App\Repositories\UserType;

use App\Repositories\RepositoryInterface;

interface UserTypeRepositoryInterface extends RepositoryInterface
{
    public function getAll();
    public function find($id);

}
