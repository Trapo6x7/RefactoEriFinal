<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{

    protected $table = 'menus';
    
    protected $fillable = [
        'title',
        'icon',
        'link',
        'order',
        'role', // optionnel : pour restreindre à certains rôles
    ];
}