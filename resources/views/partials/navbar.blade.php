{{-- Navbar publik. Menu HP memakai <details>, jadi tidak butuh JavaScript. --}}
@php
    $menu = [
        ['label' => 'Beranda', 'route' => 'home', 'active' => 'home'],
        ['label' => 'Kamar', 'route' => 'rooms.index', 'active' => 'rooms.*'],
        ['label' => 'Artikel', 'route' => 'articles.index', 'active' => 'articles.*'],
        ['label' => 'Cek booking', 'route' => 'booking.check', 'active' => 'booking.check*'],
    ];
@endphp

<header class="sticky top-0 z-40 print:hidden border-b border-palung/10 bg-garam/95 backdrop-blur">
    <nav class="mx-auto flex max-w-6xl items-center justify-between gap-6 px-4 py-3 sm:px-6" aria-label="Menu utama">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5">
            @include('partials.logo')
            <span class="font-display text-xl text-palung">Bet Glamping</span>
        </a>

        {{-- Menu desktop --}}
        <ul class="hidden items-center gap-1 md:flex">
            @foreach ($menu as $item)
                <li>
                    <a href="{{ route($item['route']) }}"
                       class="rounded-full px-4 py-2 text-sm font-medium transition-colors {{ request()->routeIs($item['active']) ? 'bg-buih text-laut' : 'text-palung/75 hover:text-laut' }}"
                       @if (request()->routeIs($item['active'])) aria-current="page" @endif>
                        {{ $item['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="hidden items-center gap-2 md:flex">
            @auth
                @if (auth()->user()->canAccessDashboard())
                    <a href="{{ route('dashboard.index') }}" class="btn btn-secondary px-4 py-2">Dashboard</a>
                @endif
                <a href="{{ route('profile.index') }}" class="flex items-center gap-2 rounded-full px-3 py-2 text-sm font-medium text-palung hover:bg-buih">
                    <span class="flex size-7 items-center justify-center rounded-full bg-laut text-xs font-semibold text-white" aria-hidden="true">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                    {{ \Illuminate\Support\Str::words(auth()->user()->name, 1, '') }}
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-full px-3 py-2 text-sm font-medium text-palung/70 hover:text-laut">Keluar</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="rounded-full px-4 py-2 text-sm font-medium text-palung hover:text-laut">Masuk</a>
                <a href="{{ route('booking.create') }}" class="btn btn-primary">Pesan sekarang</a>
            @endauth
        </div>

        {{-- Menu HP --}}
        <details class="group relative md:hidden">
            <summary class="flex cursor-pointer list-none items-center gap-2 rounded-full border border-palung/15 px-4 py-2 text-sm font-medium [&::-webkit-details-marker]:hidden">
                <span class="group-open:hidden">Menu</span>
                <span class="hidden group-open:inline">Tutup</span>
            </summary>
            <div class="absolute right-0 mt-2 w-64 rounded-2xl border border-palung/10 bg-white p-2 shadow-lg">
                <ul>
                    @foreach ($menu as $item)
                        <li>
                            <a href="{{ route($item['route']) }}"
                               class="block rounded-xl px-4 py-3 text-sm font-medium {{ request()->routeIs($item['active']) ? 'bg-buih text-laut' : 'text-palung' }}">
                                {{ $item['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-2 border-t border-palung/10 pt-2">
                    @auth
                        @if (auth()->user()->canAccessDashboard())
                            <a href="{{ route('dashboard.index') }}" class="block rounded-xl px-4 py-3 text-sm font-semibold text-laut">Dashboard</a>
                        @endif
                        <a href="{{ route('profile.index') }}" class="block rounded-xl px-4 py-3 text-sm font-medium text-palung">Profil saya</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full rounded-xl px-4 py-3 text-left text-sm font-medium text-palung/70">Keluar</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="block rounded-xl px-4 py-3 text-sm font-medium text-palung">Masuk</a>
                        <a href="{{ route('booking.create') }}" class="btn btn-primary mt-1 w-full">Pesan sekarang</a>
                    @endauth
                </div>
            </div>
        </details>
    </nav>
</header>
