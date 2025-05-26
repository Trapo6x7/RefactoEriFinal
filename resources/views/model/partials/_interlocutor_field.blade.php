@foreach ($fields as $name => $field)
    @if (str_starts_with($name, 'service_'))
        @php
            $infoField = 'infos_' . substr($name, 8);
            $serviceValue = old($name, $instance->$name ?? 0);
            $infoValue = old($infoField, $instance->$infoField ?? '');
        @endphp
        <label for="{{ $name }}" class="block text-lg font-medium text-gray-700 mb-1 text-center uppercase">
            {{ __('fields.' . $name) }}
        </label>
        <select name="{{ $name }}" id="{{ $name }}"
            class="w-full px-4 py-2 border border-secondary-grey rounded-lg text-lg"
            onchange="document.getElementById('container_{{ $infoField }}').style.display = this.value == 1 ? '' : 'none';">
            <option value="0" {{ $serviceValue == 0 ? 'selected' : '' }}>Non</option>
            <option value="1" {{ $serviceValue == 1 ? 'selected' : '' }}>Oui</option>
        </select>
        <div class="mt-2" id="container_{{ $infoField }}" style="display: {{ $serviceValue == 1 ? '' : 'none' }};">
            <textarea name="{{ $infoField }}" id="{{ $infoField }}"
                class="w-full px-4 py-2 border service-info h-auto border-secondary-grey rounded-lg text-lg"
                placeholder="{{ __('fields.' . $infoField) }}">{{ $infoValue }}</textarea>
        </div>
    @elseif (!str_starts_with($name, 'infos_') && !str_starts_with($name, 'fullname'))
        <label for="{{ $name }}" class="block text-lg font-medium text-gray-700 mb-1 text-center uppercase">
            {{ __('fields.' . $name) }}
        </label>
        @if ($name === 'societe')
            <select id="societe" name="societe"
                class="w-full px-4 py-2 border border-secondary-grey rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-accent focus:border-transparent"
                required>
                <option value="">Sélectionner une societe</option>
                @foreach ($societies as $society)
                    <option value="{{ $society->id }}"
                        {{ old('societe', $instance->societe ?? '') == $society->id ? 'selected' : '' }}>
                        {{ $society->name }}
                    </option>
                @endforeach
            </select>
        @elseif ($name === 'fullname')
            @continue
        @else
            <input type="{{ $field['type'] ?? 'text' }}" id="{{ $name }}" name="{{ $name }}"
                value="{{ old($name, $instance->{$name} ?? '') }}" placeholder="{{ $field['placeholder'] ?? '' }}"
                class="w-full px-4 py-2 border border-secondary-grey rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-accent focus:border-transparent"
                @if (in_array($name, ['service_connect', 'service_cloody', 'service_comptes', 'service_mail'])) required @endif>
        @endif
    @endif
@endforeach

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Service/infos dynamique
        @foreach ($fields as $name => $field)
            @if (str_starts_with($name, 'service_'))
                document.getElementById('{{ $name }}').addEventListener('change', function() {
                    document.getElementById('container_infos_{{ substr($name, 8) }}').style.display =
                        this.value == 1 ? '' : 'none';
                });
            @endif
        @endforeach

        // Concaténation dynamique pour fullname
        function updateFullname() {
            const name = document.getElementById('name')?.value ?? '';
            const lastname = document.getElementById('lastname')?.value ?? '';
            const fullname = (name + ' ' + lastname).trim();
            if (document.getElementById('fullname')) {
                document.getElementById('fullname').value = fullname;
            }
        }
        if (document.getElementById('name') && document.getElementById('lastname') && document.getElementById(
                'fullname')) {
            document.getElementById('name').addEventListener('input', updateFullname);
            document.getElementById('lastname').addEventListener('input', updateFullname);
            updateFullname();
        }
    });
</script>
