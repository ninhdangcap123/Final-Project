<?php

namespace App\Models;

use App\User;
use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentRecord extends Eloquent
{
    use HasFactory;

    protected $fillable = [
        'session', 'user_id', 'my_class_id', 'section_id', 'my_parent_id', 'dorm_id', 'dorm_room_no', 'adm_no', 'year_admitted', 'wd', 'wd_date', 'grad', 'grad_date', 'house', 'age'
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function my_parent()
    {
        return $this->belongsTo(User::class,'my_parent_id');
    }

    public function my_class()
    {
        return $this->belongsTo(MyClass::class,'my_class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class,'section_id');
    }

    public function dorm()
    {
        return $this->belongsTo(Dorm::class,'dorm_id');
    }
}
