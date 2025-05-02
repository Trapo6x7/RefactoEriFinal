<label class="uppercase" for="title">Titre</label>
<input
    type="text"
    id="title"
    name="title"
    value="{{ old('title', $instance->title ?? '') }}"
    placeholder="Titre"
    required
    class="w-full px-4 py-2 border border-secondary-grey rounded-lg  focus:outline-none focus:ring-2 focus:ring-blue-accent focus:border-transparent"
/>

<label class="uppercase" for="env">Environnement</label>
<input
    type="text"
    id="env"
    name="env"
    value="{{ old('env', $instance->env ?? '') }}"
    placeholder="Environnement"
    class="w-full px-4 py-2 border border-secondary-grey rounded-lg  focus:outline-none focus:ring-2 focus:ring-blue-accent focus:border-transparent"
/>

<label class="uppercase" for="tool">Outil</label>
<input
    type="text"
    id="tool"
    name="tool"
    value="{{ old('tool', $instance->tool ?? '') }}"
    placeholder="Outil"
    class="w-full px-4 py-2 border border-secondary-grey rounded-lg  focus:outline-none focus:ring-2 focus:ring-blue-accent focus:border-transparent"
/>

<label class="uppercase" for="societe">Société</label>
<input
    type="text"
    id="societe"
    name="societe"
    value="{{ old('societe', $instance->societe ?? '') }}"
    placeholder="Société"
    class="w-full px-4 py-2 border border-secondary-grey rounded-lg  focus:outline-none focus:ring-2 focus:ring-blue-accent focus:border-transparent"
/>

<label class="uppercase" for="description">Description</label>
<textarea
    id="description"
    name="description"
    placeholder="Description"
    class="w-full px-4 py-2 border border-secondary-grey rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-accent focus:border-transparent"
>{{ old('description', $instance->description ?? '') }}</textarea>
