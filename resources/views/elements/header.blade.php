<header x-data="{ open: false }"
    class="px-6 flex justify-between items-center bg-secondary-grey h-20 text-primary-grey font-bold text-sm">

    <div class="flex w-1/4 justify-center items-center">
        <img src="{{ asset('imgs/logoligne.png') }}" alt="logo" class="lg:w-1/2 md:w-auto sm:w-auto">
    </div>

    <nav class="w-2/4 gap-5 hidden lg:flex justify-center items-center">
        <a href="" class="transition-colors duration-200  hover:text-blue-accent">ACCUEIL</a>
        <a href="" class="transition-colors duration-200  hover:text-blue-accent">SOCIETES</a>
        <a href="" class="transition-colors duration-200  hover:text-blue-accent">INTERLOCUTEURS</a>
        <a href="" class="transition-colors duration-200  hover:text-blue-accent">ENVIRONNEMENTS</a>
        <a href="" class="transition-colors duration-200  hover:text-blue-accent">OUTILS</a>
        <a href="" class="transition-colors duration-200  hover:text-blue-accent">PROBLEMES</a>
    </nav>

    <div class="lg:hidden flex items-center">
        <button @click="open = !open" class="focus:outline-none transition-transform duration-300"
            :class="{ 'rotate-90': open }">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round"
                    stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                    stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <nav x-show="open" @click.away="open = false"
        class="absolute top-20 left-0 w-full bg-secondary-grey flex flex-col items-center gap-4 py-4 lg:hidden z-50">
        <a href="" class="transition-colors duration-200 hover:text-blue-accent">ACCUEIL</a>
        <a href="" class="transition-colors duration-200  hover:text-blue-accent">SOCIETES</a>
        <a href="" class="transition-colors duration-200  hover:text-blue-accent">INTERLOCUTEURS</a>
        <a href="" class="transition-colors duration-200  hover:text-blue-accent">ENVIRONNEMENTS</a>
        <a href="" class="transition-colors duration-200  hover:text-blue-accent">OUTILS</a>
        <a href="" class="transition-colors duration-200  hover:text-blue-accent">PROBLEMES</a>
    </nav>

</header>