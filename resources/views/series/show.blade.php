<x-app-layout>
    <x-slot name="header">{{ $series->title }}</x-slot>
    <x-clay-card class="mb-8">
        <div class="flex flex-wrap justify-between gap-4">
            <div>
                <p class="eyebrow">SERIES / {{ $series->is_active ? 'AKTIF' : 'NONAKTIF' }}</p>
                <p class="mt-3 text-slate-600 whitespace-pre-line">{{ $series->description }}</p>
                <p class="mt-3 text-xs text-slate-500 break-all">{{ $series->uuid }}</p>
            </div>
            <div class="flex items-start gap-3"><x-clay-button :href="route('series.edit', $series)"
                    variant="secondary">Edit</x-clay-button><x-clay-delete :action="route('series.destroy', $series)"
                    message="Series dan semua videonya akan dihapus. Tautan lama akan menampilkan konten tidak tersedia." />
            </div>
        </div>
    </x-clay-card>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Video dalam series</h2><x-clay-button :href="route('series.videos.create', $series)">+ Video</x-clay-button>
    </div>
    <div class="space-y-4">
        @forelse($videos as $video)
            <x-clay-card class="flex flex-wrap items-center justify-between gap-4">
                <div><x-clay-badge :active="$video->is_active">{{ $video->is_active ? 'Aktif' : 'Nonaktif' }}</x-clay-badge>
                    <h3 class="mt-2 text-lg font-bold">{{ $video->title }}</h3>
                </div>
                <div class="flex items-center gap-4"><a href="{{ route('watch', $video->uuid) }}"
                        class="text-cyan-800 underline">Lihat</a><a
                        href="{{ route('series.videos.edit', [$series, $video]) }}"
                        class="font-semibold">Edit</a><x-clay-delete :action="route('series.videos.destroy', [$series, $video])" /></div>
        </x-clay-card>@empty<x-clay-card>
                <p class="text-slate-600">Belum ada video. Tambahkan minimal 3 video aktif agar series bisa diposting.
                </p>
            </x-clay-card>
        @endforelse
    </div>
    <div class="mt-6">{{ $videos->links() }}</div>
</x-app-layout>
