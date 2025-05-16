<!-- filepath: c:\Users\Utilisateur\baseDeConnaissance\resources\views\components\responsive-nav-link.blade.php -->
@props(['active'])

@php
$classes = ($active ?? false)
    ? ' text-blue-accent font-bold text-lg text-center w-full py-2 transition-colors duration-200'
    : ' text-primary-grey font-bold text-lg text-center w-full py-2 hover:text-blue-accent transition-colors duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>