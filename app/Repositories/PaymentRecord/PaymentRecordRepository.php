<?php

namespace App\Repositories\PaymentRecord;

use App\Models\PaymentRecord;
use App\Repositories\BaseRepository;

class PaymentRecordRepository extends BaseRepository implements PaymentRecordRepositoryInterface
{
    public function getModel()
    {
        // TODO: Implement getModel() method.
        return PaymentRecord::class;
    }
    public function create($attributes = [])
    {
        // TODO: Implement create() method.
        return $this->model->firstOrCreate($attributes);
    }
    public function find($id)
    {
        // TODO: Implement find() method.
        return $this->model->findOrFail($id);
    }
    public function update($id, $attribute)
    {
        // TODO: Implement update() method.
        return $this->model->find($id)->update($attribute);
    }
    public function getRecord($data)
    {
        // TODO: Implement getRecord() method.
        return $this->model->orderBy('year', 'desc')->where($data)->with('payment');
    }
    public function getAllMyPR($st_id, $year = NULL)
    {
        // TODO: Implement getAllMyPR() method.
        return $year ? $this->getRecord(['student_id' => $st_id, 'year' => $year]) : $this->getRecord(['student_id' => $st_id]);
    }
    public function findMyPR($st_id, $pay_id)
    {
        // TODO: Implement findMyPR() method.
        return $this->getRecord(['student_id' => $st_id, 'payment_id' => $pay_id]);
    }


}
