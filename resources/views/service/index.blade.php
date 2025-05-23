<x-app-layout>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (!Auth::user() || Auth::user()->role !== 'superadmin')
                // Masque tout le contenu de la page côté JS
                document.body.innerHTML =
                    '<div class="flex items-center justify-center min-h-screen text-2xl text-red-accent font-bold">Accès réservé au superadmin</div>';
            @endif
        });
    </script>
    <section class="flex flex-col items-center justify-center min-h-screen py-8 bg-gray-50">
        <div class="w-full max-w-5xl mx-auto bg-white rounded-2xl p-4 sm:p-8">
            <h2 class="text-3xl font-bold mb-6 text-blue-accent text-center uppercase">Gérer les Services Externes</h2>
            @if (session('success'))
                <div class="mb-4 text-green-700 bg-green-100 rounded-lg px-4 py-2">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 text-red-700 bg-red-100 rounded-lg px-4 py-2">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('service.store') }}" class="flex flex-col md:flex-row gap-3 mb-8">
                @csrf
                <input type="text" name="name" placeholder="Nom du service" required
                    class="border border-gray-300 rounded-lg px-3 py-2 flex-1 focus:ring-2 focus:ring-blue-accent focus:border-blue-accent transition">
                <input type="url" name="link" placeholder="Lien" required
                    class="border border-gray-300 rounded-lg px-3 py-2 flex-1 focus:ring-2 focus:ring-blue-accent focus:border-blue-accent transition">
                <button
                    class="bg-blue-accent hover:bg-blue-accent-dark text-white px-6 py-2 rounded-lg font-semibold transition">Ajouter</button>
            </form>

            <div class='flex items-center mb-4'>
                <input type="text" id="search-service" placeholder="Rechercher un service..."
                    class="px-4 py-2 border border-blue-accent rounded-lg w-full max-w-md mx-auto focus:ring-2 focus:ring-blue-accent focus:outline-none bg-white text-primary-grey placeholder:text-blue-accent" />
            </div>
            <div class="flex items-center mb-2 text-blue-accent">
                <button type="button" class="global-lock-btn text-lg mr-2 hover:text-blue-hover transition"
                    data-target="services" title="Déverrouiller">
                    <i class="fa-solid fa-lock"></i>
                </button>
            </div>

            <div class="overflow-x-auto rounded-lg flex flex-col justify-center items-center">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-blue-accent text-white text-center rounded-lg">
                            <th class="py-3 px-4 font-semibold">Nom</th>
                            <th class="py-3 px-4 font-semibold">Lien</th>
                            <th class="py-3 px-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($services as $service)
                            <tr class="border-b hover:bg-off-white transition text-center"
                                data-service-id="{{ $service->id }}">
                                <td class="py-3 px-4 break-words max-w-[180px] sm:max-w-none">
                                    <input type="text" value="{{ $service->name }}"
                                        class="service-name border border-blue-accent rounded px-2 py-1 w-full text-center bg-white/80 text-primary-grey focus:ring-2 focus:ring-blue-accent transition"
                                        readonly>
                                </td>
                                <td class="py-3 px-4">
                                    <input type="url" value="{{ $service->link }}"
                                        class="service-link border border-blue-accent rounded px-2 py-1 w-full text-center bg-white/80 text-primary-grey focus:ring-2 focus:ring-blue-accent transition"
                                        readonly>
                                </td>
                                <td class="py-3 px-4 flex gap-2 justify-center">
                                    <form method="POST" action="{{ route('service.destroy', $service) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            class="text-white bg-red-accent hover:bg-red-hover px-2 py-1 rounded font-semibold transition"
                                            onclick="return confirm('Supprimer ce service ?')">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modale de confirmation -->
        <div id="confirm-modal" class="fixed inset-0 bg-primary-grey/40 items-center justify-center z-50 hidden">
            <div class="bg-white rounded-2xl p-6 md:p-8 max-w-sm w-full text-center border-2 border-blue-accent">
                <h2 class="text-lg md:text-xl font-bold mb-4 text-blue-accent">Confirmer la sauvegarde</h2>
                <p class="mb-6 text-primary-grey">Voulez-vous vraiment enregistrer les modifications&nbsp;?</p>
                <div class="flex justify-center gap-4">
                    <button id="confirm-yes"
                        class="bg-blue-accent hover:bg-blue-hover text-white px-4 py-2 rounded-lg font-semibold transition">Oui</button>
                    <button id="confirm-no"
                        class="bg-off-white hover:bg-off-white px-4 py-2 rounded-lg text-blue-accent font-semibold transition">Non</button>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Labels mobile/tablette
            if (window.innerWidth <= 768) {
                document.querySelectorAll('table tbody tr').forEach(function(tr) {
                    let labels = ['Nom', 'Lien', 'Actions'];
                    tr.querySelectorAll('td').forEach(function(td, i) {
                        td.setAttribute('data-label', labels[i]);
                    });
                });
            }

            // Recherche service
            const searchInput = document.getElementById('search-service');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    let q = this.value.toLowerCase();
                    document.querySelectorAll('table tbody tr').forEach(function(tr) {
                        let name = tr.querySelector('.service-name')?.value?.toLowerCase() || '';
                        let link = tr.querySelector('.service-link')?.value?.toLowerCase() || '';
                        tr.style.display = (name.includes(q) || link.includes(q)) ? '' : 'none';
                    });
                });
            }

            // Marque les lignes modifiées
            document.querySelectorAll('tr[data-service-id]').forEach(function(tr) {
                let nameInput = tr.querySelector('.service-name');
                let linkInput = tr.querySelector('.service-link');
                if (nameInput) {
                    nameInput.addEventListener('input', function() {
                        tr.classList.add('modified');
                    });
                }
                if (linkInput) {
                    linkInput.addEventListener('input', function() {
                        tr.classList.add('modified');
                    });
                }
            });

            // Gestion du lock/délock/sauvegarde
            document.querySelectorAll('.global-lock-btn').forEach(function(lockBtn) {
                let isLocked = true;
                let icon = lockBtn.querySelector('i');

                lockBtn.addEventListener('click', function() {
                    let rows = document.querySelectorAll('tr[data-service-id]');
                    if (isLocked) {
                        // Déverrouille
                        rows.forEach(function(tr) {
                            tr.querySelectorAll('input').forEach(input => input
                                .removeAttribute('readonly'));
                        });
                        icon.classList.remove('fa-lock');
                        icon.classList.add('fa-floppy-disk');
                        lockBtn.title = "Sauvegarder";
                        isLocked = false;
                    } else {
                        // Affiche la modale de confirmation
                        let modal = document.getElementById('confirm-modal');
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                        // Gestion des boutons Oui / Non
                        document.getElementById('confirm-yes').onclick = function() {
                            modal.classList.add('hidden');
                            modal.classList.remove('flex');
                            // Sauvegarde SEULEMENT les lignes modifiées puis reverrouille
                            let fetches = [];
                            rows.forEach(function(tr) {
                                if (!tr.classList.contains('modified')) return;
                                let id = tr.getAttribute('data-service-id');
                                let name = tr.querySelector('.service-name').value;
                                let link = tr.querySelector('.service-link').value;
                                fetches.push(
                                    fetch("{{ url('service') }}/" + id, {
                                        method: 'PUT',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json',
                                        },
                                        body: JSON.stringify({
                                            name,
                                            link
                                        })
                                    })
                                );
                            });

                            Promise.all(fetches).then(function() {
                                rows.forEach(function(tr) {
                                    tr.querySelectorAll('input').forEach(
                                        input => input.setAttribute(
                                            'readonly', true));
                                    tr.classList.remove('modified');
                                });
                                icon.classList.remove('fa-floppy-disk');
                                icon.classList.add('fa-lock');
                                lockBtn.title = "Déverrouiller";
                                isLocked = true;
                            });
                        };

                        document.getElementById('confirm-no').onclick = function() {
                            modal.classList.add('hidden');
                            modal.classList.remove('flex');
                        };
                    }
                });
            });
        });
    </script>
</x-app-layout>
