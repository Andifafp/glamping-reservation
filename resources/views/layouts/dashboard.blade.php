{{--
    Kerangka dashboard staff/admin - Marcel
    Pemakaian di halaman dashboard:
        @extends('layouts.dashboard')
        @section('title', 'Jenis kamar')
        @section('actions') <a href="..." class="btn btn-primary">Tambah</a> @endsection   (opsional)
        @section('content') ... @endsection

    Menu sidebar hanya tampil jika rutenya sudah ada (Route::has),
    jadi anggota lain cukup membuat rute dengan nama di bawah.
--}}
@php
    $menu = [
        ['label' => 'Beranda', 'route' => 'dashboard.index', 'active' => 'dashboard.index', 'admin' => false],
        ['label' => 'Pembatalan', 'route' => 'dashboard.cancellations.index', 'active' => 'dashboard.cancellations.*', 'admin' => false],
        ['label' => 'Kategori', 'route' => 'dashboard.categories.index', 'active' => 'dashboard.categories.*', 'admin' => false],
        ['label' => 'Jenis kamar', 'route' => 'dashboard.room-types.index', 'active' => 'dashboard.room-types.*', 'admin' => false],
        ['label' => 'Artikel', 'route' => 'dashboard.articles.index', 'active' => 'dashboard.articles.*', 'admin' => false],
        ['label' => 'Manajemen akun', 'route' => 'dashboard.users.index', 'active' => 'dashboard.users.*', 'admin' => true],
        ['label' => 'Log aktivitas', 'route' => 'dashboard.activity-logs.index', 'active' => 'dashboard.activity-logs.*', 'admin' => true],
    ];
    $me = auth()->user();
    $menu = array_values(array_filter($menu, fn ($item) => ! $item['admin'] || $me->isAdmin()));
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex">
    <title>@yield('title', 'Dashboard') | Dashboard {{ config('app.name', 'Bet Glamping') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700|young-serif:400" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-garam lg:grid lg:grid-cols-[16rem_1fr]">
    <a href="#konten" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:rounded-full focus:bg-white focus:px-4 focus:py-2 focus:text-sm focus:font-semibold">
        Langsung ke konten
    </a>

    {{-- Sidebar desktop --}}
    <aside class="hidden bg-palung text-white lg:sticky lg:top-0 lg:flex lg:h-screen lg:flex-col">
        <a href="{{ route('dashboard.index') }}" class="flex items-center gap-2.5 px-6 py-5">
            @include('partials.logo')
            <span class="font-display text-lg">Bet Glamping</span>
        </a>

        <nav class="flex-1 overflow-y-auto px-3 py-2" aria-label="Menu dashboard">
            <ul class="space-y-1">
                @foreach ($menu as $item)
                    @if ($item['admin'] && empty($adminLabelShown))
                        @php $adminLabelShown = true; @endphp
                        <li class="px-3 pt-5 pb-1 text-xs font-semibold text-fajar">Khusus admin</li>
                    @endif
                    <li>
                        @if (\Illuminate\Support\Facades\Route::has($item['route']))
                            <a href="{{ route($item['route']) }}"
                               class="block rounded-lg px-3 py-2.5 text-sm font-medium {{ request()->routeIs($item['active']) ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/5 hover:text-white' }}"
                               @if (request()->routeIs($item['active'])) aria-current="page" @endif>
                                {{ $item['label'] }}
                            </a>
                        @else
                            <span class="flex items-center justify-between rounded-lg px-3 py-2.5 text-sm text-white/35" title="Rute {{ $item['route'] }} belum dibuat">
                                {{ $item['label'] }}
                                <span class="text-xs">belum ada</span>
                            </span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="border-t border-white/10 p-4">
            <p class="truncate text-sm font-semibold">{{ $me->name }}</p>
            <p class="text-xs text-white/55">{{ $me->isAdmin() ? 'Admin' : 'Staff' }}</p>
            <div class="mt-3 flex items-center gap-3 text-sm">
                <a href="{{ route('home') }}" class="text-white/70 hover:text-white">Lihat situs</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-white/70 hover:text-white">Keluar</button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Bar atas HP/tablet --}}
    <header class="sticky top-0 z-40 flex items-center justify-between bg-palung px-4 py-3 text-white lg:hidden">
        <a href="{{ route('dashboard.index') }}" class="flex items-center gap-2">
            @include('partials.logo', ['size' => 30])
            <span class="font-display">Dashboard</span>
        </a>
        <details class="group relative">
            <summary class="flex cursor-pointer list-none items-center rounded-full border border-white/25 px-4 py-2 text-sm font-medium [&::-webkit-details-marker]:hidden">
                <span class="group-open:hidden">Menu</span>
                <span class="hidden group-open:inline">Tutup</span>
            </summary>
            <div class="absolute right-0 mt-2 w-64 rounded-2xl bg-white p-2 text-palung shadow-lg">
                <ul>
                    @foreach ($menu as $item)
                        <li>
                            @if (\Illuminate\Support\Facades\Route::has($item['route']))
                                <a href="{{ route($item['route']) }}"
                                   class="block rounded-xl px-4 py-3 text-sm font-medium {{ request()->routeIs($item['active']) ? 'bg-buih text-laut' : '' }}">
                                    {{ $item['label'] }}
                                </a>
                            @else
                                <span class="block px-4 py-3 text-sm text-palung/40">{{ $item['label'] }} (belum ada)</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
                <div class="mt-2 border-t border-palung/10 pt-2">
                    <a href="{{ route('home') }}" class="block rounded-xl px-4 py-3 text-sm font-medium">Lihat situs</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full rounded-xl px-4 py-3 text-left text-sm font-medium text-palung/70">Keluar</button>
                    </form>
                </div>
            </div>
        </details>
    </header>

    <main id="konten" class="min-w-0 px-4 py-8 sm:px-8 lg:py-10">
        <div class="mx-auto max-w-6xl">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h1 class="text-3xl sm:text-4xl">@yield('title', 'Dashboard')</h1>
                <div class="flex flex-wrap gap-2">@yield('actions')</div>
            </div>

            @if (session('status') || session('info'))
                <div role="status" class="mt-6 rounded-xl border px-4 py-3 text-sm {{ session('status') ? 'border-ombak/30 bg-buih text-laut-tua' : 'border-fajar/50 bg-fajar/15 text-fajar-tua' }}">
                    {{ session('status') ?? session('info') }}
                </div>
            @endif

            <div class="mt-8">
                @yield('content')
            </div>
        </div>
    </main>

    @stack('scripts')
</body>
</html>
