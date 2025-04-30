@php
    $fields = [
        'name' => 'Nom',
        'id_main' => 'ID Main',
        'status_client' => 'Status Client',
        'status_distrib' => 'Status Distrib',
        'service_backup' => 'Service Backup',
        'infos_backup' => 'Infos Backup',
        'service_connect' => 'Service Connect',
        'infos_connect' => 'Infos Connect',
        'service_cloody' => 'Service Cloody',
        'infos_cloody' => 'Infos Cloody',
        'service_maintenance' => 'Service Maintenance',
        'infos_maintenance' => 'Infos Maintenance',
        'service_heberg_web' => 'Service Héberg Web',
        'infos_heberg_web' => 'Infos Héberg Web',
        'service_mail' => 'Service Mail',
        'infos_mail' => 'Infos Mail',
        'service_EBP' => 'Service EBP',
        'infos_EBP' => 'Infos EBP',
        'service_maintenance_office' => 'Service Maintenance Office',
        'infos_maintenance_office' => 'Infos Maintenance Office',
        'service_maintenance_serveur' => 'Service Maintenance Serveur',
        'infos_maintenance_serveur' => 'Infos Maintenance Serveur',
        'service_maintenance_infra_rso' => 'Service Maintenance Infra RSO',
        'infos_maintenance_infra_rso' => 'Infos Maintenance Infra RSO',
        'service_maintenance_equip_rso' => 'Service Maintenance Equip RSO',
        'infos_maintenance_equip_rso' => 'Infos Maintenance Equip RSO',
        'service_maintenance_ESET' => 'Service Maintenance ESET',
        'infos_maintenance_ESET' => 'Infos Maintenance ESET',
        'service_maintenance_domaine_DNS' => 'Service Maintenance Domaine DNS',
        'infos_maintenance_domaine_DNS' => 'Infos Maintenance Domaine DNS',
        'boss_name' => 'Nom du dirigeant',
        'boss_phone' => 'Téléphone dirigeant',
        'recep_phone' => 'Téléphone accueil',
        'address' => 'Adresse',
        'status' => 'Status',
    ];
@endphp

@foreach ($fields as $name => $label)
    <div class="mb-4">
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1 text-center">
            {{ $label }}
        </label>
        <input
            type="text"
            id="{{ $name }}"
            name="{{ $name }}"
            value="{{ old($name, $instance->$name ?? '') }}"
            placeholder="{{ $label }}"
            @if ($name === 'name') required @endif
            class="w-full px-4 py-2 border border-secondary-grey rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-accent focus:border-transparent"
        >
    </div>
@endforeach