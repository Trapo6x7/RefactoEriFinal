<button {{ $attributes->merge(['type' => 'submit', 'class' => 'px-4 py-1 bg-blue-accent text-off-white rounded-md uppercase font-semibold hover:bg-blue-hover transition flex-shrink-0 text-center']) }}>
    {{ $slot }}
</button>
