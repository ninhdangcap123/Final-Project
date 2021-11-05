<?php

namespace App\Repositories\Payment;

use App\Repositories\RepositoryInterface;

interface PaymentRepositoryInterface extends RepositoryInterface
{
    public function getAll();
    public function getPayment($data);
    public function getGeneralPayment($data);
    public function getActivePayments();
    public function getPaymentYears();
    public function find($id);
    public function create($attributes = []);
    public function update($id, $attribute);
    public function delete($id);


}
