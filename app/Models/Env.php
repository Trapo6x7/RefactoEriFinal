<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Env extends Model
{
    protected $table = 'env';

    protected $fillable = [
        'name',
    ];

    public function problems()
    {
        return $this->hasMany(Problem::class, 'env');
    }

    public function scopeAlphabetical($query)
    {
        return $query->orderBy('name', 'asc');
    }
}
