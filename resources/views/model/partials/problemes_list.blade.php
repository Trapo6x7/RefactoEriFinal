<div class="flex justify-center gap-2 mb-4">
    <input type="text" id="search-problemes-global" placeholder="Rechercher un problème global..." 
        class="p-2 border text-sm rounded w-full max-w-xs" />
    <select id="filter-tool" class="p-2 text-sm border rounded">
        <option value="">Tous les outils</option>
    </select>
    <select id="filter-env" class="p-2 border text-sm rounded">
        <option value="">Tous les env...</option>
    </select>
</div>

{{-- Place this before your problems list --}}
<div id="problemes-list-inner-global"></div>

<div class="flex flex-col items-start w-full">
    <div id="problemes-list-inner-{{ $containerId ?? 'default' }}">
        @foreach($filteredProblemes as $i => $p)
            <div class="mb-2 p-1 bg-off-white rounded text-sm w-full max-w-2xl text-left">
                <button 
                    class="w-full text-left font-semibold text-blue-accent hover:text-blue-hover accordion-title flex items-center gap-2"
                    data-idx="{{ $i }}">
                    <span class="accordion-arrow transition-transform">&#x25BE;</span>
                    {{ $p->title ?? '' }}
                </button>
                <div class="accordion-content mt-2 hidden text-left w-full" id="problem-details-{{ $containerId ?? 'default' }}-{{ $i }}">
                    <p class="text-sm text-primary-grey w-full">
                        @if(!empty($isAdmin) && $isAdmin)
                            <span 
                                class="editable-probleme-field outline-none focus:outline-none" 
                                data-id="{{ $p->id }}" 
                                data-key="description" 
                                contenteditable="true" 
                                style="outline:none;box-shadow:none;">
                                {{ $p->description ?? '' }}
                            </span>
                        @else
                            {{ $p->description ?? '' }}
                        @endif
                    </p>
                </div>
            </div>
        @endforeach
    </div>
</div>