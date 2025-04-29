@if ($q)

    <div class="card mt-3 mb-3 shadow border-0" style="background:#232136;">
        <div class="card-body">
            <h5 class="mb-4 text-primary">Résultats pour <span class="fw-bold">"{{ $q }}"</span></h5>

            @if (isset($fieldValue))
                <div class="alert alert-info mt-2">
                    <strong>Valeur du champ "{{ $q }}" :</strong> {{ $fieldValue }}
                </div>
            @endif

            @if ($users->count())
                <strong class="text-info">Utilisateurs</strong>
                <ul class="list-group list-group-flush mb-3">
                    @foreach ($users as $user)
                        <li class="list-group-item bg-transparent text-light border-0 ps-0">
                            <i class="la la-user me-2 text-primary"></i>
                            <a href="{{ url('admin/user/' . $user->id . '/show') }}"
                                class="text-light text-decoration-underline">
                                {{ $user->name }}
                            </a>
                            <span class="text-muted">({{ $user->email }})</span>
                        </li>
                    @endforeach
                </ul>
            @endif

            @if ($societies->count())
                <strong class="text-info">Sociétés</strong>
                <ul class="list-group list-group-flush mb-3">
                    @foreach ($societies as $society)
                        <li class="list-group-item bg-transparent text-light border-0 ps-0">
                            <i class="la la-building me-2 text-primary"></i>
                            <a href="{{ url('admin/society/' . $society->id . '/show') }}"
                                class="text-light text-decoration-underline">
                                {{ $society->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif

            @if ($techs->count())
                <strong class="text-info">Techniciens</strong>
                <ul class="list-group list-group-flush mb-3">
                    @foreach ($techs as $tech)
                        <li class="list-group-item bg-transparent text-light border-0 ps-0">
                            <i class="la la-user-cog me-2 text-primary"></i>
                            <a href="{{ url('admin/tech/' . $tech->id . '/show') }}"
                                class="text-light text-decoration-underline">
                                {{ $tech->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif

            @if ($problems->count())
                <strong class="text-info">Problèmes</strong>
                <ul class="list-group list-group-flush mb-3">
                    @foreach ($problems as $problem)
                        <li class="list-group-item bg-transparent text-light border-0 ps-0">
                            <i class="la la-exclamation-triangle me-2 text-primary"></i>
                            <a href="{{ url('admin/problem/' . $problem->id . '/show') }}"
                                class="text-light text-decoration-underline">
                                {{ $problem->title ?? $problem->id }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif

            @if ($problemStatuses->count())
                <strong class="text-info">Statuts</strong>
                <ul class="list-group list-group-flush mb-3">
                    @foreach ($problemStatuses as $status)
                        <li class="list-group-item bg-transparent text-light border-0 ps-0">
                            <i class="la la-flag me-2 text-primary"></i>
                            <a href="{{ url('admin/problemstatus/' . $status->id . '/show') }}"
                                class="text-light text-decoration-underline">
                                {{ $status->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif

            @if ($interlocutors->count())
                <strong class="text-info">Interlocuteurs</strong>
                <ul class="list-group list-group-flush mb-3">
                    @foreach ($interlocutors as $interlocutor)
                        <li class="list-group-item bg-transparent text-light border-0 ps-0">
                            <i class="la la-user-friends me-2 text-primary"></i>
                            <a href="{{ url('admin/interlocutor/' . $interlocutor->id . '/show') }}"
                                class="text-light text-decoration-underline">
                                {{ $interlocutor->fullname ?? $interlocutor->name }}
                            </a>
                            @if ($interlocutor->email)
                                <span class="text-muted">({{ $interlocutor->email }})</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif

            @if ($envs->count())
                <strong class="text-info">Environnements</strong>
                <ul class="list-group list-group-flush mb-3">
                    @foreach ($envs as $env)
                        <li class="list-group-item bg-transparent text-light border-0 ps-0">
                            <i class="la la-server me-2 text-primary"></i>
                            <a href="{{ url('admin/env/' . $env->id . '/show') }}"
                                class="text-light text-decoration-underline">
                                {{ $env->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif

            @if ($tools->count())
                <strong class="text-info">Outils</strong>
                <ul class="list-group list-group-flush mb-3">
                    @foreach ($tools as $tool)
                        <li class="list-group-item bg-transparent text-light border-0 ps-0">
                            <i class="la la-wrench me-2 text-primary"></i>
                            <a href="{{ url('admin/tool/' . $tool->id . '/show') }}"
                                class="text-light text-decoration-underline">
                                {{ $tool->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif

            @if ($menus->count())
                <strong class="text-info">Menus</strong>
                <ul class="list-group list-group-flush mb-3">
                    @foreach ($menus as $menu)
                        <li class="list-group-item bg-transparent text-light border-0 ps-0">
                            <i class="la la-bars me-2 text-primary"></i>
                            <a href="{{ url('admin/menu/' . $menu->id . '/show') }}"
                                class="text-light text-decoration-underline">
                                {{ $menu->title }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif

            @if (
                !$users->count() &&
                    !$societies->count() &&
                    !$techs->count() &&
                    !$problems->count() &&
                    !$problemStatuses->count() &&
                    !$interlocutors->count() &&
                    !$envs->count() &&
                    !$tools->count() &&
                    !$menus->count())
                <em class="text-secondary">Aucun résultat trouvé.</em>
            @endif
        </div>
    </div>
@endif
