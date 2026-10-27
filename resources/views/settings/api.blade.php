<x-app-layout><x-slot name="header">Akses API</x-slot><x-clay-card class="max-w-3xl">
        <p class="eyebrow">INTEGRASI ANTARAPLIKASI</p>
        <h2 class="mt-3 text-2xl font-bold">Satu token, dua aplikasi</h2>
        <p class="mt-3 mb-6 text-slate-600">Buat token acak minimal 32 karakter, lalu masukkan token yang sama pada
            pengaturan API tele-autoposts. Mengganti token langsung menonaktifkan token lama.</p>
        <form method="POST" action="{{ route('settings.api.update') }}" class="space-y-6">@csrf
            @method('PUT')<x-clay-field name="api_token" label="API token baru" type="password"
                autocomplete="new-password" minlength="32" maxlength="200" :placeholder="$configured ? '•••••••• — tersimpan, kosongkan untuk mempertahankan' : 'Belum dikonfigurasi'" />
            <p class="text-sm text-slate-500">Gunakan huruf, angka, tanda minus atau underscore. Token tersimpan
                terenkripsi dan tidak ditampilkan kembali.</p><x-clay-button>Simpan token</x-clay-button>
        </form>
    </x-clay-card></x-app-layout>
