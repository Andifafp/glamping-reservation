{{--
    Form reservasi - Hafiz
    Variabel:
        $rooms        : RoomType aktif dengan relasi category
        $selectedRoom : RoomType dari ?kamar=slug, atau null
        $user         : user yang login (untuk mengisi data kontak)
        $checkIn, $checkOut : dari query string, atau null
    Form dikirim ke route('booking.store') dengan field:
        room_type_id, check_in, check_out, units, guests,
        contact_name, contact_email, contact_phone, with_insurance (0/1), notes
    Hitungan di sidebar hanya perkiraan (JavaScript). Total final WAJIB dihitung ulang
    di server, termasuk cek ketersediaan dengan $roomType->availableUnits().
--}}
@extends('layouts.app')

@section('title', 'Reservasi')

@section('content')
    <section class="mx-auto max-w-6xl px-4 pt-12 sm:px-6">
        @include('partials.booking-steps', ['current' => 1])
        <h1 class="mt-6 text-4xl">Reservasi kamar</h1>

        @if ($errors->any())
            <div class="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                Ada data yang perlu diperbaiki. Cek kolom yang ditandai merah di bawah.
            </div>
        @endif

        <form method="POST" action="{{ route('booking.store') }}" id="form-reservasi"
              data-insurance-rate="{{ config('glamping.insurance_rate') }}"
              class="mt-8 grid gap-8 lg:grid-cols-[1.5fr_1fr]" novalidate>
            @csrf

            <div class="space-y-8">
                {{-- Kamar dan tanggal --}}
                <fieldset class="panel">
                    <legend class="float-left mb-6 w-full font-display text-2xl">Kamar dan tanggal</legend>
                    <div class="clear-both grid gap-5 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="room_type_id" class="form-label">Jenis kamar</label>
                            <select id="room_type_id" name="room_type_id" class="form-input" required>
                                <option value="">Pilih kamar</option>
                                @foreach ($rooms as $room)
                                    <option value="{{ $room->id }}"
                                            data-price="{{ $room->price_per_night }}"
                                            data-capacity="{{ $room->capacity }}"
                                            data-units="{{ $room->total_units }}"
                                            @selected(old('room_type_id', $selectedRoom?->id) == $room->id)>
                                        {{ $room->name }} ({{ $room->category->name }}), Rp{{ number_format($room->price_per_night, 0, ',', '.') }}/malam
                                    </option>
                                @endforeach
                            </select>
                            @error('room_type_id') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="check_in" class="form-label">Check-in</label>
                            <input type="date" id="check_in" name="check_in" value="{{ old('check_in', $checkIn) }}" min="{{ now()->toDateString() }}" class="form-input" required>
                            @error('check_in') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="check_out" class="form-label">Check-out</label>
                            <input type="date" id="check_out" name="check_out" value="{{ old('check_out', $checkOut) }}" min="{{ now()->addDay()->toDateString() }}" class="form-input" required>
                            @error('check_out') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="units" class="form-label">Jumlah unit</label>
                            <input type="number" id="units" name="units" value="{{ old('units', 1) }}" min="1" max="10" class="form-input" required>
                            @error('units') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="guests" class="form-label">Jumlah tamu</label>
                            <input type="number" id="guests" name="guests" value="{{ old('guests', 2) }}" min="1" class="form-input" aria-describedby="guests-hint" required>
                            <p id="guests-hint" class="form-hint">Maksimal sesuai kapasitas kamar dikali jumlah unit.</p>
                            @error('guests') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </fieldset>

                {{-- Data kontak --}}
                <fieldset class="panel">
                    <legend class="float-left mb-2 w-full font-display text-2xl">Data kontak</legend>
                    <p class="clear-both mb-6 text-sm text-palung/65">Diisi dari profilmu. Ubah jika yang menginap orang lain.</p>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="contact_name" class="form-label">Nama pemesan</label>
                            <input type="text" id="contact_name" name="contact_name" value="{{ old('contact_name', $user->name) }}" class="form-input" autocomplete="name" required>
                            @error('contact_name') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="contact_email" class="form-label">Email</label>
                            <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email', $user->email) }}" class="form-input" autocomplete="email" required>
                            @error('contact_email') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="contact_phone" class="form-label">Nomor HP</label>
                            <input type="tel" id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $user->phone) }}" class="form-input" autocomplete="tel" required>
                            @error('contact_phone') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="notes" class="form-label">Catatan untuk staf <span class="font-normal text-palung/50">(opsional)</span></label>
                            <textarea id="notes" name="notes" rows="3" class="form-input" placeholder="Perkiraan jam tiba, permintaan khusus">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </fieldset>

                {{-- Asuransi --}}
                <div class="rounded-2xl border-2 border-fajar/60 bg-fajar/10 p-6">
                    <label class="flex cursor-pointer items-start gap-4">
                        <input type="hidden" name="with_insurance" value="0">
                        <input type="checkbox" id="with_insurance" name="with_insurance" value="1" class="mt-1 size-5 shrink-0 accent-laut" @checked(old('with_insurance'))>
                        <span>
                            <span class="block font-semibold">Tambahkan asuransi pembatalan ({{ config('glamping.insurance_rate') }}% dari harga kamar)</span>
                            <span class="mt-1.5 block text-sm leading-6 text-palung/75">
                                Batal lebih dari {{ config('glamping.refund_threshold_days') }} hari sebelum check-in: refund {{ config('glamping.refund.with_insurance.before_threshold') }}%.
                                Kurang dari itu: {{ config('glamping.refund.with_insurance.after_threshold') }}%.
                                Tanpa asuransi refund hanya {{ config('glamping.refund.without_insurance.before_threshold') }}% atau {{ config('glamping.refund.without_insurance.after_threshold') }}%.
                            </span>
                        </span>
                    </label>
                </div>
            </div>

            {{-- Ringkasan --}}
            <aside class="lg:sticky lg:top-24 lg:self-start">
                <div class="panel">
                    <h2 class="text-2xl">Ringkasan</h2>
                    <dl class="mt-6 space-y-3 text-sm">
                        <div class="flex justify-between gap-4">
                            <dt class="text-palung/65">Kamar</dt>
                            <dd class="text-right font-medium" data-out="room">Belum dipilih</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-palung/65">Lama menginap</dt>
                            <dd class="font-medium" data-out="nights">0 malam</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-palung/65">Harga kamar</dt>
                            <dd class="font-medium" data-out="subtotal">Rp0</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-palung/65">Asuransi</dt>
                            <dd class="font-medium" data-out="insurance">Rp0</dd>
                        </div>
                        <div class="flex justify-between gap-4 border-t border-palung/10 pt-4 text-base">
                            <dt class="font-semibold">Total</dt>
                            <dd class="font-display text-2xl" data-out="total">Rp0</dd>
                        </div>
                    </dl>
                    <p class="mt-2 text-xs text-palung/55" data-out="warning" role="status"></p>
                    <button type="submit" class="btn btn-primary mt-6 w-full">Lanjut ke pembayaran</button>
                    <p class="mt-3 text-center text-xs text-palung/55">Ketersediaan unit dicek ulang saat kamu menekan tombol ini.</p>
                </div>
            </aside>
        </form>
    </section>
@endsection

@push('scripts')
<script>
    (() => {
        const form = document.getElementById('form-reservasi');
        if (!form) return;

        const rate = Number(form.dataset.insuranceRate || 0);
        const el = (id) => document.getElementById(id);
        const out = (name) => form.querySelector(`[data-out="${name}"]`);
        const rupiah = (n) => 'Rp' + Math.round(n).toLocaleString('id-ID');

        function update() {
            const option = el('room_type_id').selectedOptions[0];
            const price = Number(option?.dataset.price || 0);
            const capacity = Number(option?.dataset.capacity || 0);
            const units = Math.max(1, Number(el('units').value || 1));
            const guests = Number(el('guests').value || 0);

            const start = new Date(el('check_in').value);
            const end = new Date(el('check_out').value);
            let nights = Math.round((end - start) / 86400000);
            if (!Number.isFinite(nights) || nights < 0) nights = 0;

            if (el('check_in').value) {
                const next = new Date(start.getTime() + 86400000).toISOString().slice(0, 10);
                el('check_out').min = next;
            }

            const subtotal = price * nights * units;
            const insurance = el('with_insurance').checked ? Math.round(subtotal * rate / 100) : 0;

            out('room').textContent = price ? option.textContent.split(',')[0].trim() : 'Belum dipilih';
            out('nights').textContent = nights + ' malam';
            out('subtotal').textContent = rupiah(subtotal);
            out('insurance').textContent = rupiah(insurance);
            out('total').textContent = rupiah(subtotal + insurance);

            let warning = '';
            if (el('check_in').value && el('check_out').value && nights === 0) {
                warning = 'Tanggal check-out harus setelah check-in.';
            } else if (capacity && guests > capacity * units) {
                warning = `Kamar ini muat ${capacity} orang per unit. Tambah unit atau kurangi tamu.`;
            }
            out('warning').textContent = warning;
            out('warning').classList.toggle('text-red-700', warning !== '');
        }

        form.addEventListener('input', update);
        form.addEventListener('change', update);
        update();
    })();
</script>
@endpush
