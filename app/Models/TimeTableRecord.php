<?php

namespace App\Models;

use Eloquent;

class TimeTableRecord extends Eloquent
{
    protected $fillable = ['name', 'my_class_id', 'exam_id', 'year'];

    public function my_class()
    {
        return $this->belongsTo(MyClass::class,'my_class_id');
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class,'exam_id');
    }
}
