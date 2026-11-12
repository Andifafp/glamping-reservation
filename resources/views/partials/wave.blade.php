{{-- Garis ombak pembatas bagian. Parameter: $fill (warna), $flip (true = terbalik) --}}
<svg class="block h-8 w-full sm:h-12 {{ !empty($flip) ? 'rotate-180' : '' }}" viewBox="0 0 1440 48" preserveAspectRatio="none" aria-hidden="true">
    <path d="M0 30 C 120 10, 240 10, 360 26 S 600 46, 720 30 S 960 6, 1080 22 S 1320 44, 1440 24 V48 H0 Z" fill="{{ $fill ?? '#0B2A3C' }}"/>
</svg>
