{{-- This file is used for menu items by any Backpack v6 theme --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i>
        {{ trans('backpack::base.dashboard') }}</a></li>

@php
    $user = backpack_user();
@endphp

@if ($user && $user->role === 'superadmin')
    <x-backpack::menu-item title="Users" icon="la la-question" :link="backpack_url('user')" />
    <x-backpack::menu-item title="Techniciens" icon="la la-question" :link="backpack_url('tech')" />
    <x-backpack::menu-item title="Statut Problème" icon="la la-question" :link="backpack_url('problem-status')" />
@endif


<x-backpack::menu-item title="Societés" icon="la la-question" :link="backpack_url('society')" />
<x-backpack::menu-item title="Interlocuteurs" icon="la la-question" :link="backpack_url('interlocutor')" />
<x-backpack::menu-item title="Problèmes" icon="la la-question" :link="backpack_url('problem')" />
<x-backpack::menu-item title="Environnements" icon="la la-question" :link="backpack_url('env')" />
<x-backpack::menu-item title="Outils" icon="la la-question" :link="backpack_url('tool')" />

@if ($user && $user->role === 'superadmin')
    <x-backpack::menu-item title="Menus" icon="la la-question" :link="backpack_url('menu')" />
@endif
