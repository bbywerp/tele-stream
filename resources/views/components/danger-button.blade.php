<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'clay-button clay-danger']) }}>
    {{ $slot }}
</button>
