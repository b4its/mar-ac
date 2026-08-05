<x-bauhaus.layout title="Kartu Pelaporan Hasil Perawatan">
    <div class="w-full max-w-3xl">
        <div class="mb-8 flex items-center gap-4">
            <x-bauhaus.shape type="circle-hole" color="red" class="h-12 w-12" />
            <div>
                <h1 class="bauhaus-title text-2xl lg:text-4xl">Kartu Pelaporan<br>Hasil Perawatan</h1>
                <p class="mt-1 text-xs font-bold uppercase tracking-[0.25em] text-bauhaus-blue">FM-Polnes-11-12-11/R3</p>
            </div>
        </div>

        <form method="POST" action="{{ route('laporan.perawatan.store') }}" enctype="multipart/form-data" class="bauhaus-card relative p-8 lg:p-10">
            @csrf
            <x-bauhaus.shape type="triangle" color="yellow" class="absolute -right-6 -top-6 h-12 w-12" />

            <div class="grid gap-6 md:grid-cols-2">
                <div class="md:col-span-2">
                    <livewire:searchable-select
                        type="asset"
                        name="asset_id"
                        label="Alat / Mesin"
                        placeholder="Cari nama alat, kode alat, atau no. inventaris..."
                        :selected="old('asset_id') ? (int) old('asset_id') : null"
                        required
                        wire:key="maintenance-asset-select"
                    />
                    @error('asset_id')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label for="jenis_pekerjaan" class="mb-2 block font-display text-sm uppercase tracking-widest">Jenis Pekerjaan</label>
                    <input
                        type="text"
                        id="jenis_pekerjaan"
                        name="jenis_pekerjaan"
                        value="{{ old('jenis_pekerjaan') }}"
                        required
                        placeholder="Contoh: Cleaning Indoor & Outdoor AC"
                        class="bauhaus-input"
                    >
                    @error('jenis_pekerjaan')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label for="uraian_pekerjaan" class="mb-2 block font-display text-sm uppercase tracking-widest">Uraian Pekerjaan</label>
                    <textarea
                        id="uraian_pekerjaan"
                        name="uraian_pekerjaan"
                        rows="4"
                        placeholder="Detail pekerjaan yang dilaksanakan…"
                        class="bauhaus-input resize-y"
                    >{{ old('uraian_pekerjaan') }}</textarea>
                </div>

                <div>
                    <label for="tanggal_pelaksanaan" class="mb-2 block font-display text-sm uppercase tracking-widest">Tanggal Pelaksanaan</label>
                    <input type="date" id="tanggal_pelaksanaan" name="tanggal_pelaksanaan" value="{{ old('tanggal_pelaksanaan', now()->toDateString()) }}" class="bauhaus-input">
                </div>

                <div>
                    <livewire:searchable-select
                        type="vendor"
                        name="vendor_id"
                        label="Vendor / Pelaksana (opsional)"
                        placeholder="Cari nama vendor, kontak, atau telepon..."
                        :selected="old('vendor_id') ? (int) old('vendor_id') : null"
                        wire:key="maintenance-vendor-select"
                    />
                </div>

                <div>
                    <label for="biaya" class="mb-2 block font-display text-sm uppercase tracking-widest">Biaya Material (Rp)</label>
                    <input type="text" id="biaya" name="biaya" inputmode="numeric" value="{{ old('biaya', 0) }}" class="bauhaus-input" data-money-input>
                    @error('biaya')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="biaya_jasa" class="mb-2 block font-display text-sm uppercase tracking-widest">Biaya Jasa (Rp)</label>
                    <input type="text" id="biaya_jasa" name="biaya_jasa" inputmode="numeric" value="{{ old('biaya_jasa', 0) }}" class="bauhaus-input" data-money-input>
                    @error('biaya_jasa')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2 border-t border-bauhaus-black pt-6">
                    <p class="mb-4 font-display text-sm uppercase tracking-widest">Lampiran Foto Wajib</p>
                    <div class="grid gap-4 md:grid-cols-3">
                        <div>
                            <p class="mb-2 font-display text-xs uppercase tracking-widest">Pencucian AC Indoor</p>
                            <label for="foto_indoor" class="block cursor-pointer border border-bauhaus-black bg-bauhaus-paper p-3 hover:bg-bauhaus-paper-dark" data-photo-preview-wrap>
                                <span class="sr-only">Pilih gambar</span>
                                <input type="file" id="foto_indoor" name="foto_indoor" accept="image/*" required data-photo-preview class="sr-only">
                                <span class="flex flex-col items-center justify-center gap-2 py-4 text-center">
                                    <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z" />
                                        <circle cx="12" cy="13" r="4" />
                                    </svg>
                                    <span class="font-display text-xs uppercase tracking-widest" data-photo-placeholder>Pilih gambar…</span>
                                </span>
                                <img data-photo-image alt="Pratinjau Pencucian AC Indoor" class="hidden h-40 w-full border border-bauhaus-black object-cover">
                                <span class="mt-2 block text-center text-xs font-semibold text-slate-600 dark:text-slate-300">Pencucian AC Indoor</span>
                            </label>
                            @error('foto_indoor')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <p class="mb-2 font-display text-xs uppercase tracking-widest">Pencucian AC Outdoor</p>
                            <label for="foto_outdoor" class="block cursor-pointer border border-bauhaus-black bg-bauhaus-paper p-3 hover:bg-bauhaus-paper-dark" data-photo-preview-wrap>
                                <span class="sr-only">Pilih gambar</span>
                                <input type="file" id="foto_outdoor" name="foto_outdoor" accept="image/*" required data-photo-preview class="sr-only">
                                <span class="flex flex-col items-center justify-center gap-2 py-4 text-center">
                                    <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z" />
                                        <circle cx="12" cy="13" r="4" />
                                    </svg>
                                    <span class="font-display text-xs uppercase tracking-widest" data-photo-placeholder>Pilih gambar…</span>
                                </span>
                                <img data-photo-image alt="Pratinjau Pencucian AC Outdoor" class="hidden h-40 w-full border border-bauhaus-black object-cover">
                                <span class="mt-2 block text-center text-xs font-semibold text-slate-600 dark:text-slate-300">Pencucian AC Outdoor</span>
                            </label>
                            @error('foto_outdoor')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <p class="mb-2 font-display text-xs uppercase tracking-widest">Kartu Perawatan</p>
                            <label for="foto_kartu" class="block cursor-pointer border border-bauhaus-black bg-bauhaus-paper p-3 hover:bg-bauhaus-paper-dark" data-photo-preview-wrap>
                                <span class="sr-only">Pilih gambar</span>
                                <input type="file" id="foto_kartu" name="foto_kartu" accept="image/*" required data-photo-preview class="sr-only">
                                <span class="flex flex-col items-center justify-center gap-2 py-4 text-center">
                                    <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z" />
                                        <circle cx="12" cy="13" r="4" />
                                    </svg>
                                    <span class="font-display text-xs uppercase tracking-widest" data-photo-placeholder>Pilih gambar…</span>
                                </span>
                                <img data-photo-image alt="Pratinjau Kartu Perawatan" class="hidden h-40 w-full border border-bauhaus-black object-cover">
                                <span class="mt-2 block text-center text-xs font-semibold text-slate-600 dark:text-slate-300">Kartu Perawatan</span>
                            </label>
                            @error('foto_kartu')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex flex-wrap items-center justify-between gap-4 border-t border-bauhaus-black pt-6">
                <a href="{{ route('welcome') }}" class="bauhaus-btn bg-bauhaus-paper px-5 py-2.5 text-xs">← Kembali</a>
                <button type="submit" class="bauhaus-btn bg-bauhaus-blue px-8 py-3 text-white hover:bg-bauhaus-blue-dark">
                    Kirim Laporan
                </button>
            </div>
        </form>
    </div>
</x-bauhaus.layout>
