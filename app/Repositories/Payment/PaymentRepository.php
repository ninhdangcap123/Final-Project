<?php

namespace App\Repositories\Payment;

use App\Helpers\GetSystemInfoHelper;
use App\Models\Payment;
use App\Repositories\BaseRepository;

class PaymentRepository extends BaseRepository implements PaymentRepositoryInterface
{
    public function getModel()
    {
        // TODO: Implement getModel() method.
        return Payment::class;
    }
    public function getAll()
    {
        // TODO: Implement getAll() method.
        return $this->model->all();
    }
    public function getPayment($data)
    {
        // TODO: Implement getPayment() method.
        return $this->model->where($data)->with('myCourse');
    }
    public function getGeneralPayment($data)
    {
        // TODO: Implement getGeneralPayment() method.
        return $this->model->whereNull('my_course_id')->where($data)->with('myCourse');
    }
    public function getActivePayments()
    {
        // TODO: Implement getActivePayments() method.
        return $this->getPayment(['year' => GetSystemInfoHelper::getCurrentSession()]);
    }
    public function getPaymentYears()
    {
        // TODO: Implement getPaymentYears() method.
        return $this->model->select('year')->distinct()->get();
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
    public function delete($id) :  bool
    {
        // TODO: Implement delete() method.
        return $this->model->detroy($id);
    }

}
