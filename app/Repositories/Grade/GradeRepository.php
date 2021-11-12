<?php

namespace App\Repositories\Grade;

use App\Models\Grade;
use App\Repositories\BaseRepository;

class GradeRepository extends BaseRepository implements GradeRepositoryInterface
{
    public function getModel()
    {
        // TODO: Implement getModel() method.
        return Grade::class;
    }

    public function getAll()
    {
        // TODO: Implement getAll() method.
        return $this->model->orderBy('name')->get();
    }


    public function getGrade($total, $major_id)
    {
        // TODO: Implement getGrade() method.
        if( $total < 1 ) {
            return [];
        }

        $grades = $this->model->where([ 'major_id' => $major_id ])->get();

        if( $grades->isNotEmpty() ) {
            $grade = $grades->where('mark_from', '<=', $total)->where('mark_to', '>=', $total);
            return $grade->isNotEmpty() ? $grade->first() : $this->getGrade2($total);
        }
        return $this->getGrade2($total);
    }

    public function getGrade2($total)
    {
        // TODO: Implement getGrade2() method.
        $grades = $this->model->whereNull('major_id')->get();
        if( $grades->isNotEmpty() ) {
            return $grades->where('mark_from', '<=', $total)->where('mark_to', '>=', $total)->first();
        }
        return [];
    }
}
