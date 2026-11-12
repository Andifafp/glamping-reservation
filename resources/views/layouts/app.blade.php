{{--
    Layout utama halaman pengunjung.
    Pemakaian di halaman:
        @extends('layouts.app')
        @section('title', 'Judul Halaman')
        @section('content') ... @endsection
        @push('scripts') <script>...</script> @endpush   (opsional)
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Glamping tepi laut') | {{ config('app.name', 'Bet Glamping') }}</title>
    <meta name="description" content="@yield('description', 'Bet Glamping: menginap di tenda dan kabin tepi laut, reservasi online dengan asuransi pembatalan.')">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700|young-serif:400" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col">
    <a href="#konten" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:rounded-full focus:bg-white focus:px-4 focus:py-2 focus:text-sm focus:font-semibold">
        Langsung ke konten
    </a>

    @include('partials.navbar')

    <main id="konten" class="flex-1">
        @include('partials.flash')
        @yield('content')
    </main>

    @include('partials.footer')

    @stack('scripts')
</body>
</html>
