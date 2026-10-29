@props(['url' => null])
<aside class="ad-slot" aria-label="Iklan">
    @if ($url)
        <a href="{{ $url }}" target="_blank" rel="sponsored noopener noreferrer" aria-label="Iklan (buka di tab baru)">
    @endif
    <img src="{{ route('ads.image', [], false) }}" width="874" height="1280" alt="Iklan" loading="lazy" decoding="async">
    @if ($url)
        </a>
    @endif
</aside>
