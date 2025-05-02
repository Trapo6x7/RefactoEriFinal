@foreach ($fields as $name => $config)
    <div class="mb-4">
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1 text-center uppercase">
            {{ $config['label'] }}
        </label>
        @if ($name === 'status')
            <select name="status" id="status"
                class="w-full px-4 py-2 border border-secondary-grey rounded-lg  focus:outline-none focus:ring-2 focus:ring-blue-accent focus:border-transparent"
                @if ($config['required']) required @endif>
                <option value="active" {{ old('status', $instance->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $instance->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        @else
            <input type="{{ $config['type'] }}" id="{{ $name }}" name="{{ $name }}"
                value="{{ old($name, $instance->$name ?? '') }}"
                placeholder="{{ $config['type'] === 'number' ? 'Entrez un nombre entier' : $config['label'] }}"
                @if ($config['required']) required @endif
                class="w-full px-4 py-2 border border-secondary-grey rounded-lg  focus:outline-none focus:ring-2 focus:ring-blue-accent focus:border-transparent">
        @endif
    </div>
@endforeach