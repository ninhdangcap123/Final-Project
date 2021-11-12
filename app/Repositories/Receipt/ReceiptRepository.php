<?php

namespace App\Repositories\Receipt;

use App\Models\Receipt;
use App\Repositories\BaseRepository;

class ReceiptRepository extends BaseRepository implements ReceiptRepositoryInterface
{
    public function getModel()
    {
        // TODO: Implement getModel() method.
        return Receipt::class;
    }

    public function getReceipt($data)
    {
        // TODO: Implement getReceipt() method.
        return $this->model->where($data);
    }

}
