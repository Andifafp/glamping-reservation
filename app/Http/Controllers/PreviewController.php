<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * SEMENTARA. Hanya untuk melihat template halaman yang belum punya controller asli.
 *
 * - Hanya bisa dibuka saat APP_ENV=local.
 * - Halaman yang butuh login memakai akun user@demo.test.
 * - Tombol kirim form hanya menampilkan pesan "belum dibuat".
 *
 * Setiap anggota mengganti route di routes/web.php ke controller miliknya.
 * Setelah semua route diganti, HAPUS file ini.
 */
class PreviewController extends Controller
{
    public function __construct()
    {
        abort_unless(app()->isLocal(), 404);
    }

    // ---------- Marcel: auth ----------

    public function login(): View
    {
        return view('auth.login');
    }

    public function register(): View
    {
        return view('auth.register');
    }

    // ---------- Hafiz: reservasi & cek booking ----------

    public function bookingCreate(Request $request): View
    {
        return view('booking.create', [
            'rooms'        => RoomType::active()->with('category')->orderBy('price_per_night')->get(),
            'selectedRoom' => RoomType::where('slug', $request->query('kamar'))->first(),
            'user'         => $this->demoUser(),
            'checkIn'      => $request->query('check_in'),
            'checkOut'     => $request->query('check_out'),
        ]);
    }

    public function bookingPayment(Booking $booking): View
    {
        return view('booking.payment', ['booking' => $booking->load('roomType')]);
    }

    public function bookingSuccess(Booking $booking): View
    {
        return view('booking.success', ['booking' => $booking->load('roomType')]);
    }

    public function bookingCheck(Request $request): View
    {
        // Preview: langsung tampil dari ?kode= TANPA verifikasi email/HP.
        // Versi asli (Hafiz) wajib mencocokkan kode + email/HP lewat form POST.
        $booking = $request->filled('kode')
            ? Booking::with('roomType')->where('booking_code', strtoupper($request->query('kode')))->first()
            : null;

        return view('booking.check', compact('booking'));
    }

    // ---------- Thoni: profil & pembatalan ----------

    public function profile(): View
    {
        $user = $this->demoUser();

        return view('profile.index', [
            'user'     => $user,
            'bookings' => $user->bookings()->with(['roomType', 'latestCancellation'])->latest('check_in')->get(),
        ]);
    }

    public function profileEdit(): View
    {
        return view('profile.edit', ['user' => $this->demoUser()]);
    }

    public function cancellationCreate(Booking $booking): View
    {
        $percentage = $booking->refundPercentageAt(now());

        return view('cancellations.create', [
            'booking'          => $booking->load('roomType'),
            'refundPercentage' => $percentage,
            'refundAmount'     => (int) round($booking->total_price * $percentage / 100),
        ]);
    }

    // ---------- Semua form POST/PUT yang belum dibuat ----------

    public function notBuilt(Request $request): RedirectResponse
    {
        $owner = match (true) {
            $request->routeIs('login.attempt', 'register.store', 'logout') => 'Marcel',
            $request->routeIs('booking.*') => 'Hafiz',
            default => 'Thoni',
        };

        return back()->withInput()->with('info', "Template saja: proses form ini belum dibuat (bagian {$owner}).");
    }

    private function demoUser(): User
    {
        return auth()->user() ?? User::where('email', 'user@demo.test')->firstOrFail();
    }
}
