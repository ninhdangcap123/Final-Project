<?php

namespace App\Models;

use App\User;
use Eloquent;

class Mark extends Eloquent
{
    protected $fillable = [ 't1', 't2', 'exm', 'tex1', 'tex2',
        'tex3', 'grade_id', 'year', 'exam_id',
        'subject_id', 'my_course_id', 'student_id', 'class_id' ];

    public function exam()
    {
        return $this->belongsTo(Exam::class,'exam_id');
    }

    public function classes()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function myCourse()
    {
        return $this->belongsTo(MyCourse::class, 'my_course_id');
    }

    public function studentRecord()
    {
        return $this->belongsTo(StudentRecord::class, 'student_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class,'subject_id');
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class,'grade_id');
    }
}
