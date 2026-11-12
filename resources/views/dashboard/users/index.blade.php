{{--
    Daftar akun - Marcel (admin saja)
    Variabel: $users (paginator, dengan bookings_count), $role, $search
--}}
@extends('layouts.dashboard')

@section('title', 'Manajemen akun')

@section('actions')
    <a href="{{ route('dashboard.users.create') }}" class="btn btn-primary">Tambah akun</a>
@endsection

@section('content')
    <form method="GET" action="{{ route('dashboard.users.index') }}" class="flex flex-wrap items-end gap-3">
        <div class="min-w-48 flex-1">
            <label for="q" class="form-label">Cari nama atau email</label>
            <input type="search" id="q" name="q" value="{{ $search }}" class="form-input" placeholder="rina">
        </div>
        <div>
            <label for="role" class="form-label">Role</label>
            <select id="role" name="role" class="form-input">
                <option value="">Semua</option>
                @foreach (['admin' => 'Admin', 'staff' => 'Staff', 'user' => 'User'] as $value => $label)
                    <option value="{{ $value }}" @selected($role === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-secondary h-[42px]">Terapkan</button>
    </form>

    <div class="mt-6 overflow-x-auto rounded-2xl border border-palung/10 bg-white">
        <table class="w-full min-w-[44rem] text-left text-sm">
            <caption class="sr-only">Daftar akun</caption>
            <thead class="border-b border-palung/10 text-palung/60">
                <tr>
                    <th scope="col" class="px-5 py-3 font-medium">Nama</th>
                    <th scope="col" class="px-5 py-3 font-medium">Role</th>
                    <th scope="col" class="px-5 py-3 font-medium">Status</th>
                    <th scope="col" class="px-5 py-3 font-medium">Booking</th>
                    <th scope="col" class="px-5 py-3 text-right font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-palung/10">
                @forelse ($users as $user)
                    <tr>
                        <td class="px-5 py-4">
                            <p class="font-semibold">{{ $user->name }}</p>
                            <p class="text-palung/60">{{ $user->email }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $user->role === 'admin' ? 'bg-palung text-white' : ($user->role === 'staff' ? 'bg-buih text-laut-tua' : 'bg-palung/10 text-palung') }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            @if ($user->is_active)
                                <span class="text-laut">Aktif</span>
                            @else
                                <span class="text-red-700">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-palung/70">{{ $user->bookings_count }}</td>
                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('dashboard.users.edit', $user) }}" class="btn btn-secondary px-3 py-1.5">Ubah</a>
                                @unless ($user->is(auth()->user()))
                                    <form method="POST" action="{{ route('dashboard.users.destroy', $user) }}"
                                          onsubmit="return confirm('{{ $user->bookings_count ? 'Akun ini punya riwayat booking, jadi akan dinonaktifkan. Lanjutkan?' : 'Hapus akun ini? Tindakan ini tidak bisa dibatalkan.' }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn px-3 py-1.5 text-red-700 hover:bg-red-50">
                                            {{ $user->bookings_count ? 'Nonaktifkan' : 'Hapus' }}
                                        </button>
                                    </form>
                                @endunless
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-palung/60">Tidak ada akun yang cocok dengan filter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $users->links() }}</div>
@endsection
