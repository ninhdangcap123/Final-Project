<?php

namespace App\Repositories\TimeTable;

use App\Repositories\RepositoryInterface;

interface TimeTableRepositoryInterface extends RepositoryInterface
{

    public function getTimeTable($where);

}
