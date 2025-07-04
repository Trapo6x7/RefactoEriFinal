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
<select
    id="env"
    name="env"
    class="w-full px-4 py-2 border border-secondary-grey rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-accent focus:border-transparent"
>
    <option value="">Sélectionner un environnement</option>
    @foreach($envs as $env)
        <option value="{{ $env->id }}" {{ old('env', $instance->env ?? '') == $env->id ? 'selected' : '' }}>
            {{ $env->name }}
        </option>
    @endforeach
</select>

<label class="uppercase" for="tool">Outil</label>
<select
    id="tool"
    name="tool"
    class="w-full px-4 py-2 border border-secondary-grey rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-accent focus:border-transparent"
>
    <option value="">Sélectionner un outil</option>
    @foreach($tools as $tool)
        <option value="{{ $tool->id }}" {{ old('tool', $instance->tool ?? '') == $tool->id ? 'selected' : '' }}>
            {{ $tool->name }}
        </option>
    @endforeach
</select>

<label class="uppercase" for="societe">societe</label>
<select
    id="societe"
    name="societe"
    class="w-full px-4 py-2 border border-secondary-grey rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-accent focus:border-transparent"
>
    <option value="">Sélectionner une societe</option>
    @foreach($societies as $societe)
        <option value="{{ $societe->id }}" {{ old('societe', $instance->societe ?? '') == $societe->id ? 'selected' : '' }}>
            {{ $societe->name }}
        </option>
    @endforeach
</select>

<label class="uppercase" for="description_form">Description</label>
<div class="mt-2">
    <textarea
        class="tinymce w-full h-48 px-4 py-2 border border-secondary-grey rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-accent focus:border-transparent"
        id="description_form"
        name="description"
        placeholder="Description"
    >{{ old('description', $instance->description ?? '') }}</textarea>
</div>
