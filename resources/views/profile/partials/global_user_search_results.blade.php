<article class="w-[80%] max-h-[600px] overflow-y-auto flex justify-center">
    <div class="flex gap-2 w-full">
        @if($societies->count())
            <div class="bg-white rounded-lg p-4 min-w-[300px] max-w-xs flex-shrink-0">
                <div class="flex items-center mb-2">
                    <span class="text-blue-600 mr-2"><i class="la la-building"></i></span>
                    <h3 class="font-bold text-lg">Sociétés</h3>
                </div>
                <ul class="list-disc list-inside text-gray-700">
                    @foreach($societies as $society)
                        <li>{{ $society->name }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($problems->count())
            <div class="bg-white rounded-lg p-4 min-w-[300px] max-w-xs flex-shrink-0">
                <div class="flex items-center mb-2">
                    <span class="text-red-600 mr-2"><i class="la la-exclamation-circle"></i></span>
                    <h3 class="font-bold text-lg">Problèmes</h3>
                </div>
                <ul class="list-disc list-inside text-gray-700">
                    @foreach($problems as $problem)
                        <li>{{ $problem->title }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($problemStatuses->count())
            <div class="bg-white rounded-lg p-4 min-w-[300px] max-w-xs flex-shrink-0">
                <div class="flex items-center mb-2">
                    <span class="text-green-600 mr-2"><i class="la la-flag"></i></span>
                    <h3 class="font-bold text-lg">Statuts</h3>
                </div>
                <ul class="list-disc list-inside text-gray-700">
                    @foreach($problemStatuses as $status)
                        <li>{{ $status->name }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($interlocutors->count())
            <div class="bg-white rounded-lg p-4 min-w-[300px] max-w-xs flex-shrink-0">
                <div class="flex items-center mb-2">
                    <span class="text-yellow-600 mr-2"><i class="la la-user"></i></span>
                    <h3 class="font-bold text-lg">Interlocuteurs</h3>
                </div>
                <ul class="list-disc list-inside text-gray-700">
                    @foreach($interlocutors as $interlocutor)
                        <li>{{ $interlocutor->fullname ?? $interlocutor->name }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($envs->count())
            <div class="bg-white rounded-lg p-4 min-w-[300px] max-w-xs flex-shrink-0">
                <div class="flex items-center mb-2">
                    <span class="text-indigo-600 mr-2"><i class="la la-globe"></i></span>
                    <h3 class="font-bold text-lg">Environnements</h3>
                </div>
                <ul class="list-disc list-inside text-gray-700">
                    @foreach($envs as $env)
                        <li>{{ $env->name }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($tools->count())
            <div class="bg-white rounded-lg p-4 min-w-[300px] max-w-xs flex-shrink-0">
                <div class="flex items-center mb-2">
                    <span class="text-purple-600 mr-2"><i class="la la-wrench"></i></span>
                    <h3 class="font-bold text-lg">Outils</h3>
                </div>
                <ul class="list-disc list-inside text-gray-700">
                    @foreach($tools as $tool)
                        <li>{{ $tool->name }}</li>
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
            <div class="text-gray-400 text-center py-8 min-w-[300px] flex-shrink-0">Aucun résultat trouvé.</div>
        @endif
    </div>
</article>