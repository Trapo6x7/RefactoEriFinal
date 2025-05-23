<nav x-data="{ open: false }" class="bg-off-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-auto py-2 items-center">
            <!-- Logo -->
            <div class="flex w-1/5 justify-center items-center">
                <a href="{{ route('dashboard') }}">
                    <img src="{{ asset('imgs/logoligne.png') }}" alt="logo" class="lg:w-1/2 md:w-auto sm:w-auto">
                </a>
            </div>

            <nav class="w-2/4 gap-5 hidden lg:flex justify-between items-center">
                <a href="{{ route('dashboard') }}"
                    class="transition-colors duration-200 hover:text-blue-accent text-primary-grey text-sm px-2 {{ request()->routeIs('dashboard') ? 'font-bold border-b-2 border-blue-accent text-blue-accent' : '' }}">
                    ACCUEIL
                </a>
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                        class="transition-colors duration-200  hover:text-blue-accent text-primary-grey text-sm px-2 flex items-center gap-1 focus:outline-none {{ request()->is('model/societe*') || request()->is('model/interlocuteur*') ? 'font-bold border-b-2 border-blue-accent text-blue-accent' : '' }}">
                        EXTERNE
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false"
                        class="absolute left-0 mt-2 min-w-max bg-off-white rounded-lg z-50 py-1" x-transition>
                        <a href="{{ route('model.index', ['model' => 'societe']) }}"
                            class="block px-4 py-2 text-sm  hover:text-blue-accent {{ request()->is('model/societe*') ? 'font-bold text-blue-accent' : '' }}">
                            SOCIETES
                        </a>
                        <a href="{{ route('model.index', ['model' => 'interlocuteur']) }}"
                            class="block px-4 py-2 text-sm  hover:text-blue-accent {{ request()->is('model/interlocuteur*') ? 'font-bold text-blue-accent' : '' }}">
                            INTERLOCUTEURS
                        </a>
                    </div>
                </div>
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                        class="transition-colors duration-200 hover:text-blue-accent text-primary-grey font-bol px-2 flex items-center gap-1 focus:outline-none text-sm {{ request()->is('model/environnement*') || request()->is('model/outil*') || request()->is('model/probleme*') ? 'font-bold border-b-2 border-blue-accent text-blue-accent' : '' }}">
                        MAINTENANCE
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false"
                        class="absolute left-0 mt-2 min-w-max bg-off-white rounded-lg z-50 py-1" x-transition>
                        <a href="{{ route('model.index', ['model' => 'environnement']) }}"
                            class="block px-4 py-2 text-sm  hover:text-blue-accent {{ request()->is('model/environnement*') ? 'font-bold text-blue-accent' : '' }}">
                            ENVIRONNEMENTS
                        </a>
                        <a href="{{ route('model.index', ['model' => 'outil']) }}"
                            class="block px-4 py-2 text-sm  hover:text-blue-accent {{ request()->is('model/outil*') ? 'font-bold text-blue-accent' : '' }}">
                            OUTILS
                        </a>
                        <a href="{{ route('model.index', ['model' => 'probleme']) }}"
                            class="block px-4 py-2 text-sm  hover:text-blue-accent {{ request()->is('model/probleme*') ? 'font-bold text-blue-accent' : '' }}">
                            PROBLEMES
                        </a>
                    </div>
                </div>
                @if (request()->routeIs('dashboard'))
                    <button type="button"
                        onclick="document.getElementById('dashboard-header')?.classList.toggle('hidden')"
                        class="ml-4 px-3 py-1 rounded bg-blue-accent text-off-white hover:bg-blue-hover transition text-sm">
                        +
                    </button>
                                    <button type="button" id="btn-raz"
                        class="ml-4 px-3 py-1 rounded bg-red-accent text-off-white hover:bg-red-hover transition text-sm">
                    -
                </button>
                @else
                    <div class="ml-4 px-3 py-1 rounded bg-off-white text-off-white"></div>
                    <div class="ml-4 px-3 py-1 rounded bg-off-white text-off-white"></div>
                @endif
            </nav>

            <!-- User Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @if (Auth::check())
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                            class="uppercase hidden lg:flex items-center px-3 py-2 text-sm leading-4 font-medium rounded-md text-primary-grey bg-off-white hover:text-blue-accent focus:outline-none transition ease-in-out duration-15">
                            <div class="text-sm">{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                        <div x-show="open" @click.away="open = false"
                            class="absolute right-0 mt-2 min-w-max bg-off-white rounded-lg z-50 py-1" x-transition>
                            @if (Auth::user() && Auth::user()->role === 'superadmin')
                                <a href="{{ route('user-tech.index') }}"
                                    class="block px-4 py-2 text-sm hover:text-blue-accent whitespace-nowrap">
                                    GÉRER LES UTILISATEURS
                                </a>
                            @endif
                            <a href="{{ route('profile.edit') }}"
                                class="block px-4 py-2 text-sm hover:text-blue-accent">
                                PROFIL
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="block w-full text-left px-4 py-2 text-sm text-red-accent hover:text-red-800">
                                    SE DECONNECTER
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Hamburger -->
            <div class="lg:hidden flex items-center">
                <button @click="open = !open" class="focus:outline-none transition-transform duration-300"
                    :class="{ 'rotate-90': open }">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <nav x-show="open" @click.away="open = false"
        class="absolute top-20 left-0 w-full bg-off-white flex flex-col items-center gap-4 py-4 lg:hidden z-50">
        <a href="{{ route('dashboard') }}"
            class="transition-colors duration-200 hover:text-blue-accent text-primary-grey font-bold text-s">
            ACCUEIL
        </a>
        <a href="{{ route('model.index', ['model' => 'societe']) }}"
            class="transition-colors duration-200 hover:text-blue-accent text-primary-grey font-bold text-s">
            SOCIETES
        </a>
        <a href="{{ route('model.index', ['model' => 'interlocuteur']) }}"
            class="transition-colors duration-200 hover:text-blue-accent text-primary-grey font-bold text-s">
            INTERLOCUTEURS
        </a>
        <a href="{{ route('model.index', ['model' => 'environnement']) }}"
            class="transition-colors duration-200 hover:text-blue-accent text-primary-grey font-bold text-s">
            ENVIRONNEMENTS
        </a>
        <a href="{{ route('model.index', ['model' => 'outil']) }}"
            class="transition-colors duration-200 hover:text-blue-accent text-primary-grey font-bold text-s">
            OUTILS
        </a>
        <a href="{{ route('model.index', ['model' => 'probleme']) }}"
            class="transition-colors duration-200 hover:text-blue-accent text-primary-grey font-bold text-s">
            PROBLEMES
        </a>
        @if (Auth::user() && Auth::user()->role === 'superadmin')
            <a href="{{ route('user-tech.index') }}"
                class="transition-colors duration-200 hover:text-blue-accent text-primary-grey font-bold text-s">
                GÉRER LES UTILISATEURS
            </a>
        @endif
        <a href="{{ route('profile.edit') }}"
            class="transition-colors duration-200 hover:text-blue-accent text-primary-grey font-bold text-s">
            PROFIL
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="transition-colors duration-200 text-red-accent font-bold text-s">
                SE DECONNECTER
            </button>
        </form>

    </nav>
</nav>

<!-- Modale Utilisateur -->
<div x-data="{ open: false }" x-on:open-user-modal.window="open = true" x-on:keydown.escape.window="open = false"
    x-show="open" style="display: none;"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-off-white rounded-lg p-6 w-full max-w-xl relative">
        <button @click="open = false" class="absolute top-2 right-2 text-red-accent text-sm">&times;</button>
        @include('model.form_modal', ['model' => 'user', 'action' => 'create'])
    </div>
</div>

<!-- Modale Technicien -->
<div x-data="{ open: false }" x-on:open-tech-modal.window="open = true" x-on:keydown.escape.window="open = false"
    x-show="open" style="display: none;"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-off-white rounded-lg p-6 w-full max-w-xl relative">
        <button @click="open = false" class="absolute top-2 right-2 text-red-accent text-sm">&times;</button>
        @include('model.form_modal', ['model' => 'tech', 'action' => 'create'])
    </div>
</div>

@vite(['resources/js/navbar.js'])