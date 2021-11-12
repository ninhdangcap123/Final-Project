<?php

namespace App\Repositories\User;

use App\Repositories\RepositoryInterface;

interface UserRepositoryInterface extends RepositoryInterface
{

    public function getUserByType($type);

    public function getAll();

    public function getPTAUsers();

}
