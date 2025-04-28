<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tech extends Model
{
    protected $table = 'tech';

    protected $fillable = [
        'name',
    ];
}
