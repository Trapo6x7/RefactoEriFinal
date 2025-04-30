<x-app-layout>
    <h1 class="text-2xl font-bold mb-4">Liste des {{ ucfirst($model) }}s</h1>
    <div class="container mx-auto py-8">
        <table class="min-w-full bg-white">
            <thead>
                <tr>
                    @foreach($items->first()?->getAttributes() ?? [] as $key => $value)
                        <th class="py-2 px-4 border-b">{{ $key }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        @foreach($item->getAttributes() as $value)
                            <td class="py-2 px-4 border-b">{{ $value }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>