<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Tech extends Model
{
    use CrudTrait;
    protected $table = 'tech';

    protected $fillable = [
        'name',
    ];

    public function scopeAlphabetical($query)
    {
        return $query->orderBy('name', 'asc');
    }
}
