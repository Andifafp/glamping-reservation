{{-- Isi form akun (dipakai create dan edit). Parameter: $user --}}
@php $isSelf = $user->exists && $user->is(auth()->user()); @endphp

<div class="grid gap-5 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label for="name" class="form-label">Nama</label>
        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="form-input" required>
        @error('name') <p class="form-error">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="email" class="form-label">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="form-input" required>
        @error('email') <p class="form-error">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="phone" class="form-label">Nomor HP <span class="font-normal text-palung/50">(opsional)</span></label>
        <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="form-input">
        @error('phone') <p class="form-error">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="role" class="form-label">Role</label>
        <select id="role" name="role" class="form-input" @disabled($isSelf)>
            @foreach (['admin' => 'Admin', 'staff' => 'Staff', 'user' => 'User'] as $value => $label)
                <option value="{{ $value }}" @selected(old('role', $user->role) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @if ($isSelf)
            <input type="hidden" name="role" value="{{ $user->role }}">
            <p class="form-hint">Kamu tidak bisa mengubah role akunmu sendiri.</p>
        @endif
        @error('role') <p class="form-error">{{ $message }}</p> @enderror
    </div>
    <div class="flex items-end">
        <label class="flex items-center gap-3 pb-2.5 text-sm font-medium">
            <input type="checkbox" name="is_active" value="1" class="size-4 accent-laut"
                   @checked(old('is_active', $user->is_active ?? true)) @disabled($isSelf)>
            Akun aktif (bisa masuk)
        </label>
        @if ($isSelf)
            <input type="hidden" name="is_active" value="1">
        @endif
    </div>

    <div class="border-t border-palung/10 pt-5 sm:col-span-2">
        <p class="font-semibold">{{ $user->exists ? 'Ganti password' : 'Password' }}</p>
        @if ($user->exists)
            <p class="form-hint">Kosongkan jika tidak ingin mengganti.</p>
        @endif
    </div>
    <div>
        <label for="password" class="form-label">Password</label>
        <input type="password" id="password" name="password" class="form-input" autocomplete="new-password" @required(! $user->exists)>
        <p class="form-hint">Minimal 8 karakter.</p>
        @error('password') <p class="form-error">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="password_confirmation" class="form-label">Ulangi password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" autocomplete="new-password">
    </div>
</div>

<div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
    <a href="{{ route('dashboard.users.index') }}" class="btn btn-secondary">Batal</a>
    <button type="submit" class="btn btn-primary">{{ $user->exists ? 'Simpan perubahan' : 'Buat akun' }}</button>
</div>
