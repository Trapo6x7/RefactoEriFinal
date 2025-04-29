@extends(backpack_view('layouts.' . (backpack_theme_config('layout') ?? 'vertical')))

@php
    if (isset($widgets)) {
        foreach ($widgets as $section => $widgetSection) {
            foreach ($widgetSection as $key => $widget) {
                \Backpack\CRUD\app\Library\Widget::add($widget)->section($section);
            }
        }
    }
@endphp

@section('before_breadcrumbs_widgets')

<div class="row mb-5 align-items-center">
    <div class="col">
        <h1 class="display-4 fw-bold text-primary">
            {{ __('BIENVENUE, :name', ['name' => strtoupper(Auth::user()->name)]) }}
        </h1>
    </div>
    <div class="col-auto">
        <a class="btn btn-primary" href="{{ backpack_url('logout') }}" role="button">Déconnexion</a>
    </div>
</div>
@parent
@endsection

@section('after_breadcrumbs_widgets')
    @include(backpack_view('inc.widgets'), [
        'widgets' => app('widgets')->where('section', 'after_breadcrumbs')->toArray(),
    ])
@endsection

@section('before_content_widgets')
    @include(backpack_view('inc.widgets'), [
        'widgets' => app('widgets')->where('section', 'before_content')->toArray(),
    ])
@endsection

@section('content')

@endsection

@section('after_content_widgets')
    @include(backpack_view('inc.widgets'), [
        'widgets' => app('widgets')->where('section', 'after_content')->toArray(),
    ])
@endsection
