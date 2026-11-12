{{-- Kartu jenis kamar. Parameter: $room (App\Models\RoomType dengan relasi category) --}}
<article class="group flex flex-col overflow-hidden rounded-2xl border border-palung/10 bg-white">
    <a href="{{ route('rooms.show', $room) }}" class="block" tabindex="-1" aria-hidden="true">
        @include('partials.room-visual', ['room' => $room, 'class' => 'aspect-[4/3] transition-transform duration-500 group-hover:scale-[1.02]'])
    </a>

    <div class="flex flex-1 flex-col p-5">
        <p class="text-sm text-ombak">{{ $room->category->name }}</p>
        <h3 class="mt-1 text-xl text-palung">
            <a href="{{ route('rooms.show', $room) }}" class="hover:text-laut">{{ $room->name }}</a>
        </h3>
        <p class="mt-2 text-sm text-palung/65">Untuk {{ $room->capacity }} orang per unit</p>

        <div class="mt-auto flex items-end justify-between gap-3 pt-5">
            <p>
                <span class="text-lg font-bold text-palung">Rp{{ number_format($room->price_per_night, 0, ',', '.') }}</span>
                <span class="text-sm text-palung/60">/malam</span>
            </p>
            <a href="{{ route('booking.create', ['kamar' => $room->slug]) }}" class="btn btn-primary px-4 py-2">Pesan</a>
        </div>
    </div>
</article>
