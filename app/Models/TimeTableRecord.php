<?php

namespace App\Models;

use Eloquent;

class TimeTableRecord extends Eloquent
{
    protected $fillable = [
        'name',
        'my_course_id',
        'exam_id',
        'year'
    ];

    public function myCourse()
    {
        return $this->belongsTo(MyCourse::class, 'my_course_id');
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
