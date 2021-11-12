<?php

namespace App\Repositories\Classes;

use App\Repositories\RepositoryInterface;

interface ClassesRepositoryInterface extends RepositoryInterface
{

    public function getAll();
    public function isActiveClass($class_id);
    public function getCourseClasses($course_id);

}
