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
            @if(in_array($field['name'], ['service_connect','service_cloody','service_comptes','service_mail'])) required @endif
        >
    @endif
@endforeach