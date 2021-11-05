<?php

namespace App\Repositories\Grade;

use App\Repositories\RepositoryInterface;

interface GradeRepositoryInterface extends RepositoryInterface
{
    public function getAll();
    public function find($id);
    public function create($attributes = []);
    public function update($id, $attribute);
    public function delete($id);
    public function getGrade($total, $major_id);
    public function getGrade2($total);

}
