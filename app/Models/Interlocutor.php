<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Interlocutor extends Model
{
    protected $table = 'interlocutor';

    protected $fillable = [
        'name',
        'lastname',
        'fullname',
        'societe',
        'phone_fix',
        'phone_mobile',
        'email',
        'id_teamviewer',
        'service_connect',
        'service_cloody',
        'service_comptes',
        'service_mail',
        'infos_connect',
        'infos_cloody',
        'infos_comptes',
        'infos_mail',
    ];
}
