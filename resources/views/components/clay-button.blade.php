@props(['href' => null, 'variant' => 'primary'])
@if ($href)
    <a href="{{ $href }}"
        {{ $attributes->merge(['class' => 'clay-button ' . ($variant === 'secondary' ? 'clay-secondary' : '')]) }}>{{ $slot }}</a>
@else
    <button
        {{ $attributes->merge(['type' => 'submit', 'class' => 'clay-button ' . ($variant === 'secondary' ? 'clay-secondary' : '')]) }}>{{ $slot }}</button>
@endif
