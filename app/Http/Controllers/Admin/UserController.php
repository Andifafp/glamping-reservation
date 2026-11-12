<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Manajemen akun admin, staff, user. Hanya admin. Penanggung jawab: Marcel.
 */
class UserController extends Controller
{
    private const ROLES = [User::ROLE_ADMIN, User::ROLE_STAFF, User::ROLE_USER];

    public function index(Request $request): View
    {
        $role = in_array($request->query('role'), self::ROLES, true) ? $request->query('role') : null;
        $search = trim((string) $request->query('q'));

        $users = User::query()
            ->withCount('bookings')
            ->when($role, fn ($q) => $q->where('role', $role))
            ->when($search, fn ($q) => $q->where(fn ($w) => $w
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")))
            ->orderByRaw("FIELD(role, 'admin', 'staff', 'user')")
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('dashboard.users.index', compact('users', 'role', 'search'));
    }

    public function create(): View
    {
        return view('dashboard.users.create', ['user' => new User(['role' => User::ROLE_STAFF, 'is_active' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $user = User::create($data);

        ActivityLog::record('user.created', "Membuat akun {$user->role} {$user->email}", $user);

        return redirect()->route('dashboard.users.index')->with('status', "Akun {$user->email} dibuat.");
    }

    public function edit(User $user): View
    {
        return view('dashboard.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validated($request, $user);

        // Admin tidak boleh menurunkan role atau menonaktifkan akunnya sendiri
        if ($user->is($request->user())) {
            $data['role'] = $user->role;
            $data['is_active'] = true;
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->fill($data);
        $changes = array_keys($user->getDirty());
        $user->save();

        ActivityLog::record('user.updated', "Mengubah akun {$user->email}", $user, [
            'fields' => array_values(array_diff($changes, ['password', 'remember_token'])),
            'password_changed' => in_array('password', $changes, true),
        ]);

        return redirect()->route('dashboard.users.index')->with('status', "Akun {$user->email} diperbarui.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('info', 'Kamu tidak bisa menghapus akunmu sendiri.');
        }

        // Akun yang punya riwayat booking tidak boleh dihapus (database menolak).
        // Nonaktifkan saja supaya riwayat transaksi tetap ada.
        if ($user->bookings()->exists()) {
            $user->update(['is_active' => false]);
            ActivityLog::record('user.deactivated', "Menonaktifkan akun {$user->email}", $user);

            return back()->with('status', "Akun {$user->email} punya riwayat booking, jadi dinonaktifkan, bukan dihapus.");
        }

        $email = $user->email;
        ActivityLog::record('user.deleted', "Menghapus akun {$email}", null, ['email' => $email, 'role' => $user->role]);
        $user->delete();

        return back()->with('status', "Akun {$email} dihapus.");
    }

    private function validated(Request $request, ?User $user = null): array
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:100'],
            'email'     => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'phone'     => ['nullable', 'string', 'max:20'],
            'role'      => ['required', Rule::in(self::ROLES)],
            'password'  => [$user ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
        ], [], [
            'name'     => 'nama',
            'phone'    => 'nomor HP',
            'role'     => 'role',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
