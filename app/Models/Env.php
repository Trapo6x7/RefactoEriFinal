<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Env extends Model
{
    use CrudTrait;
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
