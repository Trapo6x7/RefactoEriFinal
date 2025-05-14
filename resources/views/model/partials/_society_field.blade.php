@php
    $client = old('status_client', $instance->status_client ?? 0);
    $distrib = old('status_distrib', $instance->status_distrib ?? 0);
    $selectedStatus = $client && $distrib ? 'both' : ($client ? 'client' : ($distrib ? 'distrib' : 'none'));
@endphp
<div class="mb-4">
    <label for="status_combined" class="block text-sm font-medium text-gray-700 mb-1 text-center uppercase">
        Client / Distributeur
    </label>
    <select name="status_combined" id="status_combined"
        class="w-full px-4 py-2 border border-secondary-grey rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-accent focus:border-transparent">
        <option value="client" {{ $selectedStatus == 'client' ? 'selected' : '' }}>Client</option>
        <option value="distrib" {{ $selectedStatus == 'distrib' ? 'selected' : '' }}>Distributeur</option>
        <option value="both" {{ $selectedStatus == 'both' ? 'selected' : '' }}>Client & Distributeur</option>
    </select>
    <input type="hidden" name="status_client" id="status_client" value="{{ $client }}">
    <input type="hidden" name="status_distrib" id="status_distrib" value="{{ $distrib }}">
</div>

@foreach ($fields as $name => $config)
    @if (!str_starts_with($name, 'infos_') && !in_array($name, ['status_client', 'status_distrib']))
        <div class="mb-4">
            @if (!str_starts_with($name, 'infos_'))
                <label for="{{ $name }}"
                    class="block text-sm font-medium text-gray-700 mb-1 text-center uppercase">
                    {{ __('fields.' . $name) }}
                </label>
            @endif

            @if ($name === 'status')
                <select name="status" id="status"
                    class="w-full px-4 py-2 border border-secondary-grey rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-accent focus:border-transparent"
                    @if ($config['required']) required @endif>
                    <option value="active" {{ old('status', $instance->status ?? '') == 'active' ? 'selected' : '' }}>
                        Actif
                    </option>
                    <option value="inactive"
                        {{ old('status', $instance->status ?? '') == 'inactive' ? 'selected' : '' }}>
                        Inactif</option>
                </select>
            @elseif ($name === 'id_main')
                <select name="id_main" id="id_main"
                    class="w-full px-4 py-2 border border-secondary-grey rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-accent focus:border-transparent">
                    <option value="0">Sélectionnez une société</option>
                    @foreach ($societies as $society)
                        <option value="{{ $society->id }}"
                            {{ old('id_main', $instance->id_main ?? '') == $society->id ? 'selected' : '' }}>
                            {{ $society->name }}
                        </option>
                    @endforeach
                </select>
            @elseif (str_starts_with($name, 'service_'))
                @php
                    $infoField = 'infos_' . substr($name, 8);
                    $serviceValue = old($name, $instance->$name ?? 0);
                    $infoValue = old($infoField, $instance->$infoField ?? '');
                @endphp
                <select name="{{ $name }}" id="{{ $name }}"
                    class="w-full px-4 py-2 border border-secondary-grey rounded-lg text-sm"
                    onchange="document.getElementById('container_{{ $infoField }}').style.display = this.value == 1 ? '' : 'none';">
                    <option value="0" {{ $serviceValue == 0 ? 'selected' : '' }}>Non</option>
                    <option value="1" {{ $serviceValue == 1 ? 'selected' : '' }}>Oui</option>
                </select>
                <div class="mt-2" id="container_{{ $infoField }}"
                    style="display: {{ $serviceValue == 1 ? '' : 'none' }};">
                    <input type="text" name="{{ $infoField }}" id="{{ $infoField }}"
                        value="{{ $infoValue }}"
                        class="w-full px-4 py-2 border border-secondary-grey rounded-lg text-sm"
                        placeholder="{{ __('fields.' . $infoField) }}">
                </div>
            @elseif (!str_starts_with($name, 'infos_'))
                <input type="{{ $config['type'] }}" id="{{ $name }}" name="{{ $name }}"
                    value="{{ old($name, $instance->$name ?? '') }}"
                    placeholder="{{ $config['type'] === 'number' ? __('Entrez un nombre entier') : __('fields.' . $name) }}"
                    @if ($config['required']) required @endif
                    class="w-full px-4 py-2 border text-sm border-secondary-grey rounded-lg  focus:outline-none focus:ring-2 focus:ring-blue-accent focus:border-transparent">
            @endif
        </div>
    @endif
@endforeach

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Status client/distrib synchronisation
        const select = document.getElementById('status_combined');
        const client = document.getElementById('status_client');
        const distrib = document.getElementById('status_distrib');

        function syncStatusFields() {
            switch (select.value) {
                case 'client':
                    client.value = 1;
                    distrib.value = 0;
                    break;
                case 'distrib':
                    client.value = 0;
                    distrib.value = 1;
                    break;
                case 'both':
                    client.value = 1;
                    distrib.value = 1;
                    break;
            }
        }
        if (select) {
            select.addEventListener('change', syncStatusFields);
            syncStatusFields();
        }

        // Service/infos dynamique
        @foreach ($fields as $name => $config)
            @if (str_starts_with($name, 'service_'))
                document.getElementById('{{ $name }}').addEventListener('change', function() {
                    document.getElementById('container_infos_{{ substr($name, 8) }}').style.display =
                        this.value == 1 ? '' : 'none';
                });
            @endif
        @endforeach
    });
</script>
