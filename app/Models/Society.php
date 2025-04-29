<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Society extends Model
{
    use CrudTrait;
    protected $table = 'society';
    protected $fillable = [
        'id_main',
        'name',
        'status_client',
        'status_distrib',
        'service_backup',
        'infos_backup',
        'service_connect',
        'infos_connect',
        'service_cloody',
        'infos_cloody',
        'service_maintenance',
        'infos_maintenance',
        'service_heberg_web',
        'infos_heberg_web',
        'service_mail',
        'infos_mail',
        'service_EBP',
        'infos_EBP',
        'service_maintenance_office',
        'infos_maintenance_office',
        'service_maintenance_serveur',
        'infos_maintenance_serveur',
        'service_maintenance_infra_rso',
        'infos_maintenance_infra_rso',
        'service_maintenance_equip_rso',
        'infos_maintenance_equip_rso',
        'service_maintenance_ESET',
        'infos_maintenance_ESET',
        'service_maintenance_domaine_DNS',
        'infos_maintenance_domaine_DNS',
        'boss_name',
        'boss_phone',
        'recep_phone',
        'address',
        'status',
    ];
}
