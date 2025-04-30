{{-- This file is used for menu items by any Backpack v6 theme --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i>
        {{ trans('backpack::base.dashboard') }}</a></li>

@php
    $user = backpack_user();
@endphp

<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="usersDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        Interne
    </a>
    <ul class="dropdown-menu" aria-labelledby="internDropdown">
        <li>
            <x-backpack::menu-item title="Users" :link="backpack_url('user')" class="dropdown-item" />
        </li>
        <li>
            <x-backpack::menu-item title="Techniciens" :link="backpack_url('tech')" class="dropdown-item" />
        </li>
    </ul>
</li>

<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="usersDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
      Externe
    </a>
    <ul class="dropdown-menu" aria-labelledby="externDropdown">
        <li>
            <x-backpack::menu-item title="Societés" :link="backpack_url('society')" />
        </li>
        <li>
            <x-backpack::menu-item title="Interlocuteurs" :link="backpack_url('interlocutor')" />        </li>
    </ul>
</li>

<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="usersDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
      Maintenance
    </a>
    <ul class="dropdown-menu" aria-labelledby="maintenanceDropdown">
        <li>
            <x-backpack::menu-item title="Statuts" :link="backpack_url('problem-status')" />
        </li>
        <li>
            <x-backpack::menu-item title="Problèmes" :link="backpack_url('problem')" />
        </li>
        <li>
            <x-backpack::menu-item title="Environnements" :link="backpack_url('env')" />
        </li>
        <li>
            <x-backpack::menu-item title="Outils" :link="backpack_url('tool')" />
        </li>
    </ul>
</li>


<x-backpack::menu-item title="Raccourci" :link="backpack_url('menu')" />
