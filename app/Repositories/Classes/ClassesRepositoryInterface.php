<?php

namespace App\Repositories\Classes;

use App\Repositories\RepositoryInterface;

interface ClassesRepositoryInterface extends RepositoryInterface
{
    public function create($attributes = []);
    public function find($id);
    public function update($id, $attribute);
    public function delete($id);
    public function getAll();
    public function isActiveClass($class_id);
    public function getCourseClasses($course_id);

}
