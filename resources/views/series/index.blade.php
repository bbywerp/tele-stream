<x-app-layout>
    <x-slot name="header">Library series</x-slot>
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <p class="text-slate-600">Kelola cerita dan koleksi video Anda.</p><x-clay-button :href="route('series.create')">+ Tambah
            series</x-clay-button>
    </div>
    <form method="GET" class="flex gap-3 mb-6"><label for="search" class="sr-only">Cari judul series</label><input
            id="search" name="search" value="{{ $search }}" placeholder="Cari judul series…"
            class="clay-input max-w-md"><x-clay-button variant="secondary">Cari</x-clay-button></form>
    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        @forelse($series as $item)
            <x-clay-card class="flex flex-col gap-4">
                @if ($item->cover_image)
                    <img src="{{ $item->cover_image }}" alt="Cover {{ $item->title }}"
                        class="aspect-video w-full rounded-2xl object-cover" loading="lazy"
                    referrerpolicy="no-referrer">@else<div
                        class="clay-inset grid aspect-video place-items-center bg-clay-blue text-4xl font-black text-slate-600"
                        aria-hidden="true">▶</div>
                @endif
                <div class="flex justify-between gap-3"><x-clay-badge
                        :active="$item->is_active">{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</x-clay-badge><span
                        class="text-sm text-slate-500">{{ $item->videos_count }} video</span></div>
                <h2 class="text-xl font-bold"><a href="{{ route('series.show', $item) }}">{{ $item->title }}</a></h2>
                <p class="text-sm text-slate-600 line-clamp-2">{{ $item->description ?: 'Belum ada deskripsi.' }}</p>
                <a href="{{ route('series.show', $item) }}" class="mt-auto font-semibold text-pink-700">Kelola series
                    →</a>
            </x-clay-card>
        @empty<x-clay-card class="md:col-span-2">
                <h2 class="text-xl font-bold">Library masih kosong</h2>
                <p class="mt-2 text-slate-600">Tambahkan series pertama atau ubah pencarian Anda.</p>
            </x-clay-card>
        @endforelse
    </div>
    <div class="mt-8">{{ $series->links() }}</div>
</x-app-layout>
