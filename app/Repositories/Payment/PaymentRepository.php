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

    public function getGeneralPayment($data)
    {
        // TODO: Implement getGeneralPayment() method.
        return $this->model->whereNull('my_course_id')->where($data)->with('myCourse');
    }

    public function getActivePayments()
    {
        // TODO: Implement getActivePayments() method.
        return $this->getPayment([ 'year' => GetSystemInfoHelper::getCurrentSession() ]);
    }

    public function getPayment($data)
    {
        // TODO: Implement getPayment() method.
        return $this->model->where($data)->with('myCourse');
    }

    public function getPaymentYears()
    {
        // TODO: Implement getPaymentYears() method.
        return $this->model->select('year')->distinct()->get();
    }


}
