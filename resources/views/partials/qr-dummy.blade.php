{{--
    QR DUMMY: pola kotak dari kode booking, BUKAN QR asli yang bisa dipindai.
    Tugas hanya meminta QR dummy. Parameter: $code
--}}
@php
    $n = 25;
    $hash = '';
    for ($i = 0; strlen($hash) < $n * $n; $i++) {
        foreach (str_split(md5($code . $i)) as $hex) {
            $hash .= sprintf('%04b', hexdec($hex));
        }
    }
    $isFinder = function ($x, $y) use ($n) {
        foreach ([[0, 0], [$n - 7, 0], [0, $n - 7]] as [$fx, $fy]) {
            if ($x >= $fx && $x < $fx + 7 && $y >= $fy && $y < $fy + 7) {
                return true;
            }
        }
        return false;
    };
@endphp

<svg viewBox="-2 -2 {{ $n + 4 }} {{ $n + 4 }}" class="h-full w-full" role="img" aria-label="QR dummy untuk booking {{ $code }}" shape-rendering="crispEdges">
    <rect x="-2" y="-2" width="{{ $n + 4 }}" height="{{ $n + 4 }}" fill="#fff"/>
    @for ($y = 0; $y < $n; $y++)
        @for ($x = 0; $x < $n; $x++)
            @if (! $isFinder($x, $y) && $hash[$y * $n + $x] === '1')
                <rect x="{{ $x }}" y="{{ $y }}" width="1" height="1" fill="#0B2A3C"/>
            @endif
        @endfor
    @endfor
    @foreach ([[0, 0], [$n - 7, 0], [0, $n - 7]] as [$fx, $fy])
        <rect x="{{ $fx }}" y="{{ $fy }}" width="7" height="7" fill="#0B2A3C"/>
        <rect x="{{ $fx + 1 }}" y="{{ $fy + 1 }}" width="5" height="5" fill="#fff"/>
        <rect x="{{ $fx + 2 }}" y="{{ $fy + 2 }}" width="3" height="3" fill="#0B2A3C"/>
    @endforeach
</svg>
