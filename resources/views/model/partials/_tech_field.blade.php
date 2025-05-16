<label class="uppercase" for="name">Nom du technicien</label>
<input
    type="text"
    id="nametech"
    name="name"
    value="{{ old('name', $instance->name ?? '') }}"
    placeholder="Nom du technicien"
    required
    class="w-full px-4 py-2 border border-secondary-grey shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-accent focus:border-transparent"
/>