<?php

namespace App\Repositories\ExamRecord;

use App\Repositories\RepositoryInterface;

interface ExamRecordRepositoryInterface extends RepositoryInterface
{

    public function getRecord($data);

    public function updateRecord($where,$data);
}
