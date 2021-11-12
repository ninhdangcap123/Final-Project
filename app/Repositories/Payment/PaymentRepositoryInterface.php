<?php

namespace App\Repositories\Payment;

use App\Repositories\RepositoryInterface;

interface PaymentRepositoryInterface extends RepositoryInterface
{

    public function getPayment($data);

    public function getGeneralPayment($data);

    public function getActivePayments();

    public function getPaymentYears();


}
