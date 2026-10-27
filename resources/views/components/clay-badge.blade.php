@props(['active' => true])
<span
    {{ $attributes->merge(['class' => 'badge ' . ($active ? 'bg-cyan-100 text-cyan-900' : 'bg-amber-100 text-amber-900')]) }}>{{ $slot }}</span>
