{{--
    Log aktivitas staff/admin - Marcel (admin saja)
    Variabel: $logs (paginator, dengan relasi user), $actors (staff+admin), $filters
--}}
@extends('layouts.dashboard')

@section('title', 'Log aktivitas')

@section('content')
    <form method="GET" action="{{ route('dashboard.activity-logs.index') }}" class="flex flex-wrap items-end gap-3">
        <div class="min-w-48 flex-1">
            <label for="user_id" class="form-label">Pelaku</label>
            <select id="user_id" name="user_id" class="form-input">
                <option value="">Semua staff dan admin</option>
                @foreach ($actors as $actor)
                    <option value="{{ $actor->id }}" @selected(($filters['user_id'] ?? null) == $actor->id)>
                        {{ $actor->name }} ({{ $actor->role }})
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="from" class="form-label">Dari tanggal</label>
            <input type="date" id="from" name="from" value="{{ $filters['from'] ?? '' }}" class="form-input">
        </div>
        <div>
            <label for="to" class="form-label">Sampai tanggal</label>
            <input type="date" id="to" name="to" value="{{ $filters['to'] ?? '' }}" class="form-input">
        </div>
        <button type="submit" class="btn btn-secondary h-[42px]">Terapkan</button>
    </form>
    @error('to') <p class="form-error">{{ $message }}</p> @enderror

    <div class="mt-6 overflow-x-auto rounded-2xl border border-palung/10 bg-white">
        <table class="w-full min-w-[40rem] text-left text-sm">
            <caption class="sr-only">Log aktivitas</caption>
            <thead class="border-b border-palung/10 text-palung/60">
                <tr>
                    <th scope="col" class="px-5 py-3 font-medium">Waktu</th>
                    <th scope="col" class="px-5 py-3 font-medium">Pelaku</th>
                    <th scope="col" class="px-5 py-3 font-medium">Tindakan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-palung/10">
                @forelse ($logs as $log)
                    <tr class="align-top">
                        <td class="whitespace-nowrap px-5 py-4 text-palung/70">
                            <time datetime="{{ $log->created_at->toIso8601String() }}">{{ $log->created_at->translatedFormat('d M Y, H:i') }}</time>
                        </td>
                        <td class="px-5 py-4">
                            <p class="font-semibold">{{ $log->user?->name ?? 'Akun terhapus' }}</p>
                            <p class="text-palung/55">{{ $log->user?->role }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <p>{{ $log->description }}</p>
                            <p class="mt-1 font-mono text-xs text-palung/50">{{ $log->action }}</p>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-5 py-10 text-center text-palung/60">Belum ada aktivitas pada filter ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $logs->links() }}</div>
@endsection
