<?php

namespace App\Repositories\MyCourse;

use App\Repositories\RepositoryInterface;

interface MyCourseRepositoryInterface extends RepositoryInterface
{
    public function getAll();
    public function find($id);
    public function create($attributes = []);
    public function update($id, $attribute);
    public function delete($id);
    public function getMC($data);

}
