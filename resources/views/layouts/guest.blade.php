<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased">
    <main class="grid min-h-screen place-items-center overflow-hidden px-6 py-12">
        <div class="clay-auth w-full max-w-md"><a href="/"
                class="mb-8 flex items-center justify-center gap-3 text-xl font-extrabold"><span class="brand-icon"
                    aria-hidden="true">&#9654;</span>{{ config('app.name') }}</a>
            <div class="clay-card">{{ $slot }}</div>
            <p class="mt-8 text-center text-xs text-slate-500">Studio admin &middot; Akses terbatas</p>
        </div>
    </main>
</body>

</html>
