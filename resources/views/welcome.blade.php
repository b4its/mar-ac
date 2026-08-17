<x-bauhaus.layout title="Beranda">
    <div class="w-full max-w-5xl">
        <div class="mb-10 text-center">
            <img src="{{ asset('images/logoPolnes.png') }}" alt="Logo Polnes" class="mx-auto mb-4 h-24 w-24 object-contain">
            <p class="mb-2 text-xs font-bold uppercase tracking-[0.3em] text-bauhaus-blue">Politeknik Negeri Samarinda</p>
            <h1 class="bauhaus-title text-3xl lg:text-5xl">Sistem Informasi Pemeliharaan</h1>
            <p class="mt-3 text-sm uppercase tracking-widest text-bauhaus-ink">
                Halo, <span class="font-bold text-bauhaus-blue">{{ auth()->user()->name }}</span> — pilih jenis laporan
            </p>
        </div>

        <div class="bauhaus-card relative p-5 sm:p-8 lg:p-10">
            <x-bauhaus.shape type="circle" color="yellow" class="absolute -left-6 -top-6 h-14 w-14" />

            @if (auth()->user()->hasRole('admin'))
                <div class="mb-8 rounded-2xl border border-blue-200 bg-blue-50 p-5 shadow-sm dark:border-blue-900 dark:bg-blue-950/40">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="font-display text-xs uppercase tracking-[0.25em] text-bauhaus-blue">Mode Admin</p>
                            <h2 class="mt-1 text-lg font-bold text-slate-950 dark:text-white">Lihat fitur pengguna atau kelola data dari panel admin.</h2>
                            <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Halaman ini menampilkan alur publik yang digunakan teknisi/pelapor.</p>
                        </div>
                        <a href="{{ route('filament.admin.pages.dashboard') }}" class="bauhaus-btn justify-center bg-bauhaus-blue px-5 py-2.5 text-xs text-white">Buka Panel Admin</a>
                    </div>
                </div>
            @endif

            <div class="grid gap-8 md:grid-cols-2">
                <a
                    href="{{ route('laporan.perawatan') }}"
                    class="group flex flex-col items-center gap-5 border border-bauhaus-black bg-bauhaus-paper p-5 text-center transition-transform duration-100 hover:-translate-y-1 hover:bg-bauhaus-paper-dark sm:p-8"
                >
                    <x-bauhaus.shape type="circle-hole" color="red" class="h-24 w-24" />
                    <div>
                        <h2 class="bauhaus-title text-xl lg:text-2xl">Kartu Pelaporan<br>Hasil Perawatan</h2>
                        <p class="mt-3 text-sm leading-relaxed text-bauhaus-ink">
                            Laporan hasil perawatan rutin / preventif, cleaning indoor &amp; outdoor
                        </p>
                    </div>
                    <span class="bauhaus-btn bg-bauhaus-yellow text-bauhaus-black group- group-">Buat Laporan →</span>
                </a>

                <a
                    href="{{ route('laporan.kerusakan') }}"
                    class="group flex flex-col items-center gap-5 border border-bauhaus-black bg-bauhaus-paper p-5 text-center transition-transform duration-100 hover:-translate-y-1 hover:bg-bauhaus-paper-dark sm:p-8"
                >
                    <x-bauhaus.shape type="triangle" color="blue" class="h-24 w-24" />
                    <div>
                        <h2 class="bauhaus-title text-xl lg:text-2xl">Laporan<br>Kerusakan</h2>
                        <p class="mt-3 text-sm leading-relaxed text-bauhaus-ink">
                            Laporkan alat yang rusak dengan tingkat kerusakan ringan, sedang, atau berat
                        </p>
                    </div>
                    <span class="bauhaus-btn bg-bauhaus-blue text-white group- group-">Buat Laporan →</span>
                </a>
            </div>

            <div class="mt-8 flex flex-col gap-4 border-t border-bauhaus-black pt-6 sm:flex-row sm:items-center">
                <x-bauhaus.shape type="square" color="yellow" class="h-8 w-8" />
                <div class="flex-1">
                    <p class="font-display text-sm uppercase tracking-widest">Lacak status laporan</p>
                    <p class="text-xs text-bauhaus-ink">Cek perkembangan laporan yang sudah dikirim</p>
                </div>
                <a href="{{ route('laporan.status') }}" class="bauhaus-btn justify-center bg-bauhaus-paper px-4 py-2 text-xs">Lacak →</a>
            </div>

            <div class="mt-6 flex flex-col gap-4 border-t border-bauhaus-black pt-6 sm:flex-row sm:items-center">
                <x-bauhaus.shape type="triangle" color="red" class="h-8 w-8" />
                <div class="flex-1">
                    <p class="font-display text-sm uppercase tracking-widest">Arsip laporan</p>
                    <p class="text-xs text-bauhaus-ink">Semua laporan yang pernah Anda kirim dalam satu halaman</p>
                </div>
                <a href="{{ route('laporan.saya') }}" class="bauhaus-btn justify-center bg-bauhaus-paper px-4 py-2 text-xs">Laporan Saya →</a>
            </div>

            <div class="mt-6 flex flex-col gap-4 border-t border-bauhaus-black pt-6 sm:flex-row sm:items-center">
                <x-bauhaus.shape type="square" color="blue" class="h-8 w-8" />
                <div class="flex-1">
                    <p class="font-display text-sm uppercase tracking-widest">Registrasi aset</p>
                    <p class="text-xs text-bauhaus-ink">Lihat data alat, status, jadwal, dan riwayat penanganan</p>
                </div>
                <a href="{{ route('aset.index') }}" class="bauhaus-btn justify-center bg-bauhaus-paper px-4 py-2 text-xs">Cari Aset →</a>
            </div>
        </div>
    </div>
</x-bauhaus.layout>
