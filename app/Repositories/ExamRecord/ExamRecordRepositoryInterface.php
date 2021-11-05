<?php

namespace App\Repositories\ExamRecord;

use App\Repositories\RepositoryInterface;

interface ExamRecordRepositoryInterface extends RepositoryInterface
{
    public function create($attributes = []);
    public function update($id, $attribute);
    public function find($id);
    public function getRecord($data);

}
