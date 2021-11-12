<?php

namespace App\Repositories\Grade;

use App\Repositories\RepositoryInterface;

interface GradeRepositoryInterface extends RepositoryInterface
{
    public function getAll();

    public function getGrade($total, $major_id);
    public function getGrade2($total);

}
