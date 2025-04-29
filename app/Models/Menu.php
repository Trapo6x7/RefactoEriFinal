<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use CrudTrait;
    protected $table = 'menus';
    
    protected $fillable = [
        'title',
        'icon',
        'link',
        'order',
        'role', // optionnel : pour restreindre à certains rôles
    ];
}