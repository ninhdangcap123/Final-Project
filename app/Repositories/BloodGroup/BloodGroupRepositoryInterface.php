<?php

namespace App\Repositories\BloodGroup;

use App\Repositories\RepositoryInterface;

interface BloodGroupRepositoryInterface extends RepositoryInterface
{
    public function getAll();

}
