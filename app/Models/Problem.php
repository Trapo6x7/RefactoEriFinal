<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Problem extends Model
{
    use CrudTrait;
    protected $table = 'problems';

    protected $fillable = [
        'title',
        'env',
        'tool',
        'societe',
        'description',
    ];

    public function env()
    {
        return $this->belongsToMany(Env::class, 'env');
    }
    public function tool()
    {
        return $this->belongsToMany(Tool::class, 'env');
    }
    public function society()
    {
        return $this->belongsToMany(Society::class, 'env');
    }
}
