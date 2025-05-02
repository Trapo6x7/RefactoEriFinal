<label class="uppercase" for="name">Nom de l'environnement</label>
<input
    type="text"
    id="name"
    name="name"
    value="{{ old('name', $instance->name ?? '') }}"
    placeholder="Nom de l'environnement"
    required
    class="w-full px-4 py-2 border border-secondary-grey rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-accent focus:border-transparent"
/>