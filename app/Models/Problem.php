<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Problem extends Model
{

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

    public function scopeAlphabetical($query)
    {
        return $query->orderBy('title', 'asc');
    }
    
}
