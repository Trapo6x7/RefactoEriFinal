@if (!Auth::user() || Auth::user()->role !== 'superadmin')
    <script>
        window.location = "{{ route('dashboard') }}";
    </script>
@endif

<x-app-layout>
    <div class="flex gap-8 justify-center flex-col items-center w-full h-screen">

        <!-- Colonne Utilisateurs -->
        <div class="w-full md:w-1/2 h-1/2 flex flex-col">
            <h1 class="text-3xl font-extrabold my-8 text-center uppercase text-blue-accent">Liste des Utilisateurs</h1>
            <div class="w-full mx-auto px-4 mb-10 flex-1 flex flex-col overflow-hidden">
                <!-- Formulaire d'ajout -->
                <form method="POST" action="{{ route('user-tech.user.store') }}"
                    class="mb-4 flex gap-2 justify-center items-center">
                    @csrf
                    <input type="text" name="name" placeholder="Nom" required
                        class="appearance-none border-2 border-blue-accent rounded-lg px-4 py-2 w-full bg-white text-blue-accent focus:outline-none focus:ring-2 focus:ring-blue-accent transition">
                    <input type="email" name="email" placeholder="Email" required
                        class="appearance-none border-2 border-blue-accent rounded-lg px-4 py-2 w-full bg-white text-blue-accent focus:outline-none focus:ring-2 focus:ring-blue-accent transition">
                    <input type="password" name="password" placeholder="Mot de passe" required
                        class="appearance-none border-2 border-blue-accent rounded-lg px-4 py-2 w-full bg-white text-blue-accent focus:outline-none focus:ring-2 focus:ring-blue-accent transition">
                    <select name="role" required
                        class="appearance-none border-2 border-blue-accent rounded-lg px-4 py-2 w-full bg-white text-blue-accent focus:outline-none focus:ring-2 focus:ring-blue-accent transition">
                        <option value="" disabled selected>Rôle</option>
                        <option value="user">Utilisateur</option>
                        <option value="admin">Admin</option>
                        <option value="superadmin">Superadmin</option>
                    </select>
                    <button type="submit" class="bg-blue-accent text-white px-3 py-1 rounded text-sm">Ajouter</button>
                </form>
                <div class="flex items-center mb-2 text-blue-accent">
                    <button type="button" class="global-lock-btn text-md mr-2" data-target="users"
                        title="Déverrouiller">
                        <i class="fa-solid fa-lock"></i>
                    </button>
                </div>
                <div class="rounded-lg flex-1 overflow-y-auto" style="max-height: calc(100% - 100px);">
                    <table class="w-full text-lg text-center">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b text-center bg-blue-accent text-secondary-grey">Nom</th>
                                <th class="py-2 px-4 border-b text-center bg-blue-accent text-secondary-grey">Rôle</th>
                                <th class="py-2 px-4 border-b text-center bg-blue-accent text-secondary-grey">Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr class="border-b" data-user-id="{{ $user->id }}">
                                    <td class="@if ($loop->index % 2 == 1) bg-secondary-grey @endif text-center">
                                        <input type="text" value="{{ $user->name }}"
                                            class="border rounded px-2 py-1 w-full text-center user-name" readonly>
                                    </td>
                                    <td class="@if ($loop->index % 2 == 0) bg-secondary-grey @endif text-center">
                                        <select class="border rounded px-2 py-1 w-full text-center user-role" disabled>
                                            <option value="user" @if ($user->role == 'user') selected @endif>
                                                Utilisateur</option>
                                            <option value="admin" @if ($user->role == 'admin') selected @endif>
                                                Admin</option>
                                            <option value="superadmin"
                                                @if ($user->role == 'superadmin') selected @endif>Superadmin</option>
                                        </select>
                                    </td>
                                    <td class="text-center">
                                        <form method="POST" action="{{ route('user-tech.user.destroy', $user) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="bg-red-accent text-white px-2 py-1 rounded text-sm flex items-center justify-center mx-auto">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="border-b border-blue-accent w-3/4"></div>

        <!-- Colonne Techniciens -->
        <div class="w-full md:w-1/2 h-1/2 flex flex-col">
            <h1 class="text-3xl font-extrabold my-8 text-center uppercase text-blue-accent">Liste des Techniciens</h1>
            <div class="w-full mx-auto px-4 flex-1 flex flex-col overflow-hidden">
                <!-- Formulaire d'ajout -->
                <div class="flex items-center justify-center">
                    <form method="POST" action="{{ route('user-tech.tech.store') }}" class="mb-4 flex gap-2 w-1/2">
                        @csrf
                        <input type="text" name="name" placeholder="Nom" required
                            class="appearance-none border-2 border-blue-accent rounded-lg px-4 py-2 w-full bg-white text-blue-accent focus:outline-none focus:ring-2 focus:ring-blue-accent transition">
                        <button type="submit"
                            class="bg-blue-accent text-white px-3 py-1 rounded text-sm">Ajouter</button>
                    </form>
                </div>
                <div class="flex items-center mb-2 text-blue-accent">
                    <button type="button" class="global-lock-btn text-md mr-2" data-target="techs"
                        title="Déverrouiller">
                        <i class="fa-solid fa-lock"></i>
                    </button>
                </div>
                <div class="rounded-lg flex-1 overflow-y-auto" style="max-height: calc(100% - 100px);">
                    <table class="w-full text-lg text-center">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b text-center bg-blue-accent text-secondary-grey">Nom</th>
                                <th class="py-2 px-4 border-b text-center bg-blue-accent text-secondary-grey">Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($techs as $tech)
                                <tr class="border-b" data-tech-id="{{ $tech->id }}">
                                    <td class="@if ($loop->index % 2 == 1) bg-secondary-grey @endif text-center">
                                        <input type="text" value="{{ $tech->name }}"
                                            class="border rounded px-2 py-1 w-full text-center tech-name" readonly>
                                    </td>
                                    <td class="text-center">
                                        <form method="POST" action="{{ route('user-tech.tech.destroy', $tech) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="bg-red-accent text-white px-2 py-1 rounded text-sm flex items-center justify-center mx-auto">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Modale de confirmation -->
    <div id="confirm-modal" class="fixed inset-0 bg-black bg-opacity-40 items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-lg p-8 max-w-sm w-full text-center">
            <h2 class="text-xl font-bold mb-4">Confirmer la sauvegarde</h2>
            <p class="mb-6">Voulez-vous vraiment enregistrer les modifications&nbsp;?</p>
            <div class="flex justify-center gap-4">
                <button id="confirm-yes" class="bg-blue-accent text-white px-4 py-2 rounded">Oui</button>
                <button id="confirm-no" class="bg-gray-300 px-4 py-2 rounded">Non</button>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.global-lock-btn').forEach(function(lockBtn) {
            let isLocked = true;
            let target = lockBtn.getAttribute('data-target');
            let icon = lockBtn.querySelector('i');

            // Ajout : marquer les lignes modifiées
            function markModifiedRows() {
                if (target === 'users') {
                    document.querySelectorAll('tr[data-user-id]').forEach(function(tr) {
                        let input = tr.querySelector('.user-name');
                        let select = tr.querySelector('.user-role');
                        if (input) {
                            input.addEventListener('input', function() {
                                tr.classList.add('modified');
                            });
                        }
                        if (select) {
                            select.addEventListener('change', function() {
                                tr.classList.add('modified');
                            });
                        }
                    });
                } else if (target === 'techs') {
                    document.querySelectorAll('tr[data-tech-id]').forEach(function(tr) {
                        let input = tr.querySelector('.tech-name');
                        if (input) {
                            input.addEventListener('input', function() {
                                tr.classList.add('modified');
                            });
                        }
                    });
                }
            }
            markModifiedRows();

            lockBtn.addEventListener('click', function() {
                let rows = [];
                if (target === 'users') {
                    rows = document.querySelectorAll('tr[data-user-id]');
                } else if (target === 'techs') {
                    rows = document.querySelectorAll('tr[data-tech-id]');
                }

                if (isLocked) {
                    // Déverrouille et passe en disquette
                    rows.forEach(function(tr) {
                        let input = tr.querySelector('input');
                        let select = tr.querySelector('select');
                        if (input) input.removeAttribute('readonly');
                        if (select) select.removeAttribute('disabled');
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
                            if (!tr.classList.contains('modified'))
                        return; // <-- SEULEMENT modifiés
                            if (target === 'users') {
                                let userId = tr.getAttribute('data-user-id');
                                let name = tr.querySelector('.user-name').value;
                                let role = tr.querySelector('.user-role').value;
                                fetches.push(
                                    fetch(`/user-tech/user/${userId}`, {
                                        method: 'PUT',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json'
                                        },
                                        body: JSON.stringify({
                                            name,
                                            role
                                        })
                                    })
                                );
                            } else if (target === 'techs') {
                                let techId = tr.getAttribute('data-tech-id');
                                let name = tr.querySelector('.tech-name').value;
                                fetches.push(
                                    fetch(`/user-tech/tech/${techId}`, {
                                        method: 'PUT',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json'
                                        },
                                        body: JSON.stringify({
                                            name
                                        })
                                    })
                                );
                            }
                        });

                        Promise.all(fetches).then(function() {
                            rows.forEach(function(tr) {
                                let input = tr.querySelector('input');
                                let select = tr.querySelector('select');
                                if (input) input.setAttribute('readonly', true);
                                if (select) select.setAttribute('disabled', true);
                                tr.classList.remove(
                                'modified'); // Nettoie la classe modifiée
                            });
                            icon.classList.remove('fa-floppy-disk');
                            icon.classList.add('fa-lock');
                            lockBtn.title = "Déverrouiller";
                            isLocked = true;
                        });
                    };

                    document.getElementById('confirm-no').onclick = function() {
                        modal.classList.add('hidden');
                    };
                }
            });
        });
    </script>

</x-app-layout>
