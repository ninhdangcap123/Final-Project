<?php

namespace App\Models;

use Eloquent;

class MyClass extends Eloquent
{
    protected $fillable = ['name', 'major_id'];

    public function section(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Section::class);
    }

    public function major(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    public function student_record(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StudentRecord::class);
    }
}
