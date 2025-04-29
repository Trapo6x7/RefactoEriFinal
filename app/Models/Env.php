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
}
