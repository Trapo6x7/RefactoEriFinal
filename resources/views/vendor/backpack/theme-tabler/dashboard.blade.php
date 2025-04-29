@extends(backpack_view('blank'))

@section('content')
@foreach (\App\Models\Menu::orderBy('order')->get() as $item)
@if (!$item->role || $item->role === backpack_user()->role)
    <x-backpack::menu-item :title="$item->title" :icon="$item->icon ?: 'la la-bars'" :link="$item->link" />
@endif
@endforeach
@endsection