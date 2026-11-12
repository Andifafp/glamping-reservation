{{-- Penanda langkah reservasi. Parameter: $current (1, 2, atau 3) --}}
<ol class="flex flex-wrap items-center gap-x-3 gap-y-2 text-sm" aria-label="Langkah reservasi">
    @foreach (['Isi data', 'Bayar', 'Selesai'] as $i => $label)
        @php $step = $i + 1; @endphp
        <li class="flex items-center gap-2 {{ $step <= $current ? 'text-laut' : 'text-palung/45' }}" @if ($step === $current) aria-current="step" @endif>
            <span class="flex size-7 items-center justify-center rounded-full text-xs font-semibold {{ $step < $current ? 'bg-laut text-white' : ($step === $current ? 'border-2 border-laut' : 'border border-palung/25') }}">{{ $step }}</span>
            <span class="font-medium">{{ $label }}</span>
            @if ($step < 3)
                <span class="ml-1 h-px w-6 bg-palung/20 sm:w-10" aria-hidden="true"></span>
            @endif
        </li>
    @endforeach
</ol>
