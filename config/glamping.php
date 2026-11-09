<?php

/*
|--------------------------------------------------------------------------
| Aturan bisnis aplikasi reservasi glamping
|--------------------------------------------------------------------------
| Semua angka aturan bisnis dikumpulkan di sini supaya tidak ditulis ulang
| di banyak controller. Akses dengan: config('glamping.insurance_rate')
*/

return [

    // Biaya asuransi = persentase dari subtotal sewa kamar
    'insurance_rate' => 10, // persen

    // Batas hari untuk refund. "Lebih dari 3 hari" berarti selisih hari
    // antara tanggal pengajuan pembatalan dan tanggal check-in minimal 4.
    'refund_threshold_days' => 3,

    // Persentase refund berdasarkan asuransi dan jarak hari ke check-in
    'refund' => [
        'with_insurance' => [
            'before_threshold' => 100, // batal > 3 hari sebelum check-in
            'after_threshold'  => 50,  // batal <= 3 hari sebelum check-in
        ],
        'without_insurance' => [
            'before_threshold' => 50,
            'after_threshold'  => 0,
        ],
    ],

    // Jam check-in / check-out (informasi di kartu check-in)
    'check_in_time'  => '14:00',
    'check_out_time' => '12:00',

    // Metode pembayaran dummy / sandbox
    'payment_methods' => [
        'bank_transfer' => 'Transfer Bank (dummy)',
        'e_wallet'      => 'E-Wallet (dummy)',
        'qris'          => 'QRIS (dummy)',
    ],
];
