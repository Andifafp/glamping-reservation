<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CancellationController;
use App\Http\Controllers\Dashboard\ArticleController as DashboardArticleController;
use App\Http\Controllers\Dashboard\CancellationController as DashboardCancellationController;
use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\RoomTypeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute aplikasi Bet Glamping
|--------------------------------------------------------------------------
| NAMA rute (->name(...)) sudah dipakai di template, jangan diganti.
| Versi ini sudah tidak memakai PreviewController.
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

// ---------- Hafiz: auth ----------
Route::middleware('guest')->group(function () {
    Route::get('/masuk', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/masuk', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/daftar', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/daftar', [AuthController::class, 'register'])->name('register.store');
});
Route::post('/keluar', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ---------- Marcel: cek booking (tanpa login) ----------
Route::get('/cek-booking', [BookingController::class, 'check'])->name('booking.check');
Route::post('/cek-booking', [BookingController::class, 'lookup'])->middleware('throttle:10,1')->name('booking.lookup');

// ---------- Halaman yang wajib login ----------
Route::middleware('auth')->group(function () {
    // Marcel: reservasi
    Route::get('/reservasi', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/reservasi', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/reservasi/{booking:booking_code}/bayar', [BookingController::class, 'payment'])->name('booking.payment');
    Route::post('/reservasi/{booking:booking_code}/bayar', [BookingController::class, 'pay'])->name('booking.pay');
    Route::get('/reservasi/{booking:booking_code}/sukses', [BookingController::class, 'success'])->name('booking.success');

    // Thoni: profil & pembatalan
    Route::get('/profil', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profil/ubah', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/reservasi/{booking:booking_code}/batal', [CancellationController::class, 'create'])->name('cancellations.create');
    Route::post('/reservasi/{booking:booking_code}/batal', [CancellationController::class, 'store'])->name('cancellations.store');
});

// ---------- Dashboard staff & admin ----------
Route::prefix('dashboard')
    ->name('dashboard.')
    ->middleware(['auth', 'role:staff,admin'])
    ->group(function () {
        // Thoni: isi beranda dashboard (ringkasan, transaksi, grafik)
        Route::get('/', DashboardController::class)->name('index');

        // Thoni: proses pembatalan
        Route::get('/pembatalan', [DashboardCancellationController::class, 'index'])->name('cancellations.index');
        Route::get('/pembatalan/{cancellation}', [DashboardCancellationController::class, 'show'])->name('cancellations.show');
        Route::post('/pembatalan/{cancellation}/setujui', [DashboardCancellationController::class, 'approve'])->name('cancellations.approve');
        Route::post('/pembatalan/{cancellation}/tolak', [DashboardCancellationController::class, 'reject'])->name('cancellations.reject');

        // Fais: kategori, jenis kamar, artikel
        Route::resource('kategori', CategoryController::class)
            ->except('show')
            ->parameters(['kategori' => 'category'])
            ->names('categories');
        Route::resource('kamar', RoomTypeController::class)
            ->except('show')
            ->parameters(['kamar' => 'roomType'])
            ->names('room-types');
        Route::resource('artikel', DashboardArticleController::class)
            ->except('show')
            ->parameters(['artikel' => 'article'])
            ->names('articles');

        // Hafiz: khusus admin
        Route::middleware('role:admin')->group(function () {
            Route::resource('akun', UserController::class)
                ->except('show')
                ->parameters(['akun' => 'user'])
                ->names('users');
            Route::get('/log-aktivitas', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        });
    });
