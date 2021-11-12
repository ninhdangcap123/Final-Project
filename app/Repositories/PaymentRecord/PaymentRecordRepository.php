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

    public function getAllMyPR($st_id, $year = NULL)
    {
        // TODO: Implement getAllMyPR() method.
        return $year ? $this->getRecord([
            'student_id' => $st_id,
            'year' => $year
        ]) : $this->getRecord([ 'student_id' => $st_id ]);
    }

    public function getRecord($data)
    {
        // TODO: Implement getRecord() method.
        return $this->model->orderBy('year', 'desc')->where($data)->with('payment');
    }

    public function findMyPR($st_id, $pay_id)
    {
        // TODO: Implement findMyPR() method.
        return $this->getRecord([ 'student_id' => $st_id, 'payment_id' => $pay_id ]);
    }


}
