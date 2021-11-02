<?php

namespace App\Models;

use Eloquent;

class TimeTableRecord extends Eloquent
{
    protected $fillable = ['name', 'my_course_id', 'exam_id', 'year'];

    public function my_course()
    {
        return $this->belongsTo(MyCourse::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
