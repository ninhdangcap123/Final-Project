<?php

namespace App\Models;

use App\User;
use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentRecord extends Eloquent
{
    use HasFactory;

    protected $fillable = [
        'session', 'user_id', 'my_course_id', 'class_id', 'my_parent_id',
        'dorm_id', 'dorm_room_no', 'adm_no', 'year_admitted', 'wd', 'wd_date', 'grad', 'grad_date', 'house', 'age'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function myParent()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function myCourse()
    {
        return $this->belongsTo(MyCourse::class, 'my_course_id');
    }

    public function classes()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function dorm()
    {
        return $this->belongsTo(Dorm::class, 'dorm_id');
    }
}
