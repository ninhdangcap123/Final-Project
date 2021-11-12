<?php

namespace App\Repositories\PaymentRecord;

use App\Repositories\RepositoryInterface;

interface PaymentRecordRepositoryInterface extends RepositoryInterface
{

    public function getRecord($data);

    public function getAllMyPR($st_id, $year = NULL);

    public function findMyPR($st_id, $pay_id);


}
