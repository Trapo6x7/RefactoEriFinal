<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class ProblemStatus extends Model
{
    use CrudTrait;
    protected $table = 'problems_status';

    protected $fillable = [
        'name',
    ];
}
