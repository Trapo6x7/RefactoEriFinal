<article class="w-[80%] max-h-[600px] overflow-y-auto flex justify-center">
    <div class="flex gap-2 w-full">
        @if($societies->count())
            <div class="bg-white rounded-lg p-4 min-w-[300px] max-w-xs flex-shrink-0">
                <div class="flex items-center mb-2">
                    <span class="text-blue-600 mr-2"><i class="la la-building"></i></span>
                    <h3 class="font-bold text-lg text-primary-grey">Sociétés</h3>
                </div>
                <ul class="text-primary-grey">
                    @foreach($societies as $society)
                        <li>
                            <a href="{{ route('model.show', ['model' => 'société', 'id' => $society->id]) }}"
                               class="text-primary-grey hover:text-blue-accent hover:underline">
                                {{ $society->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($problems->count())
            <div class="bg-white rounded-lg p-4 min-w-[300px] max-w-xs flex-shrink-0">
                <div class="flex items-center mb-2">
                    <span class="text-red-600 mr-2"><i class="la la-exclamation-circle"></i></span>
                    <h3 class="font-bold text-lg text-primary-grey">Problèmes</h3>
                </div>
                <ul class="text-primary-grey">
                    @foreach($problems as $problem)
                        <li>
                            <a href="{{ route('model.show', ['model' => 'problème', 'id' => $problem->id]) }}"
                               class="text-primary-grey hover:text-blue-accent hover:underline">
                                {{ $problem->title }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($problemStatuses->count())
            <div class="bg-white rounded-lg p-4 min-w-[300px] max-w-xs flex-shrink-0">
                <div class="flex items-center mb-2">
                    <span class="text-green-600 mr-2"><i class="la la-flag"></i></span>
                    <h3 class="font-bold text-lg text-primary-grey">Statuts</h3>
                </div>
                <ul class="text-primary-grey">
                    @foreach($problemStatuses as $status)
                        <li>
                            <a href="{{ route('model.show', ['model' => 'problemStatus', 'id' => $status->id]) }}"
                               class="text-primary-grey hover:text-blue-accent hover:underline">
                                {{ $status->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($interlocutors->count())
            <div class="bg-white rounded-lg p-4 min-w-[300px] max-w-xs flex-shrink-0">
                <div class="flex items-center mb-2">
                    <span class="text-yellow-600 mr-2"><i class="la la-user"></i></span>
                    <h3 class="font-bold text-lg text-primary-grey">Interlocuteurs</h3>
                </div>
                <ul class="text-primary-grey">
                    @foreach($interlocutors as $interlocutor)
                        <li>
                            <a href="{{ route('model.show', ['model' => 'interlocuteur', 'id' => $interlocutor->id]) }}"
                               class="text-primary-grey hover:text-blue-accent hover:underline">
                                {{ $interlocutor->fullname ?? $interlocutor->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($envs->count())
            <div class="bg-white rounded-lg p-4 min-w-[300px] max-w-xs flex-shrink-0">
                <div class="flex items-center mb-2">
                    <span class="text-indigo-600 mr-2"><i class="la la-globe"></i></span>
                    <h3 class="font-bold text-lg text-primary-grey">Environnements</h3>
                </div>
                <ul class="text-primary-grey">
                    @foreach($envs as $env)
                        <li>
                            <a href="{{ route('model.show', ['model' => 'environnement', 'id' => $env->id]) }}"
                               class="text-primary-grey hover:text-blue-accent hover:underline">
                                {{ $env->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($tools->count())
            <div class="bg-white rounded-lg p-4 min-w-[300px] max-w-xs flex-shrink-0">
                <div class="flex items-center mb-2">
                    <span class="text-purple-600 mr-2"><i class="la la-wrench"></i></span>
                    <h3 class="font-bold text-lg text-primary-grey">Outils</h3>
                </div>
                <ul class="text-primary-grey">
                    @foreach($tools as $tool)
                        <li>
                            <a href="{{ route('model.show', ['model' => 'outil', 'id' => $tool->id]) }}"
                               class="text-primary-grey hover:text-blue-accent hover:underline">
                                {{ $tool->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(
            !$societies->count() &&
            !$problems->count() &&
            !$problemStatuses->count() &&
            !$interlocutors->count() &&
            !$envs->count() &&
            !$tools->count()
        )
            <div class="text-primary-grey text-center py-8 min-w-[300px] flex-shrink-0">Aucun résultat trouvé.</div>
        @endif
    </div>
</article>