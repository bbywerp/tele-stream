<x-public-layout>
    @if ($video)
        <p class="eyebrow">{{ $video['series']['title'] }}</p>
        <h1 class="mt-3 text-3xl font-extrabold tracking-tight sm:text-4xl">{{ $video['title'] }}</h1>
        <div class="clay-card mt-8 p-2 sm:p-4">
            <iframe src="{{ $video['player_url'] }}" title="{{ $video['title'] }}"
                class="aspect-video w-full rounded-2xl border-0 bg-slate-950"
                allow="accelerometer; gyroscope; autoplay; encrypted-media; picture-in-picture"
                referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
        </div>
        <p class="mt-6 whitespace-pre-line text-slate-600">{{ $video['description'] ?? '' }}</p>
    @else
        <x-clay-card class="text-center">
            <p class="eyebrow">KONTEN TIDAK TERSEDIA</p>
            <h1 class="mt-4 text-3xl font-bold">{{ $unavailable ? 'Konten belum dapat dimuat' : 'Video tidak ditemukan' }}</h1>
            <p class="mt-4 text-slate-600">{{ $unavailable ? 'Layanan konten sementara tidak tersedia. Silakan coba lagi nanti.' : 'Konten mungkin sudah dinonaktifkan atau belum tersedia.' }}</p>
        </x-clay-card>
    @endif
</x-public-layout>
