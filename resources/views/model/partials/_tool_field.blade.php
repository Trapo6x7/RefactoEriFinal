<label class="uppercase" for="name">Nom de l'outil</label>
<input
    type="text"
    id="name"
    name="name"
    value="{{ old('name', $instance->name ?? '') }}"
    placeholder="Nom de l'outil"
    required
    class="w-full text-lg px-4 py-2 border border-secondary-grey rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-accent focus:border-transparent"
/>