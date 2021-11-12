<?php

namespace App\Repositories\Exam;

use App\Repositories\RepositoryInterface;

interface ExamRepositoryInterface extends RepositoryInterface
{
    public function getAll();

    public function getExam($data);

}
