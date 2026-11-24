<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Profil user: riwayat reservasi + edit data diri. Penanggung jawab: Thoni.
 */
class ProfileController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $bookings = $user->bookings()
            ->with(['roomType', 'latestCancellation'])
            ->latest('check_in')
            ->get();

        return view('profile.index', compact('user', 'bookings'));
    }

    public function edit(Request $request): View
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name'             => ['required', 'string', 'max:100'],
            'email'            => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'            => ['required', 'string', 'regex:/^(\+62|62|0)8[0-9]{7,12}$/'],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password'         => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'email.unique'                   => 'Email ini sudah dipakai akun lain.',
            'phone.regex'                    => 'Nomor HP harus diawali 08, 62, atau +62, contoh 081234567890.',
            'current_password.required_with' => 'Masukkan password sekarang untuk mengganti password.',
            'current_password.current_password' => 'Password sekarang salah.',
        ], [
            'name'     => 'nama',
            'phone'    => 'nomor HP',
            'password' => 'password baru',
        ]);

        $user->fill([
            'name'  => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
        ]);

        if (! empty($data['password'])) {
            $user->password = $data['password']; // otomatis di-hash oleh cast 'hashed'
        }

        $user->save();

        return redirect()->route('profile.index')->with('status', 'Data diri disimpan.');
    }
}
