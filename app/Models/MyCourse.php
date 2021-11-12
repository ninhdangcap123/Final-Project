<?php

namespace App\Models;

use Eloquent;

class MyCourse extends Eloquent
{
    protected $fillable = [
        'name',
        'major_id'
    ];

    public function classes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Classes::class);
    }

    public function major(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Major::class, 'major_id');
    }

    public function studentRecord(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StudentRecord::class);
    }
}
