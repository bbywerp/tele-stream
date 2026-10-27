<x-app-layout><x-slot name="header">{{ $series->exists ? 'Edit series' : 'Series baru' }}</x-slot>
    <x-clay-card class="max-w-3xl">
        <form method="POST" action="{{ $series->exists ? route('series.update', $series) : route('series.store') }}"
            class="space-y-6">@csrf @if ($series->exists)
                @method('PUT')
            @endif
            <x-clay-field name="title" label="Judul series" :value="$series->title" required maxlength="255" />
            <div><label for="description" class="field-label">Deskripsi</label>
                <textarea id="description" name="description" rows="5" maxlength="10000" class="clay-input">{{ old('description', $series->description) }}</textarea>
            </div>
            <x-clay-field name="cover_image" label="URL cover image (opsional)" type="url" :value="$series->cover_image"
                placeholder="https://…" maxlength="2048" />
            <input type="hidden" name="is_active" value="0"><label class="flex items-center gap-3"><input
                    type="checkbox" name="is_active" value="1" @checked(old('is_active', $series->is_active))
                    class="rounded text-pink-600">Series aktif</label>
            <div class="flex gap-3"><x-clay-button>Simpan series</x-clay-button><x-clay-button :href="route('series.index')"
                    variant="secondary">Kembali</x-clay-button></div>
        </form>
    </x-clay-card>
</x-app-layout>
