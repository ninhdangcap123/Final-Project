<?php

namespace App\Models;

use Eloquent;

class Payment extends Eloquent
{
    protected $fillable = ['title', 'amount', 'my_course_id', 'description', 'year', 'ref_no'];

    public function my_course()
    {
        return $this->belongsTo(MyCourse::class);
    }
}
