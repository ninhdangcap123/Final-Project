<?php

namespace App\Repositories\Receipt;

use App\Repositories\RepositoryInterface;

interface ReceiptRepositoryInterface extends RepositoryInterface
{

    public function getReceipt($data);


}
