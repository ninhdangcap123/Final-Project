<?php

namespace App\Models;

use App\User;
use Eloquent;

class Promotion extends Eloquent
{
    protected $fillable = [
        'from_course',
        'from_section',
        'to_course',
        'to_section',
        'grad',
        'student_id',
        'from_session',
        'to_session',
        'status'
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function fromCourse()
    {
        return $this->belongsTo(MyCourse::class, 'from_course');
    }

    public function fromSection()
    {
        return $this->belongsTo(Classes::class, 'from_section');
    }

    public function toSection()
    {
        return $this->belongsTo(Classes::class, 'to_section');
    }

    public function toCourse()
    {
        return $this->belongsTo(MyCourse::class, 'to_course');
    }
}
