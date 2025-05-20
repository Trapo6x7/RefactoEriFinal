<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Tech extends Model
{

    protected $table = 'tech';

    protected $fillable = [
        'name',
    ];

    public function scopeAlphabetical($query)
    {
        return $query->orderBy('name', 'asc');
    }
}
