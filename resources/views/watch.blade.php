<x-public-layout>
    @if ($video)
        <div class="public-content">
            <div class="stream-player">
                <iframe src="{{ $video['player_url'] }}" title="Pemutar video"
                    allow="accelerometer; gyroscope; autoplay; encrypted-media; picture-in-picture"
                    referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
            </div>
            <x-ad-slot :url="$video['ad_url'] ?? null" />
        </div>
    @else
        <div class="stream-notice" role="status">
            <h1>{{ $unavailable ? 'Konten belum dapat dimuat' : 'Video tidak ditemukan' }}</h1>
            <p>{{ $unavailable ? 'Layanan konten sementara tidak tersedia. Silakan coba lagi nanti.' : 'Konten mungkin sudah dinonaktifkan atau belum tersedia.' }}</p>
        </div>
    @endif
</x-public-layout>
