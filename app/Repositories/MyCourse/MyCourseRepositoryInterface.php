<?php

namespace App\Repositories\MyCourse;

use App\Repositories\RepositoryInterface;

interface MyCourseRepositoryInterface extends RepositoryInterface
{
    public function getAll();

    public function getMC($data);

}
