{{--
    Pesan singkat setelah aksi. Dari controller:
        return redirect()->route('...')->with('status', 'Profil disimpan');
        return back()->with('info', '...');
--}}
@if (session('status') || session('info'))
    <div class="mx-auto max-w-6xl px-4 pt-6 sm:px-6">
        <div role="status"
             class="rounded-xl border px-4 py-3 text-sm {{ session('status') ? 'border-ombak/30 bg-buih text-laut-tua' : 'border-fajar/50 bg-fajar/15 text-fajar-tua' }}">
            {{ session('status') ?? session('info') }}
        </div>
    </div>
@endif
