{{-- Ilustrasi hero: fajar di laut, tiga jenis kamar di tebing. Bisa diganti foto asli nanti. --}}
<svg class="absolute inset-0 h-full w-full" viewBox="0 0 1440 720" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
    <defs>
        <linearGradient id="langit" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0" stop-color="#0B2A3C"/>
            <stop offset="0.55" stop-color="#145C7A"/>
            <stop offset="1" stop-color="#5FB3C4"/>
        </linearGradient>
        <linearGradient id="laut" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0" stop-color="#2A8FA6"/>
            <stop offset="1" stop-color="#0B2A3C"/>
        </linearGradient>
    </defs>

    <rect width="1440" height="460" fill="url(#langit)"/>

    {{-- Bintang yang memudar --}}
    <g fill="#F7FBFC" opacity="0.6">
        <circle cx="180" cy="80" r="1.6"/><circle cx="320" cy="140" r="1.2"/><circle cx="520" cy="60" r="1.4"/>
        <circle cx="760" cy="110" r="1.1"/><circle cx="980" cy="70" r="1.5"/><circle cx="1240" cy="120" r="1.2"/>
        <circle cx="1360" cy="50" r="1.4"/><circle cx="640" cy="180" r="1"/>
    </g>

    {{-- Matahari terbit --}}
    <circle cx="760" cy="460" r="92" fill="#F2B33D"/>
    <circle cx="760" cy="460" r="140" fill="#F2B33D" opacity="0.15"/>

    {{-- Laut --}}
    <rect y="458" width="1440" height="262" fill="url(#laut)"/>
    <g stroke="#F7FBFC" stroke-linecap="round" fill="none">
        <path d="M660 492 H860" stroke-width="3" opacity="0.55"/>
        <path d="M690 520 H830" stroke-width="2.5" opacity="0.4"/>
        <path d="M720 548 H800" stroke-width="2" opacity="0.3"/>
        <path d="M80 600 q 30 -10 60 0 t 60 0" stroke-width="2" opacity="0.25"/>
        <path d="M640 640 q 30 -10 60 0 t 60 0" stroke-width="2" opacity="0.2"/>
    </g>

    {{-- Tebing --}}
    <path d="M820 720 L860 470 Q900 420 980 418 L1440 400 V720 Z" fill="#0B2A3C"/>
    <path d="M860 470 Q900 420 980 418 L1440 400 V412 L985 432 Q910 434 872 478 Z" fill="#145C7A" opacity="0.6"/>

    {{-- Tenda safari --}}
    <g transform="translate(940 340)">
        <path d="M0 80 L46 18 L92 80 Z" fill="#F7FBFC"/>
        <path d="M46 18 L46 80" stroke="#0B2A3C" stroke-width="2"/>
        <path d="M36 80 L46 52 L56 80 Z" fill="#F2B33D"/>
    </g>

    {{-- Kabin kayu --}}
    <g transform="translate(1080 316)">
        <path d="M0 40 L50 6 L100 40 Z" fill="#E8F3F5"/>
        <rect x="10" y="40" width="80" height="54" fill="#E8F3F5"/>
        <rect x="40" y="62" width="20" height="32" fill="#0B2A3C"/>
        <rect x="18" y="52" width="14" height="12" fill="#F2B33D"/>
        <rect x="68" y="52" width="14" height="12" fill="#F2B33D"/>
    </g>

    {{-- Dome --}}
    <g transform="translate(1230 330)">
        <path d="M0 76 A 52 52 0 0 1 104 76 Z" fill="#F7FBFC"/>
        <path d="M18 76 A 34 46 0 0 1 86 76" stroke="#2A8FA6" stroke-width="2" fill="none"/>
        <path d="M52 24 V76" stroke="#2A8FA6" stroke-width="2"/>
        <circle cx="52" cy="58" r="8" fill="#F2B33D"/>
    </g>
</svg>
