<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Middleware role: ->middleware(['auth', 'role:staff,admin'])
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureRole::class,
        ]);

        // Pengunjung belum login yang membuka halaman 'auth' diarahkan ke /masuk
        $middleware->redirectGuestsTo(fn () => route('login'));

        // User yang sudah login lalu membuka /masuk atau /daftar
        $middleware->redirectUsersTo(fn (Request $request) => $request->user()?->canAccessDashboard()
            ? route('dashboard.index')
            : route('home'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
