<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Nama hari dan bulan dalam bahasa Indonesia (APP_LOCALE=id di .env)
        Carbon::setLocale(config('app.locale'));
    }
}
