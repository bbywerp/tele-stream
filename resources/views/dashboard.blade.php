<x-app-layout>
    <x-slot name="header">Ruang konten Anda</x-slot>
    <p class="mb-8 text-slate-600">Kelola koleksi hari ini, bagikan cerita berikutnya.</p>
    <div class="grid gap-6 sm:grid-cols-3">
        @foreach ([['Total series', $seriesCount], ['Total video', $videoCount], ['Siap untuk diposting', $eligibleCount]] as [$label, $value])
            <x-clay-card class="clay-stat">
                <p class="text-sm font-semibold text-slate-700">{{ $label }}</p>
                <p class="stat-value">{{ $value }}</p>
            </x-clay-card>
        @endforeach
    </div>
    <x-clay-card class="mt-8">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-xl font-bold">Series terbaru</h2><x-clay-button :href="route('series.create')">+ Series</x-clay-button>
        </div>
        <div class="mt-6 divide-y divide-slate-100">
            @forelse($recent as $item)
                <a href="{{ route('series.show', $item) }}"
                    class="flex items-center justify-between gap-4 py-5 font-semibold"><span>{{ $item->title }}</span><span
                    class="text-sm text-slate-500">{{ $item->videos_count }} video &#8594;</span></a>@empty<p
                    class="py-8 text-slate-500">Belum ada series. Mulai dengan menambahkan koleksi pertama Anda.</p>
            @endforelse
        </div>
    </x-clay-card>
</x-app-layout>
