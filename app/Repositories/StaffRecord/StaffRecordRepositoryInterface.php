<?php

namespace App\Repositories\StaffRecord;

use App\Repositories\RepositoryInterface;

interface StaffRecordRepositoryInterface extends RepositoryInterface
{
    public function create($attributes = []);
    public function update($id, $attribute);

}
