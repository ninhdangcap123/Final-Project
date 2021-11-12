<?php

namespace App\Repositories\TimeTableRecord;

use App\Repositories\RepositoryInterface;

interface TimeTableRecordRepositoryInterface extends RepositoryInterface
{
    public function getAll();

    public function getTTRByIDs($ids);

    public function getRecord($where);

}
