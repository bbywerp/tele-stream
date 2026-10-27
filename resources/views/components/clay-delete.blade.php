@props(['action', 'label' => 'Hapus', 'message' => 'Data akan dihapus. Lanjutkan?'])
<div x-data="{ open: false }">
    <button type="button" class="text-sm font-semibold text-rose-700 underline underline-offset-4"
        @click="open = true">{{ $label }}</button>
    <div x-show="open" x-cloak @keydown.escape.window="open = false"
        class="fixed inset-0 z-50 grid place-items-center bg-slate-900/40 p-4" @click.self="open = false">
        <section role="dialog" aria-modal="true" aria-label="Konfirmasi penghapusan" class="clay-card max-w-md"
            x-trap.inert.noscroll="open">
            <h2 class="text-xl font-bold">Konfirmasi penghapusan</h2>
            <p class="my-5 text-slate-600">{{ $message }}</p>
            <div class="flex flex-wrap gap-3"><button type="button" class="clay-button clay-secondary"
                    @click="open = false">Batal</button>
                <form method="POST" action="{{ $action }}">@csrf
                    @method('DELETE')<x-clay-button>{{ $label }}</x-clay-button></form>
            </div>
        </section>
    </div>
</div>
