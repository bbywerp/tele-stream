<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <title>{{ config('app.name') }}</title>@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased">
    <nav class="clay-topbar mx-auto mt-5 flex w-[calc(100%-2rem)] max-w-6xl flex-wrap items-center justify-between gap-4" aria-label="Navigasi publik"><a
            href="/" class="flex items-center gap-3 font-extrabold tracking-tight"><span class="brand-icon"
                aria-hidden="true">▶</span>{{ config('app.name') }}</a><span
            class="badge bg-white text-slate-600">Konten pilihan</span></nav>
    <main class="mx-auto max-w-5xl px-5 pb-16 pt-8">{{ $slot }}</main>
    <footer class="mx-auto max-w-6xl px-5 pb-8 text-sm text-slate-500">© {{ date('Y') }} {{ config('app.name') }}
    </footer>
</body>

</html>
