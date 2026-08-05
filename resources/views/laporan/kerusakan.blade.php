<x-bauhaus.layout title="Laporan Kerusakan">
    <div class="w-full max-w-3xl">
        <div class="mb-8 flex items-center gap-4">
            <x-bauhaus.shape type="triangle" color="blue" class="h-12 w-12" />
            <div>
                <h1 class="bauhaus-title text-2xl lg:text-4xl">Laporan Kerusakan</h1>
                <p class="mt-1 text-xs font-bold uppercase tracking-[0.25em] text-bauhaus-blue">FM-Polnes-11-02-03/R3</p>
            </div>
        </div>

        <form method="POST" action="{{ route('laporan.kerusakan.store') }}" enctype="multipart/form-data" class="bauhaus-card relative p-8 lg:p-10">
            @csrf
            <x-bauhaus.shape type="circle" color="red" class="absolute -right-6 -top-6 h-12 w-12" />

            <div class="grid gap-6 md:grid-cols-2">
                <div class="md:col-span-2">
                    <livewire:searchable-select
                        type="asset"
                        name="asset_id"
                        label="Alat / Mesin"
                        placeholder="Cari nama alat, kode alat, atau no. inventaris..."
                        :selected="old('asset_id') ? (int) old('asset_id') : null"
                        required
                        wire:key="damage-asset-select"
                    />
                    @error('asset_id')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="mb-2 block font-display text-sm uppercase tracking-widest">Tingkat Kerusakan</label>
                    <div class="grid gap-4 sm:grid-cols-3">
                        @foreach ($levels as $level)
                            <label class="flex cursor-pointer items-center gap-3 border border-bauhaus-black p-4 transition-colors has-checked:bg-bauhaus-yellow">
                                <input type="radio" name="tingkat_kerusakan" value="{{ $level->value }}" class="h-5 w-5 accent-bauhaus-blue" @checked(old('tingkat_kerusakan') === $level->value)>
                                <span>
                                    <span class="block font-display text-sm uppercase tracking-widest">{{ $level->label() }}</span>
                                    <span class="block text-xs text-bauhaus-ink">
                                        @switch($level)
                                            @case(\App\Enums\DamageLevel::Ringan) Penanganan cepat @break
                                            @case(\App\Enums\DamageLevel::Sedang) Perlu pemeriksaan @break
                                            @case(\App\Enums\DamageLevel::Berat) Segera ditangani @break
                                        @endswitch
                                    </span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('tingkat_kerusakan')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label for="jenis_kerusakan" class="mb-2 block font-display text-sm uppercase tracking-widest">Jenis Kerusakan</label>
                    <input
                        type="text"
                        id="jenis_kerusakan"
                        name="jenis_kerusakan"
                        value="{{ old('jenis_kerusakan') }}"
                        required
                        placeholder="Contoh: AC tidak dingin, kompresor mati"
                        class="bauhaus-input"
                    >
                    @error('jenis_kerusakan')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label for="uraian_kerusakan" class="mb-2 block font-display text-sm uppercase tracking-widest">Uraian Kerusakan</label>
                    <textarea
                        id="uraian_kerusakan"
                        name="uraian_kerusakan"
                        rows="4"
                        placeholder="Penjelasan detail kondisi kerusakan…"
                        class="bauhaus-input resize-y"
                    >{{ old('uraian_kerusakan') }}</textarea>
                </div>

                <div>
                    <label for="tanggal_laporan" class="mb-2 block font-display text-sm uppercase tracking-widest">Tanggal Laporan</label>
                    <input type="date" id="tanggal_laporan" name="tanggal_laporan" value="{{ old('tanggal_laporan', now()->toDateString()) }}" class="bauhaus-input">
                </div>

                <div class="md:col-span-2 border-t border-bauhaus-black pt-6">
                    <p class="mb-1 font-display text-sm uppercase tracking-widest">Lampiran Foto Kerusakan <span class="text-xs normal-case tracking-normal text-bauhaus-ink">(opsional, maks. 10 foto)</span></p>

                    <div data-photo-dynamic data-photo-count="1" data-photo-max="10">
                        <div class="grid gap-4 sm:grid-cols-2" data-photo-grid></div>

                        <template data-photo-template>
                            <div class="border border-dashed border-bauhaus-black bg-bauhaus-paper p-4" data-photo-wrap>
                                <label class="flex cursor-pointer flex-col items-center justify-center gap-2 border border-bauhaus-black bg-bauhaus-paper px-4 py-6 text-center hover:bg-bauhaus-paper-dark">
                                    <span class="sr-only">Pilih gambar</span>
                                    <input type="file" name="photos[]" accept="image/*" class="sr-only" data-photo-input>
                                    <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z" />
                                        <circle cx="12" cy="13" r="4" />
                                    </svg>
                                    <span class="font-display text-xs uppercase tracking-widest" data-photo-placeholder>Pilih gambar…</span>
                                </label>
                                <img data-photo-image alt="Pratinjau foto" class="mt-3 hidden h-40 w-full border border-bauhaus-black object-cover">
                                <input type="text" name="photos_captions[]" class="bauhaus-input mt-3 text-sm" placeholder="Keterangan gambar / caption">
                                <button type="button" data-photo-remove class="mt-3 w-full border border-bauhaus-black bg-bauhaus-blue px-3 py-1.5 font-display text-xs uppercase tracking-widest text-white hover:bg-bauhaus-blue-dark">
                                    Hapus Foto
                                </button>
                            </div>
                        </template>

                        <button type="button" data-photo-add class="bauhaus-btn mt-4 bg-bauhaus-paper px-5 py-2.5 text-xs">
                            + Tambah Foto
                        </button>
                    </div>
                    @error('photos')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                    @error('photos.*')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
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
