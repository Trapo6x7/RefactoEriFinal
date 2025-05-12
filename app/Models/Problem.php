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
        return $this->belongsTo(Env::class, 'env');
    }
    public function tool()
    {
        return $this->belongsTo(Tool::class, 'tool');
    }
    public function society()
    {
        return $this->belongsTo(Society::class, 'societe');
    }
}
