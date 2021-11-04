<?php

namespace App\Models;

use Eloquent;

class TimeTable extends Eloquent
{
    protected $fillable = ['ttr_id', 'ts_id', 'exam_date', 'day', 'timestamp_from', 'timestamp_to', 'subject_id',];

    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class, 'ts_id');
    }

    public function ttRecord()
    {
        return $this->belongsTo(TimeTableRecord::class, 'ttr_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
