<?php

namespace App\Models;

use App\User;
use Eloquent;

class Subject extends Eloquent
{
    protected $fillable = ['name', 'my_course_id', 'teacher_id', 'slug'];

    public function my_course()
    {
        return $this->belongsTo(MyCourse::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
