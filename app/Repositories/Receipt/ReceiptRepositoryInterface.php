<?php

namespace App\Repositories\Receipt;

use App\Repositories\RepositoryInterface;

interface ReceiptRepositoryInterface extends RepositoryInterface
{
    public function create($attributes = []);
    public function delete($id);
    public function getReceipt($data);



}
