<article class="w-auto max-h-[400px] overflow-y-auto flex justify-around">
    <div class="flex gap-2 w-full">
        @if ($societies->count())
            <div class=" rounded-lg p-4 min-w-[300px] max-w-xs flex-shrink-0">
                <div class="flex items-center mb-2">
                    <span class="mr-2"><i class="la la-building"></i></span>
                    <h3 class="font-bold text-lg text-primary-grey">Sociétés</h3>
                </div>
                <ul class="text-primary-grey">
                    @php
                        $allowedKeys = ['name', 'adress', 'boss_name', 'boss_phone', 'recep_phone', 'main'];
                    @endphp

                    @foreach ($societies as $society)
                        <li>
                            @if ($society->main)
                                <a href="{{ route('model.show', ['model' => 'société', 'id' => $society->main->id]) }}"
                                    class="search-result-link text-primary-grey hover:text-blue-accent"
                                    data-model="société"
                                    @foreach (\Illuminate\Support\Arr::only($society->getAttributes(), $allowedKeys) as $key => $value)
                                    data-{{ $key }}="{{ $value }}" @endforeach
                                    data-id="{{ $society->id }}" data-main_name="{{ $society->main->name }}">
                                    {{ $society->name }}
                                    <span
                                        class="ml-2 text-xs text-blue-hover font-semibold">({{ $society->main->name }})</span>
                                </a>
                            @else
                                <a href="{{ route('model.show', ['model' => 'société', 'id' => $society->id]) }}"
                                    class="search-result-link text-primary-grey hover:text-blue-accent"
                                    data-model="société"
                                    @foreach (\Illuminate\Support\Arr::only($society->getAttributes(), $allowedKeys) as $key => $value)
                                    data-{{ $key }}="{{ $value }}" @endforeach
                                    data-id="{{ $society->id }}">
                                    {{ $society->name }}
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($interlocutors->count())
            <div class=" rounded-lg p-4 min-w-[300px] max-w-xs flex-shrink-0">
                <div class="flex items-center mb-2">
                    <span class="mr-2"><i class="la la-user"></i></span>
                    <h3 class="font-bold text-lg text-primary-grey">Interlocuteurs</h3>
                </div>
                <ul class="text-primary-grey">
                    @php
                        $allowedKeys = [
                            'fullname',
                            'lastname',
                            'name',
                            'email',
                            'phone_fix',
                            'phone_mobile',
                            'id_teamviewer',
                        ];
                    @endphp

                    @foreach ($interlocutors as $interlocutor)
                        <li>
                            <a href="{{ route('model.show', ['model' => 'interlocuteur', 'id' => $interlocutor->id]) }}"
                                class="search-result-link text-primary-grey hover:text-blue-accent"
                                data-model="interlocuteur"
                                @foreach (\Illuminate\Support\Arr::only($interlocutor->getAttributes(), $allowedKeys) as $key => $value)
            data-{{ $key }}="{{ $value }}" @endforeach
                                data-id="{{ $interlocutor->id }}" data-society_id="{{ $interlocutor->societe }}">
                                {{ $interlocutor->fullname ?? $interlocutor->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (!$societies->count() && !$interlocutors->count())
            <div class="text-primary-grey text-center flex just py-8 min-w-[300px] flex-shrink-0">
                <p>Aucun résultat trouvé.</p>
            </div>
        @endif
    </div>
</article>
