<?php

namespace App\Repositories\Major;

use App\Repositories\RepositoryInterface;

interface MajorRepositoryInterface extends RepositoryInterface
{
    public function findMajor($id);

    public function getAll();

    public function findMyCourse($id);

    public function findMajorByCourse($course_id);


}
