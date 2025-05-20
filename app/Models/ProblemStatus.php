<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ProblemStatus extends Model
{

    protected $table = 'problems_status';

    protected $fillable = [
        'name',
    ];
}
