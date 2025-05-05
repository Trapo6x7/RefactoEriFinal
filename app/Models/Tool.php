<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Tool extends Model
{
    use CrudTrait;
    protected $table = 'tools';

    protected $fillable = [
        'name',
    ];

    public function problems()
    {
        return $this->hasMany(Problem::class, 'tool');
    }
}
