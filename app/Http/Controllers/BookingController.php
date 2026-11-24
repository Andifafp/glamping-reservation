<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\RoomType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Reservasi, pembayaran dummy, dan cek booking. Penanggung jawab: Marcel.
 *
 * Alur: create (form) -> store (booking pending, unit ditahan)
 *       -> payment (pilih metode) -> pay (payment paid, booking confirmed)
 *       -> success (kode booking) -> check/lookup (kartu check-in)
 */
class BookingController extends Controller
{
    public function create(Request $request): View
    {
        return view('booking.create', [
            'rooms'        => RoomType::active()->with('category')->orderBy('price_per_night')->get(),
            'selectedRoom' => RoomType::active()->where('slug', $request->query('kamar'))->first(),
            'user'         => $request->user(),
            'checkIn'      => $request->query('check_in'),
            'checkOut'     => $request->query('check_out'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'room_type_id'   => ['required', Rule::exists('room_types', 'id')->where('is_active', true)],
            'check_in'       => ['required', 'date', 'after_or_equal:today'],
            'check_out'      => ['required', 'date', 'after:check_in'],
            'units'          => ['required', 'integer', 'min:1', 'max:10'],
            'guests'         => ['required', 'integer', 'min:1', 'max:60'],
            'contact_name'   => ['required', 'string', 'max:100'],
            'contact_email'  => ['required', 'email', 'max:255'],
            'contact_phone'  => ['required', 'string', 'regex:/^(\+62|62|0)8[0-9]{7,12}$/'],
            'with_insurance' => ['nullable', 'boolean'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ], [
            'room_type_id.required' => 'Pilih jenis kamar.',
            'check_in.after_or_equal' => 'Tanggal check-in tidak boleh di masa lalu.',
            'check_out.after' => 'Tanggal check-out harus setelah check-in.',
            'contact_phone.regex' => 'Nomor HP harus diawali 08, 62, atau +62, contoh 081234567890.',
        ], [
            'check_in' => 'tanggal check-in',
            'check_out' => 'tanggal check-out',
            'units' => 'jumlah unit',
            'guests' => 'jumlah tamu',
            'contact_name' => 'nama pemesan',
            'contact_email' => 'email',
            'contact_phone' => 'nomor HP',
        ]);

        $checkIn = Carbon::parse($data['check_in'])->startOfDay();
        $checkOut = Carbon::parse($data['check_out'])->startOfDay();
        $nights = (int) $checkIn->diffInDays($checkOut);
        $units = (int) $data['units'];
        $withInsurance = $request->boolean('with_insurance');

        $booking = DB::transaction(function () use ($data, $checkIn, $checkOut, $nights, $units, $withInsurance, $request) {
            // Kunci baris jenis kamar supaya dua orang tidak memesan unit terakhir bersamaan
            $room = RoomType::whereKey($data['room_type_id'])->lockForUpdate()->firstOrFail();

            if ((int) $data['guests'] > $room->capacity * $units) {
                throw ValidationException::withMessages([
                    'guests' => "{$room->name} muat {$room->capacity} orang per unit. Untuk {$data['guests']} tamu butuh minimal "
                        . (int) ceil($data['guests'] / $room->capacity) . ' unit.',
                ]);
            }

            $available = $room->availableUnits($checkIn->toDateString(), $checkOut->toDateString());
            if ($available < $units) {
                throw ValidationException::withMessages([
                    'units' => $available > 0
                        ? "Di tanggal itu {$room->name} hanya tersisa {$available} unit."
                        : "{$room->name} sudah penuh di tanggal itu. Coba tanggal atau kamar lain.",
                ]);
            }

            $subtotal = $room->price_per_night * $nights * $units;
            $insuranceFee = $withInsurance ? (int) round($subtotal * config('glamping.insurance_rate') / 100) : 0;

            return Booking::create([
                'booking_code'    => Booking::generateCode(),
                'user_id'         => $request->user()->id,
                'room_type_id'    => $room->id,
                'contact_name'    => $data['contact_name'],
                'contact_email'   => Str::lower($data['contact_email']),
                'contact_phone'   => $data['contact_phone'],
                'check_in'        => $checkIn->toDateString(),
                'check_out'       => $checkOut->toDateString(),
                'nights'          => $nights,
                'units'           => $units,
                'guests'          => (int) $data['guests'],
                'price_per_night' => $room->price_per_night,
                'subtotal'        => $subtotal,
                'with_insurance'  => $withInsurance,
                'insurance_fee'   => $insuranceFee,
                'total_price'     => $subtotal + $insuranceFee,
                'status'          => Booking::STATUS_PENDING,
                'notes'           => $data['notes'] ?? null,
            ]);
        });

        return redirect()->route('booking.payment', $booking)
            ->with('status', "Booking {$booking->booking_code} dibuat. Unit ditahan untukmu, selesaikan pembayaran.");
    }

    public function payment(Request $request, Booking $booking): View|RedirectResponse
    {
        $this->authorizeOwner($request, $booking);

        if ($booking->status === Booking::STATUS_CONFIRMED) {
            return redirect()->route('booking.success', $booking);
        }
        if ($booking->status !== Booking::STATUS_PENDING) {
            return redirect()->route('profile.index')->with('info', "Booking {$booking->booking_code} tidak bisa dibayar lagi.");
        }

        return view('booking.payment', ['booking' => $booking->load('roomType')]);
    }

    public function pay(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorizeOwner($request, $booking);

        $data = $request->validate([
            'method' => ['required', Rule::in(array_keys(config('glamping.payment_methods')))],
        ], ['method.required' => 'Pilih metode pembayaran.']);

        DB::transaction(function () use ($booking, $data) {
            $booking = Booking::whereKey($booking->id)->lockForUpdate()->firstOrFail();

            if ($booking->status !== Booking::STATUS_PENDING) {
                return; // sudah dibayar (misal tombol diklik dua kali)
            }

            Payment::create([
                'booking_id' => $booking->id,
                'reference'  => 'PAY-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
                'method'     => $data['method'],
                'amount'     => $booking->total_price,
                'status'     => Payment::STATUS_PAID,
                'paid_at'    => now(),
            ]);

            $booking->update([
                'status'       => Booking::STATUS_CONFIRMED,
                'confirmed_at' => now(),
            ]);
        });

        return redirect()->route('booking.success', $booking)->with('status', 'Pembayaran berhasil (simulasi).');
    }

    public function success(Request $request, Booking $booking): View|RedirectResponse
    {
        $this->authorizeOwner($request, $booking);

        if ($booking->status === Booking::STATUS_PENDING) {
            return redirect()->route('booking.payment', $booking);
        }

        return view('booking.success', ['booking' => $booking->load('roomType')]);
    }

    /**
     * Halaman cek booking. Pemilik yang sedang login dan membuka ?kode=...
     * langsung melihat kartunya; orang lain harus memasukkan email/HP.
     */
    public function check(Request $request): View
    {
        $booking = null;

        if ($request->filled('kode') && $request->user()) {
            $booking = Booking::with('roomType')
                ->where('booking_code', Str::upper(trim($request->query('kode'))))
                ->where('user_id', $request->user()->id)
                ->first();
        }

        return view('booking.check', compact('booking'));
    }

    public function lookup(Request $request): View|RedirectResponse
    {
        $data = $request->validate([
            'booking_code' => ['required', 'string', 'max:20'],
            'contact'      => ['required', 'string', 'max:255'],
        ], [], [
            'booking_code' => 'kode booking',
            'contact'      => 'email atau nomor HP',
        ]);

        $booking = Booking::with('roomType')
            ->where('booking_code', Str::upper(trim($data['booking_code'])))
            ->first();

        if (! $booking || ! $this->contactMatches($booking, $data['contact'])) {
            return back()->withInput()->withErrors([
                'booking_code' => 'Kode booking dan email/nomor HP tidak cocok. Periksa lagi penulisannya.',
            ]);
        }

        return view('booking.check', compact('booking'));
    }

    private function contactMatches(Booking $booking, string $contact): bool
    {
        $contact = trim($contact);

        if (str_contains($contact, '@')) {
            return Str::lower($contact) === Str::lower($booking->contact_email);
        }

        return $this->normalizePhone($contact) !== ''
            && $this->normalizePhone($contact) === $this->normalizePhone($booking->contact_phone);
    }

    /** 0812..., 62812..., +62812... dianggap sama. */
    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        return preg_replace('/^(62|0)/', '', $digits);
    }

    private function authorizeOwner(Request $request, Booking $booking): void
    {
        abort_unless((int) $booking->user_id === (int) $request->user()->id, 403);
    }
}
