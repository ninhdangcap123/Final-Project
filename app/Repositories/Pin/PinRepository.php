<?php

namespace App\Repositories\Pin;

use App\Models\Pin;
use App\Repositories\BaseRepository;

class PinRepository extends BaseRepository implements PinRepositoryInterface
{
    public function getModel()
    {
        // TODO: Implement getModel() method.
        return Pin::class;
    }

    public function insert($attributes = [])
    {
        // TODO: Implement insert() method.
        return $this->model->insert($attributes);
    }


    public function countValid(){
        return $this->find(['used' => 0])->count();
    }
    public function deleteUsed()
    {
        // TODO: Implement deleteUsed() method.
        return $this->find(['used' => 1])->delete();
    }
    public function getUserPin($code, $user_id, $st_id)
    {
        // TODO: Implement getUserPin() method.
        return  $this->find(['code' => $code, 'user_id' => $user_id, 'student_id' => $st_id])->get();
    }
    public function findValidCode($code)
    {
        // TODO: Implement findValidCode() method.
        return  $this->find(['code' => $code, 'used' => 0])->get();
    }
    public function getValid()
    {
        // TODO: Implement getValid() method.
        return $this->find(['used' => 0])->get();
    }
    public function getInvalid()
    {
        // TODO: Implement getInvalid() method.
        return $this->find(['used' => 1])->with(['user', 'student'])->get();
    }


}
