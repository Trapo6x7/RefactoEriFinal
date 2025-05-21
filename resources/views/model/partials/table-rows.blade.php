@foreach ($items as $item)
    <tr class="border-b">
        <td class="py-2 px-4 text-center bg-off-white hover:text-blue-accent">
            <a href="{{ route('model.show', ['model' => $model, 'id' => $item->id]) }}"
               class="ml-4 text-blue-500 hover:underline">
                @if ($model === 'interlocuteur')
                    {{ $item->fullname ?? '-' }}
                @elseif ($model === 'probleme')
                    {{ $item->title ?? '-' }}
                @else
                    {{ $item->name ?? '-' }}
                @endif
            </a>
        </td>
    </tr>
@endforeach