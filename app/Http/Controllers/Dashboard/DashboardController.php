<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Beranda dashboard staff/admin.
 * SEMENTARA dari Marcel. Isinya (kartu ringkasan, tabel transaksi, grafik)
 * dikerjakan Thoni: ganti method ini dan view dashboard/index.blade.php.
 */
class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard.index');
    }
}
