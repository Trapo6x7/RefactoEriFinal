@if (!Auth::user() || Auth::user()->role !== 'superadmin')
    <script>
        window.location = "{{ route('dashboard') }}";
    </script>
@endif

<x-app-layout>
    <div class="flex flex-col gap-8 justify-center items-center w-full min-h-screen px-2 md:px-0 bg-off-white">

        <!-- Colonne Utilisateurs -->
        <div class="w-full max-w-5xl h-auto flex flex-col bg-white/80 rounded-2xl p-4 md:p-8">
            <h1 class="text-2xl md:text-3xl font-extrabold my-6 md:my-8 text-center uppercase text-blue-accent tracking-wide">
                Liste des Techniciens
            </h1>
            <div class="w-full mx-auto px-0 md:px-2 mb-6 md:mb-10 flex-1 flex flex-col overflow-hidden">
                <!-- Formulaire d'ajout -->
                <form method="POST" action="{{ route('user-tech.user.store') }}"
                    class="mb-4 flex flex-col gap-2 justify-center items-center sm:flex-row sm:flex-wrap bg-off-white rounded-xl p-3">
                    @csrf
                    <input type="text" name="name" placeholder="Nom" required
                        class="appearance-none border-2 border-blue-accent rounded-lg px-4 py-2 w-full sm:w-auto bg-white text-primary-grey focus:outline-none focus:ring-2 focus:ring-blue-accent transition placeholder:text-blue-accent" />
                    <input type="email" name="email" placeholder="Email" required
                        class="appearance-none border-2 border-blue-accent rounded-lg px-4 py-2 w-full sm:w-auto bg-white text-primary-grey focus:outline-none focus:ring-2 focus:ring-blue-accent transition placeholder:text-blue-accent" />
                    <input type="password" name="password" placeholder="Mot de passe" required
                        class="appearance-none border-2 border-blue-accent rounded-lg px-4 py-2 w-full sm:w-auto bg-white text-primary-grey focus:outline-none focus:ring-2 focus:ring-blue-accent transition placeholder:text-blue-accent" />
                    <select name="role" required
                        class="appearance-none border-2 border-blue-accent rounded-lg px-4 py-2 w-full sm:w-auto bg-white text-primary-grey focus:outline-none focus:ring-2 focus:ring-blue-accent transition">
                        <option value="" disabled selected>Rôle</option>
                        <option value="user">Utilisateur</option>
                        <option value="admin">Admin</option>
                        <option value="superadmin">Superadmin</option>
                    </select>
                    <button type="submit"
                        class="bg-blue-accent hover:bg-blue-hover text-white px-4 py-2 rounded-lg text-sm w-full sm:w-auto font-semibold transition">Ajouter</button>
                </form>
                <input type="text" id="search-tech" placeholder="Rechercher un technicien..."
                    class="mb-4 px-4 py-2 border border-blue-accent rounded-lg w-full max-w-md mx-auto focus:ring-2 focus:ring-blue-accent focus:outline-none bg-white text-primary-grey placeholder:text-blue-accent" />
                <div class="flex items-center mb-2 text-blue-accent">
                    <button type="button" class="global-lock-btn text-lg mr-2 hover:text-blue-hover transition"
                        data-target="users" title="Déverrouiller">
                        <i class="fa-solid fa-lock"></i>
                    </button>
                </div>
                <div class="rounded-xl flex-1 overflow-x-auto bg-white/70"
                    style="max-height: calc(100% - 100px);">

                    <table class="w-full text-sm md:text-base lg:text-lg text-center min-w-[600px] sm:min-w-0">
                        <thead>
                            <tr>
                                <th class="py-2 px-2 md:px-4 border-b text-center bg-blue-accent text-white font-semibold rounded-tl-xl">
                                    Nom</th>
                                <th class="py-2 px-2 md:px-4 border-b text-center bg-blue-accent text-white font-semibold">
                                    Rôle</th>
                                <th class="py-2 px-2 md:px-4 border-b text-center bg-blue-accent text-white font-semibold">
                                    Email</th>
                                <th class="py-2 px-2 md:px-4 border-b text-center bg-blue-accent text-white font-semibold">
                                    Mot de passe</th>
                                <th class="py-2 px-2 md:px-4 border-b text-center bg-blue-accent text-white font-semibold rounded-tr-xl">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr class="border-b transition hover:bg-off-white data-[modified=true]:bg-yellow-100"
                                    data-user-id="{{ $user->id }}">
                                    <!-- Nom -->
                                    <td class="@if ($loop->index % 2 == 1) bg-off-white @endif text-center align-middle">
                                        <input type="text" value="{{ $user->name }}"
                                            class="border border-blue-accent rounded px-2 py-1 w-full text-center user-name text-sm md:text-base lg:text-lg bg-white/80 text-primary-grey focus:ring-2 focus:ring-blue-accent transition"
                                            readonly>
                                    </td>
                                    <!-- Rôle -->
                                    <td class="@if ($loop->index % 2 == 0) bg-off-white @endif text-center align-middle">
                                        <select
                                            class="border border-blue-accent rounded px-2 py-1 w-full text-center user-role text-sm md:text-base lg:text-lg bg-white/80 text-primary-grey focus:ring-2 focus:ring-blue-accent transition"
                                            disabled>
                                            <option value="user" @if ($user->role == 'user') selected @endif>
                                                Utilisateur</option>
                                            <option value="admin" @if ($user->role == 'admin') selected @endif>
                                                Admin</option>
                                            <option value="superadmin" @if ($user->role == 'superadmin') selected @endif>
                                                Superadmin</option>
                                        </select>
                                    </td>
                                    <!-- Email -->
                                    <td class="@if ($loop->index % 2 == 1) bg-off-white @endif text-center align-middle">
                                        <input type="email" value="{{ $user->email }}"
                                            class="border border-blue-accent rounded px-2 py-1 w-full text-center user-email text-sm md:text-base lg:text-lg bg-white/80 text-primary-grey focus:ring-2 focus:ring-blue-accent transition"
                                            readonly>
                                    </td>
                                    <!-- Mot de passe (éditable) -->
                                    <td class="@if ($loop->index % 2 == 0) bg-off-white @endif text-center align-middle">
                                        <input type="password" value="********" placeholder="Nouveau mot de passe"
                                            class="border border-blue-accent rounded px-2 py-1 w-full text-center user-password text-sm md:text-base lg:text-lg bg-white/80 text-primary-grey focus:ring-2 focus:ring-blue-accent transition"
                                            readonly>
                                    </td>
                                    <!-- Actions -->
                                    <td class="text-center align-middle">
                                        <form method="POST" action="{{ route('user-tech.user.destroy', $user) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="bg-red-accent hover:bg-red-hover text-white px-3 py-1 rounded-lg text-xs flex items-center justify-center mx-auto transition">
                                                <i class="fa-solid fa-trash mr-1"></i> Supprimer
                                            </button>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ajoute les labels pour mobile/tablette
            if (window.innerWidth <= 768) {
                document.querySelectorAll('table tbody tr').forEach(function(tr) {
                    let labels = ['Nom', 'Rôle', 'Email', 'Mot de passe', 'Actions'];
                    tr.querySelectorAll('td').forEach(function(td, i) {
                        td.setAttribute('data-label', labels[i]);
                    });
                });
            }

            // Recherche technicien (fonctionne même avec input/select)
            const searchInput = document.getElementById('search-tech');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    let q = this.value.toLowerCase();
                    document.querySelectorAll('table tbody tr').forEach(function(tr) {
                        let values = [];
                        tr.querySelectorAll('input, select').forEach(function(el) {
                            if (el.type === 'password') return; // ignore le champ mot de passe masqué
                            if (el.tagName === 'SELECT') {
                                values.push(el.options[el.selectedIndex]?.text || '');
                            } else {
                                values.push(el.value);
                            }
                        });
                        let txt = values.join(' ').toLowerCase();
                        tr.style.display = txt.includes(q) ? '' : 'none';
                    });
                });
            }

            // Gestion du lock/délock/sauvegarde
            document.querySelectorAll('.global-lock-btn').forEach(function(lockBtn) {
                let isLocked = true;
                let target = lockBtn.getAttribute('data-target');
                let icon = lockBtn.querySelector('i');

                // Marque les lignes modifiées
                function markModifiedRows() {
                    if (target === 'users') {
                        document.querySelectorAll('tr[data-user-id]').forEach(function(tr) {
                            let nameInput = tr.querySelector('.user-name');
                            let roleSelect = tr.querySelector('.user-role');
                            let emailInput = tr.querySelector('.user-email');
                            let passwordInput = tr.querySelector('.user-password');
                            if (nameInput) {
                                nameInput.addEventListener('input', function() {
                                    tr.classList.add('modified');
                                });
                            }
                            if (roleSelect) {
                                roleSelect.addEventListener('change', function() {
                                    tr.classList.add('modified');
                                });
                            }
                            if (emailInput) {
                                emailInput.addEventListener('input', function() {
                                    tr.classList.add('modified');
                                });
                            }
                            if (passwordInput) {
                                passwordInput.addEventListener('input', function() {
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
                    }

                    if (isLocked) {
                        // Déverrouille et passe en disquette
                        rows.forEach(function(tr) {
                            let inputs = tr.querySelectorAll('input');
                            let select = tr.querySelector('select');
                            inputs.forEach(input => input.removeAttribute('readonly'));
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
                                if (!tr.classList.contains('modified')) return;
                                if (target === 'users') {
                                    let userId = tr.getAttribute('data-user-id');
                                    let name = tr.querySelector('.user-name').value;
                                    let role = tr.querySelector('.user-role').value;
                                    let email = tr.querySelector('.user-email').value;
                                    let password = tr.querySelector('.user-password').value;
                                    let data = {
                                        name,
                                        role,
                                        email
                                    };
                                    if (password) data.password = password;
                                    fetches.push(
                                        fetch(`/user-tech/user/${userId}`, {
                                            method: 'PUT',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Accept': 'application/json'
                                            },
                                            body: JSON.stringify(data)
                                        })
                                    );
                                }
                            });

                            Promise.all(fetches).then(function() {
                                rows.forEach(function(tr) {
                                    let inputs = tr.querySelectorAll('input');
                                    let select = tr.querySelector('select');
                                    inputs.forEach(input => input.setAttribute('readonly', true));
                                    if (select) select.setAttribute('disabled', true);
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