<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Interlocutor extends Model
{
    use CrudTrait;
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

    public function society()
    {
        return $this->belongsTo(Society::class, 'societe');
    }

    public function activeServicesWithInfos()
    {
        $result = [];
        foreach ($this->getAttributes() as $key => $value) {
            if (str_starts_with($key, 'service_') && $value) {
                $infoKey = 'infos_' . substr($key, 8);
                $result[$key] = [
                    'label' => ucfirst(str_replace('_', ' ', substr($key, 8))),
                    'info' => $this->$infoKey ?? null,
                ];
            }
        }
        return $result;
    }
}
