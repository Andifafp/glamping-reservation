<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PreviewController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute aplikasi Bet Glamping
|--------------------------------------------------------------------------
| NAMA rute (->name(...)) sudah dipakai di template, jangan diganti.
| Rute yang masih memakai PreviewController diganti oleh pemilik modulnya.
|
| Middleware:
|   'auth'              harus login
|   'guest'             hanya untuk yang belum login
|   'role:admin'        hanya admin
|   'role:staff,admin'  staff dan admin
*/

// ---------- Fais: beranda, kamar, artikel ----------
Route::get('/', [HomeController::class, '__invoke'])->name('home');
Route::get('/kamar', [RoomController::class, 'index'])->name('rooms.index');
Route::get('/kamar/{roomType:slug}', [RoomController::class, 'show'])->name('rooms.show');
Route::get('/artikel', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/artikel/{article:slug}', [ArticleController::class, 'show'])->name('articles.show');

// ---------- Marcel: auth ----------
Route::middleware('guest')->group(function () {
    Route::get('/masuk', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/masuk', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/daftar', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/daftar', [AuthController::class, 'register'])->name('register.store');
});
Route::post('/keluar', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ---------- Hafiz: cek booking (tanpa login) ----------
Route::get('/cek-booking', [PreviewController::class, 'bookingCheck'])->name('booking.check');
Route::post('/cek-booking', [PreviewController::class, 'notBuilt'])->name('booking.lookup');

// ---------- Halaman yang wajib login ----------
Route::middleware('auth')->group(function () {
    // Hafiz: reservasi (TODO: ganti PreviewController)
    Route::get('/reservasi', [PreviewController::class, 'bookingCreate'])->name('booking.create');
    Route::post('/reservasi', [PreviewController::class, 'notBuilt'])->name('booking.store');
    Route::get('/reservasi/{booking:booking_code}/bayar', [PreviewController::class, 'bookingPayment'])->name('booking.payment');
    Route::post('/reservasi/{booking:booking_code}/bayar', [PreviewController::class, 'notBuilt'])->name('booking.pay');
    Route::get('/reservasi/{booking:booking_code}/sukses', [PreviewController::class, 'bookingSuccess'])->name('booking.success');

    // Thoni: profil & pembatalan (TODO: ganti PreviewController)
    Route::get('/profil', [PreviewController::class, 'profile'])->name('profile.index');
    Route::get('/profil/ubah', [PreviewController::class, 'profileEdit'])->name('profile.edit');
    Route::put('/profil', [PreviewController::class, 'notBuilt'])->name('profile.update');
    Route::get('/reservasi/{booking:booking_code}/batal', [PreviewController::class, 'cancellationCreate'])->name('cancellations.create');
    Route::post('/reservasi/{booking:booking_code}/batal', [PreviewController::class, 'notBuilt'])->name('cancellations.store');
});

// ---------- Dashboard staff & admin ----------
Route::prefix('dashboard')
    ->name('dashboard.')
    ->middleware(['auth', 'role:staff,admin'])
    ->group(function () {
        // Thoni: isi beranda dashboard (ringkasan, transaksi, grafik)
        Route::get('/', DashboardController::class)->name('index');

        // Thoni: pembatalan          -> name('cancellations.index'), dst.
        // Fais : kategori            -> Route::resource('kategori', ...)->names('categories')
        // Fais : jenis kamar         -> Route::resource('kamar', ...)->names('room-types')
        // Fais : artikel             -> Route::resource('artikel', ...)->names('articles')
        // Menu di sidebar otomatis muncul setelah rutenya dibuat.

        // Marcel: khusus admin
        Route::middleware('role:admin')->group(function () {
            Route::resource('akun', UserController::class)
                ->except('show')
                ->parameters(['akun' => 'user'])
                ->names('users');
            Route::get('/log-aktivitas', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        });
    });
