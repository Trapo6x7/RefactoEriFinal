<label class="uppercase" for="name">Nom</label>
<input
    type="text"
    id="nameuser"
    name="name"
    value="{{ old('name', $instance->name ?? '') }}"
    placeholder="Nom"
    required
    class="w-full px-4 py-2 border border-secondary-grey shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-accent focus:border-transparent"
/>
<label class="uppercase" for="email">Email</label>
<input
    type="email"
    id="email"
    name="email"
    value="{{ old('email', $instance->email ?? '') }}"
    placeholder="Email"
    required
    class="w-full px-4 py-2 border border-secondary-grey shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-accent focus:border-transparent"
/>
<label class="uppercase" for="password">Mot de passe</label>
<input
    type="password"
    id="password"
    name="password"
    placeholder="Mot de passe"
    @if(!isset($instance)) required @endif
    class="w-full px-4 py-2 border border-secondary-grey shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-accent focus:border-transparent"
/>
<label class="uppercase" for="role">Rôle</label>
<select
    id="role"
    name="role"
    required
    class="w-full px-4 py-2 border border-secondary-grey shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-accent focus:border-transparent"
>
    @php
        $roles = ['user' => 'User', 'admin' => 'Admin', 'superadmin' => 'Superadmin'];
        $selectedRole = old('role', $instance->role ?? '');
    @endphp
    @foreach($roles as $value => $label)
        <option value="{{ $value }}" @if($selectedRole === $value) selected @endif>{{ $label }}</option>
    @endforeach
</select>