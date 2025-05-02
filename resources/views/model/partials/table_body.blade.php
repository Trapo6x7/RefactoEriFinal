@foreach ($items as $item)
    <tr class="border-b">
        <td class="py-2 px-4 text-center bg-off-white hover:text-blue-accent">
            {{ $item->name ?? ($item->title ?? '-') }}</td>
    </tr>
@endforeach
