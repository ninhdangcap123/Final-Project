<?php

namespace App\Models;

use Eloquent;

class Receipt extends Eloquent
{
    protected $fillable = [ 'pr_id', 'year', 'balance', 'amt_paid' ];

    public function paymentRecord()
    {
        return $this->belongsTo(PaymentRecord::class, 'pr_id');
    }

}
