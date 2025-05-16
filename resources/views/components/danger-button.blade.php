<button {{ $attributes->merge(['type' => 'submit', 'class' => 'px-4 py-1 bg-red-accent text-off-white rounded-md uppercase font-semibold hover:bg-red-hover transition flex-shrink-0 text-center']) }}>
    {{ $slot }}
</button>
