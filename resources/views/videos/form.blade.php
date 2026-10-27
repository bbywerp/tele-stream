<x-app-layout><x-slot name="header">{{ $video->exists ? 'Edit video' : 'Video baru' }}</x-slot>
    <p class="mb-6 text-slate-600">Series: {{ $series->title }}</p><x-clay-card class="max-w-3xl">
        <form method="POST"
            action="{{ $video->exists ? route('series.videos.update', [$series, $video]) : route('series.videos.store', $series) }}"
            class="space-y-6">@csrf @if ($video->exists)
                @method('PUT')
            @endif
            <x-clay-field name="title" label="Judul video" :value="$video->title" maxlength="255" required />
            <div><label for="description" class="field-label">Deskripsi</label>
                <textarea id="description" name="description" rows="4" class="clay-input" maxlength="10000">{{ old('description', $video->description) }}</textarea>
            </div>
            <x-clay-field name="thumbnail_url" label="URL thumbnail (opsional)" type="url" :value="$video->thumbnail_url"
                maxlength="2048" />
            <x-clay-field name="stream_url" label="URL stream eksternal (MP4 / HLS)" type="url" :value="$video->stream_url"
                required maxlength="8192" />
            <input type="hidden" name="is_active" value="0"><label class="flex items-center gap-3"><input
                    type="checkbox" name="is_active" value="1" @checked(old('is_active', $video->is_active))
                    class="rounded text-pink-600">Video aktif</label>
            <div class="flex gap-3"><x-clay-button>Simpan video</x-clay-button><x-clay-button :href="route('series.show', $series)"
                    variant="secondary">Kembali</x-clay-button></div>
        </form>
    </x-clay-card>
</x-app-layout>
