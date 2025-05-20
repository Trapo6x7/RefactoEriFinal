<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Tool extends Model
{

    protected $table = 'tools';

    protected $fillable = [
        'name',
    ];

    public function problems()
    {
        return $this->hasMany(Problem::class, 'tool');
    }

    public function scopeAlphabetical($query)
    {
        return $query->orderBy('name', 'asc');
    }
}
