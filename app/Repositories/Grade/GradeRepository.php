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
    public function find($id)
    {
        // TODO: Implement find() method.
        return $this->model->find($id);
    }
    public function create($attributes = [])
    {
        // TODO: Implement create() method.
        return $this->model->create($attributes);
    }
    public function update($id, $attribute)
    {
        // TODO: Implement update() method.
        return $this->model->find($id)->update($attribute);
    }
    public function delete($id) : bool
    {
        // TODO: Implement delete() method.
        return $this->model->destroy($id);
    }

    public function getGrade($total, $major_id)
    {
        // TODO: Implement getGrade() method.
        if($total < 1) { return NULL; }

        $grades = $this->model->where(['major_id' => $major_id])->get();

        if($grades->count() > 0){
            $gr = $grades->where('mark_from', '<=', $total)->where('mark_to', '>=', $total);
            return $gr->count() > 0 ? $gr->first() : $this->getGrade2($total);
        }
        return $this->getGrade2($total);
    }

    public function getGrade2($total)
    {
        // TODO: Implement getGrade2() method.
        $grades = $this->model->whereNull('major_id')->get();
        if($grades->count() > 0){
            return $grades->where('mark_from', '<=', $total)->where('mark_to', '>=', $total)->first();
        }
        return NULL;
    }
}