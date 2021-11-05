<?php

namespace App\Repositories\TimeTable;

use App\Repositories\RepositoryInterface;

interface TimeTableRepositoryInterface extends RepositoryInterface
{
    public function create($attributes = []);
    public function update($id, $attribute);
    public function delete($id);
    public function getTimeTable($where);

}
