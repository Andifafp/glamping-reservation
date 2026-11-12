<div class="mt-24 print:hidden" aria-hidden="true">
    @include('partials.wave', ['fill' => '#0B2A3C'])
</div>
<footer class="-mt-px bg-palung text-white print:hidden">

    <div class="mx-auto grid max-w-6xl gap-10 px-4 py-14 sm:px-6 md:grid-cols-[1.4fr_1fr_1fr_1fr]">
        <div>
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                @include('partials.logo')
                <span class="font-display text-xl">Bet Glamping</span>
            </a>
            <p class="mt-4 max-w-xs text-sm leading-6 text-white/70">
                Tenda safari, kabin kayu, dan dome di tebing pantai. Tempat tidurnya hotel, suaranya ombak.
            </p>
        </div>

        <div>
            <h2 class="font-sans text-sm font-semibold text-fajar">Menginap</h2>
            <ul class="mt-4 space-y-2.5 text-sm text-white/75">
                <li><a href="{{ route('rooms.index') }}" class="hover:text-white">Semua kamar</a></li>
                <li><a href="{{ route('booking.create') }}" class="hover:text-white">Pesan kamar</a></li>
                <li><a href="{{ route('booking.check') }}" class="hover:text-white">Cek booking</a></li>
                <li><a href="{{ route('home') }}#asuransi" class="hover:text-white">Asuransi dan refund</a></li>
            </ul>
        </div>

        <div>
            <h2 class="font-sans text-sm font-semibold text-fajar">Bacaan</h2>
            <ul class="mt-4 space-y-2.5 text-sm text-white/75">
                <li><a href="{{ route('articles.index') }}" class="hover:text-white">Artikel</a></li>
                <li><a href="{{ route('home') }}#cara-reservasi" class="hover:text-white">Cara reservasi</a></li>
                <li><a href="{{ route('profile.index') }}" class="hover:text-white">Profil saya</a></li>
            </ul>
        </div>

        <div id="kontak">
            <h2 class="font-sans text-sm font-semibold text-fajar">Kontak</h2>
            <address class="mt-4 space-y-2.5 text-sm not-italic text-white/75">
                <p>Jl. Tebing Karang No. 8, Pantai Sawarna, Lebak, Banten</p>
                <p><a href="https://wa.me/6281200000000" class="hover:text-white">WhatsApp 0812-0000-0000</a></p>
                <p><a href="mailto:halo@betglamping.test" class="hover:text-white">halo@betglamping.test</a></p>
            </address>
        </div>
    </div>

    <div class="border-t border-white/10">
        <p class="mx-auto max-w-6xl px-4 py-5 text-xs text-white/50 sm:px-6">
            &copy; {{ date('Y') }} Bet Glamping. Proyek tugas Pemrograman Web Kelas B. Pembayaran di situs ini hanya simulasi.
        </p>
    </div>
</footer>
