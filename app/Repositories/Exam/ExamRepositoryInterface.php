<?php

namespace App\Repositories\Exam;

use App\Repositories\RepositoryInterface;

interface ExamRepositoryInterface extends RepositoryInterface
{
    public function getAll();
    public function find($id);
    public function update($id, $attribute);
    public function create($attributes = []);
    public function delete($id);
    public function getExam($data);

}
