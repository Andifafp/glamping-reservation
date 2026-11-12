{{--
    Gambar kamar. Kalau $room->image kosong, tampil ilustrasi sesuai kategori.
    Parameter: $room, $class (opsional, ukuran/rasio)
--}}
@php
    $slug = $room->category->slug ?? '';
@endphp

<div class="relative overflow-hidden bg-laut {{ $class ?? 'aspect-[4/3]' }}">
    @if ($room->image)
        <img src="{{ asset('storage/' . $room->image) }}" alt="Foto {{ $room->name }}" class="h-full w-full object-cover" loading="lazy">
    @else
        <svg class="h-full w-full" viewBox="0 0 400 300" preserveAspectRatio="xMidYMid slice" role="img" aria-label="Ilustrasi {{ $room->name }}">
            <rect width="400" height="300" fill="#145C7A"/>
            <circle cx="320" cy="90" r="34" fill="#F2B33D" opacity="0.9"/>
            <rect y="200" width="400" height="100" fill="#0F4A63"/>
            <path d="M0 206 q 25 -8 50 0 t 50 0 t 50 0 t 50 0 t 50 0 t 50 0 t 50 0 t 50 0" stroke="#5FB3C4" stroke-width="2" fill="none" opacity="0.6"/>
            @if (str_contains($slug, 'kabin'))
                <path d="M120 140 L200 90 L280 140 Z" fill="#E8F3F5"/>
                <rect x="135" y="140" width="130" height="66" fill="#E8F3F5"/>
                <rect x="185" y="164" width="30" height="42" fill="#0B2A3C"/>
                <rect x="148" y="154" width="24" height="18" fill="#F2B33D"/>
                <rect x="228" y="154" width="24" height="18" fill="#F2B33D"/>
            @elseif (str_contains($slug, 'dome'))
                <path d="M120 206 A 80 80 0 0 1 280 206 Z" fill="#F7FBFC"/>
                <path d="M146 206 A 54 70 0 0 1 254 206" stroke="#2A8FA6" stroke-width="3" fill="none"/>
                <path d="M200 126 V206" stroke="#2A8FA6" stroke-width="3"/>
                <circle cx="200" cy="178" r="12" fill="#F2B33D"/>
            @else
                <path d="M120 206 L200 100 L280 206 Z" fill="#F7FBFC"/>
                <path d="M200 100 V206" stroke="#0B2A3C" stroke-width="3"/>
                <path d="M182 206 L200 160 L218 206 Z" fill="#F2B33D"/>
            @endif
        </svg>
    @endif
</div>
