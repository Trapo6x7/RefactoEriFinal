@php
    $fields = [
        ['name' => 'name', 'label' => 'Prénom', 'placeholder' => 'Prénom'],
        ['name' => 'lastname', 'label' => 'Nom', 'placeholder' => 'Nom'],
        ['name' => 'fullname', 'label' => 'Nom complet', 'placeholder' => 'Nom complet'],
        ['name' => 'societe', 'label' => 'Société', 'placeholder' => 'Société'],
        ['name' => 'phone_fix', 'label' => 'Téléphone fixe', 'placeholder' => 'Téléphone fixe'],
        ['name' => 'phone_mobile', 'label' => 'Téléphone mobile', 'placeholder' => 'Téléphone mobile'],
        ['name' => 'email', 'label' => 'Email', 'placeholder' => 'Email', 'type' => 'email'],
        ['name' => 'id_teamviewer', 'label' => 'ID Teamviewer', 'placeholder' => 'ID Teamviewer'],
        ['name' => 'service_connect', 'label' => 'Service Connect', 'placeholder' => 'Service Connect'],
        ['name' => 'service_cloody', 'label' => 'Service Cloody', 'placeholder' => 'Service Cloody'],
        ['name' => 'service_comptes', 'label' => 'Service Comptes', 'placeholder' => 'Service Comptes'],
        ['name' => 'service_mail', 'label' => 'Service Mail', 'placeholder' => 'Service Mail'],
        ['name' => 'infos_connect', 'label' => 'Infos Connect', 'placeholder' => 'Infos Connect'],
        ['name' => 'infos_cloody', 'label' => 'Infos Cloody', 'placeholder' => 'Infos Cloody'],
        ['name' => 'infos_comptes', 'label' => 'Infos Comptes', 'placeholder' => 'Infos Comptes'],
        ['name' => 'infos_mail', 'label' => 'Infos Mail', 'placeholder' => 'Infos Mail'],
    ];
@endphp


@foreach ($fields as $field)
    <label for="{{ $field['name'] }}" class="block text-sm font-medium text-gray-700 mb-1 text-center uppercase">{{ $field['label'] }}</label>
    @if ($field['name'] === 'societe')
        <select
            id="societe"
            name="societe"
            class="w-full px-4 py-2 border border-secondary-grey rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-accent focus:border-transparent"
            required
        >
            <option value="">Sélectionner une société</option>
            @foreach($societies as $society)
                <option value="{{ $society->id }}"
                    {{ old('societe', $instance->societe ?? '') == $society->id ? 'selected' : '' }}>
                    {{ $society->name }}
                </option>
            @endforeach
        </select>
    @else
        <input
            type="{{ $field['type'] ?? 'text' }}"
            id="{{ $field['name'] }}"
            name="{{ $field['name'] }}"
            value="{{ old($field['name'], $instance->{$field['name']} ?? '') }}"
            placeholder="{{ $field['placeholder'] }}"
            class="w-full px-4 py-2 border border-secondary-grey rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-accent focus:border-transparent"
            {{ $field['name'] === 'name' ? 'required' : '' }}
        >
    @endif
@endforeach
