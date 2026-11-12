<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Membatasi rute berdasarkan role. Penanggung jawab: Marcel.
 *
 * Pemakaian di routes/web.php (selalu bersama 'auth'):
 *     ->middleware(['auth', 'role:admin'])          hanya admin
 *     ->middleware(['auth', 'role:staff,admin'])    staff dan admin
 *
 * Alias 'role' didaftarkan di bootstrap/app.php.
 */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, $roles, true)) {
            abort(403);
        }

        // Akun yang dinonaktifkan saat sedang login langsung dikeluarkan
        if (! $user->is_active) {
            auth()->logout();
            $request->session()->invalidate();

            return redirect()->route('login')->withErrors(['email' => 'Akun kamu sedang dinonaktifkan.']);
        }

        return $next($request);
    }
}
