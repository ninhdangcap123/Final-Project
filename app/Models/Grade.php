<?php

namespace App\Models;

use Eloquent;

class Grade extends Eloquent
{
    protected $fillable = ['name', 'major_id', 'mark_from', 'mark_to', 'remark'];
}
