<?php

namespace App\Models;

use App\User;
use Eloquent;

class Section extends Eloquent
{
    protected $fillable = ['name', 'my_course_id', 'active', 'teacher_id'];

    public function my_course()
    {
        return $this->belongsTo(MyCourse::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function studentRecord()
    {
        return $this->hasMany(StudentRecord::class);
    }
}
