<x-public-layout>
    <x-clay-card class="mx-auto max-w-3xl text-center">
        <p class="eyebrow">@yield('code')</p>
        <h1 class="mt-4 text-3xl font-extrabold">@yield('title')</h1>
        <p class="mt-4 text-slate-600">@yield('message')</p>
        <x-clay-button href="/" variant="secondary" class="mt-6">Kembali ke beranda</x-clay-button>
    </x-clay-card>
</x-public-layout>
