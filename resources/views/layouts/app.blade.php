<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} &middot; Studio</title>@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased" x-data="{ menu: false }">
    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:bg-white focus:p-4">Lewati ke
        konten</a>
    <div class="mx-auto min-h-screen max-w-[1600px] p-3 sm:p-6 lg:flex lg:items-start lg:gap-6">
        <aside
            class="clay-sidebar lg:sticky lg:top-6 lg:max-h-[calc(100dvh-3rem)] lg:w-64 lg:shrink-0 lg:overflow-y-auto">
            <div class="flex items-center justify-between"><a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 font-extrabold tracking-tight"><span class="brand-icon"
                        aria-hidden="true">&#9654;</span><span>{{ config('app.name') }}<small
                            class="block text-[10px] font-bold tracking-[.2em] text-slate-400">ADMIN
                            STUDIO</small></span></a><button type="button" class="clay-button clay-secondary lg:hidden"
                    @click="menu = !menu" :aria-expanded="menu" aria-controls="sidebar">Menu</button></div>
            <nav id="sidebar" class="mt-8 space-y-2 lg:!block" :class="menu ? 'block' : 'hidden'"
                aria-label="Navigasi dashboard">
                @foreach (config('studio.navigation') as $item)
                    <a href="{{ route($item['route']) }}"
                        class="sidebar-link {{ request()->routeIs($item['match']) ? 'active' : '' }}"
                        @if (request()->routeIs($item['match'])) aria-current="page" @endif><svg aria-hidden="true"
                            class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                        </svg>{{ $item['label'] }}</a>
                @endforeach
                <div class="pt-6">
                    <p class="px-4 text-xs text-slate-500 break-all">{{ auth()->user()->email }}</p><a
                        href="{{ route('profile.edit') }}" class="sidebar-link">Profil admin</a>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button class="sidebar-link w-full"
                            type="submit">Keluar &#8599;</button></form>
                </div>
            </nav>
        </aside>
        <div class="min-w-0 flex-1">
            <header class="clay-topbar my-5 flex flex-wrap items-center justify-between gap-3 lg:mt-0">
                <p class="text-sm text-slate-500">Workspace <span class="mx-2">/</span> <span
                        class="font-semibold text-slate-700">Studio</span></p><span
                    class="badge bg-white text-slate-600">{{ now()->format('d M Y') }} &middot; WIB</span>
            </header>
            <main id="main" class="px-1 pb-10 pt-3 sm:px-2">
                <div class="mb-8">
                    <p class="eyebrow">{{ config('studio.caption') }}</p>
                    <h1 class="mt-3 text-3xl font-extrabold tracking-tight sm:text-4xl">{{ $header ?? 'Dashboard' }}
                    </h1>
                </div>
                @if (session('status'))
                    <div role="status"
                        class="clay-notice mb-6 bg-clay-mint text-emerald-950">
                        {{ session('status') }}</div>
                @endif
                @if ($errors->any())
                    <div role="alert" class="clay-notice mb-6 bg-[#fac3be] text-rose-950">
                        <p class="font-bold">Periksa kembali isian Anda.</p>
                        <ul class="mt-2 list-inside list-disc">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                {{ $slot }}
            </main>
        </div>
    </div>
</body>

</html>
