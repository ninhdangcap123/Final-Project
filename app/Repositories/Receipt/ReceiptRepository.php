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
    public function create($attributes = [])
    {
        // TODO: Implement create() method.
        return $this->model->create($attributes);
    }
    public function delete($id) : bool
    {
        // TODO: Implement delete() method.
        return $this->model->destroy($id);
    }
    public function getReceipt($data)
    {
        // TODO: Implement getReceipt() method.
        return $this->model->where($data);
    }

}
