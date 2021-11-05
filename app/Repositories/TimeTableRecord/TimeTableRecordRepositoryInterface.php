<?php

namespace App\Repositories\TimeTableRecord;

use App\Repositories\RepositoryInterface;

interface TimeTableRecordRepositoryInterface extends RepositoryInterface
{
    public function getAll();
    public function create($attributes = []);
    public function update($id, $attribute);
    public function delete($id);
    public function find($id);
    public function getTTRByIDs($ids);
    public function getRecord($where);

}
