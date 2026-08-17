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

            {{-- Filter lokasi bercabang: Gedung → Jurusan → Ruangan --}}
            <div class="mb-8 border-b border-bauhaus-black pb-6">
                <p class="mb-4 font-display text-sm uppercase tracking-widest">Filter Lokasi Aset <span class="text-slate-500">(opsional — mempersempit daftar alat)</span></p>
                <livewire:lokasi-filter wire:key="maintenance-lokasi-filter" />
            </div>

            {{-- Vendor / Pelaksana (berlaku untuk seluruh laporan) --}}
            <div class="mb-8">
                <livewire:searchable-select
                    type="vendor"
                    name="vendor_id"
                    label="Vendor / Pelaksana (opsional)"
                    placeholder="Cari nama vendor, kontak, atau telepon..."
                    :selected="old('vendor_id') ? (int) old('vendor_id') : null"
                    wire:key="maintenance-vendor-select"
                />
            </div>

            <div data-perawatan-sections>
                {{-- ================= BAGIAN 1 ================= --}}
                <section data-bagian="1" class="border-t border-bauhaus-black pt-6">
                    <div class="mb-6 flex items-center gap-3">
                        <span class="inline-flex h-8 items-center bg-bauhaus-blue px-3 font-display text-xs uppercase tracking-widest text-white">Bagian 1</span>
                        <span class="font-display text-xs uppercase tracking-widest text-slate-500">Laporan Pertama</span>
                    </div>

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
                            <label for="biaya" class="mb-2 block font-display text-sm uppercase tracking-widest">Biaya Material (Rp)</label>
                            <input type="text" id="biaya" name="biaya" inputmode="numeric" value="{{ old('biaya', 0) }}" class="bauhaus-input" data-money-input>
                            @error('biaya')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="biaya_jasa" class="mb-2 block font-display text-sm uppercase tracking-widest">Biaya Jasa (Rp)</label>
                            <input type="text" id="biaya_jasa" name="biaya_jasa" inputmode="numeric" value="{{ old('biaya_jasa', 0) }}" class="bauhaus-input" data-money-input>
                            @error('biaya_jasa')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="md:col-span-2 border-t border-bauhaus-black pt-6">
                            <p class="mb-4 font-display text-sm uppercase tracking-widest">Lampiran Foto Wajib</p>
                            <div class="grid gap-4 md:grid-cols-3">
                                @include('laporan.partials.photo-slot', ['field' => 'foto_indoor', 'caption' => 'Pencucian AC Indoor', 'bagian' => 1, 'required' => true])
                                @include('laporan.partials.photo-slot', ['field' => 'foto_outdoor', 'caption' => 'Pencucian AC Outdoor', 'bagian' => 1, 'required' => true])
                                @include('laporan.partials.photo-slot', ['field' => 'foto_kartu', 'caption' => 'Kartu Perawatan', 'bagian' => 1, 'required' => true])
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ================= BAGIAN 2 (opsional, maksimal 2 bagian) ================= --}}
                <section data-bagian="2" hidden class="border-t border-bauhaus-black pt-6">
                    <div class="mb-6 flex items-center gap-3">
                        <span class="inline-flex h-8 items-center bg-bauhaus-yellow px-3 font-display text-xs uppercase tracking-widest">Bagian 2</span>
                        <span class="font-display text-xs uppercase tracking-widest text-slate-500">Laporan Kedua</span>
                        <button
                            type="button"
                            data-bagian-remove
                            class="bauhaus-btn ml-auto bg-bauhaus-red px-3 py-1.5 text-xs text-white hover:bg-red-700"
                        >Hapus Bagian</button>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <livewire:searchable-select
                                type="asset"
                                name="asset_id_2"
                                label="Alat / Mesin"
                                placeholder="Cari nama alat, kode alat, atau no. inventaris..."
                                :selected="old('asset_id_2') ? (int) old('asset_id_2') : null"
                                wire:key="maintenance-asset-select-2"
                            />
                            @error('asset_id_2')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="jenis_pekerjaan_2" class="mb-2 block font-display text-sm uppercase tracking-widest">Jenis Pekerjaan</label>
                            <input
                                type="text"
                                id="jenis_pekerjaan_2"
                                name="jenis_pekerjaan_2"
                                value="{{ old('jenis_pekerjaan_2') }}"
                                placeholder="Contoh: Cleaning Indoor & Outdoor AC"
                                class="bauhaus-input"
                            >
                            @error('jenis_pekerjaan_2')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="uraian_pekerjaan_2" class="mb-2 block font-display text-sm uppercase tracking-widest">Uraian Pekerjaan</label>
                            <textarea
                                id="uraian_pekerjaan_2"
                                name="uraian_pekerjaan_2"
                                rows="4"
                                placeholder="Detail pekerjaan yang dilaksanakan…"
                                class="bauhaus-input resize-y"
                            >{{ old('uraian_pekerjaan_2') }}</textarea>
                        </div>

                        <div>
                            <label for="tanggal_pelaksanaan_2" class="mb-2 block font-display text-sm uppercase tracking-widest">Tanggal Pelaksanaan</label>
                            <input type="date" id="tanggal_pelaksanaan_2" name="tanggal_pelaksanaan_2" value="{{ old('tanggal_pelaksanaan_2') }}" class="bauhaus-input">
                        </div>

                        <div>
                            <label for="biaya_2" class="mb-2 block font-display text-sm uppercase tracking-widest">Biaya Material (Rp)</label>
                            <input type="text" id="biaya_2" name="biaya_2" inputmode="numeric" value="{{ old('biaya_2', 0) }}" class="bauhaus-input" data-money-input>
                            @error('biaya_2')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="biaya_jasa_2" class="mb-2 block font-display text-sm uppercase tracking-widest">Biaya Jasa (Rp)</label>
                            <input type="text" id="biaya_jasa_2" name="biaya_jasa_2" inputmode="numeric" value="{{ old('biaya_jasa_2', 0) }}" class="bauhaus-input" data-money-input>
                            @error('biaya_jasa_2')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="md:col-span-2 border-t border-bauhaus-black pt-6">
                            <p class="mb-4 font-display text-sm uppercase tracking-widest">Lampiran Foto Wajib</p>
                            <div class="grid gap-4 md:grid-cols-3">
                                @include('laporan.partials.photo-slot', ['field' => 'foto_indoor_2', 'caption' => 'Pencucian AC Indoor', 'bagian' => 2])
                                @include('laporan.partials.photo-slot', ['field' => 'foto_outdoor_2', 'caption' => 'Pencucian AC Outdoor', 'bagian' => 2])
                                @include('laporan.partials.photo-slot', ['field' => 'foto_kartu_2', 'caption' => 'Kartu Perawatan', 'bagian' => 2])
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="mt-8 flex flex-wrap items-center justify-between gap-4 border-t border-bauhaus-black pt-6">
                <a href="{{ route('welcome') }}" class="bauhaus-btn bg-bauhaus-paper px-5 py-2.5 text-xs">← Kembali</a>
                <div class="flex flex-wrap items-center gap-3">
                    <button type="button" data-bagian-add class="bauhaus-btn bg-bauhaus-yellow px-5 py-2.5 text-xs">
                        + Tambah Bagian
                    </button>
                    <button type="submit" class="bauhaus-btn bg-bauhaus-blue px-8 py-3 text-white hover:bg-bauhaus-blue-dark">
                        Kirim Laporan
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        (function () {
            var container = document.querySelector('[data-perawatan-sections]');
            if (!container) {
                return;
            }

            var addBtn = document.querySelector('[data-bagian-add]');
            var MAX_BAGIAN = 2;
            var sections = Array.prototype.slice.call(container.querySelectorAll('[data-bagian]'));

            function visibleCount() {
                return sections.filter(function (section) { return !section.hidden; }).length;
            }

            function sync() {
                if (addBtn) {
                    addBtn.hidden = visibleCount() >= MAX_BAGIAN;
                }
                sections.forEach(function (section) {
                    var remove = section.querySelector('[data-bagian-remove]');
                    if (remove) {
                        remove.hidden = section.hidden || parseInt(section.dataset.bagian, 10) === 1;
                    }
                });
            }

            function resetSection(section) {
                section.querySelectorAll('input[type="search"], input[type="text"], input[type="date"], textarea').forEach(function (el) {
                    el.value = '';
                });
                section.querySelectorAll('input[type="file"]').forEach(function (el) {
                    el.value = '';
                    var wrap = el.closest('[data-photo-preview-wrap]');
                    if (wrap) {
                        var img = wrap.querySelector('[data-photo-image]');
                        var placeholder = wrap.querySelector('[data-photo-placeholder]');
                        if (img) {
                            img.classList.add('hidden');
                            img.removeAttribute('src');
                        }
                        if (placeholder) {
                            placeholder.textContent = 'Pilih gambar…';
                        }
                    }
                });
                section.querySelectorAll('input[type="hidden"]').forEach(function (el) {
                    el.value = '';
                });
            }

            if (addBtn) {
                addBtn.addEventListener('click', function () {
                    var next = sections.find(function (section) { return section.hidden; });
                    if (next) {
                        next.hidden = false;
                        sync();
                    }
                });
            }

            container.addEventListener('click', function (event) {
                var remove = event.target.closest('[data-bagian-remove]');
                if (!remove) {
                    return;
                }
                var section = remove.closest('[data-bagian]');
                if (section) {
                    resetSection(section);
                    section.hidden = true;
                    sync();
                }
            });

            sync();
        })();
    </script>
</x-bauhaus.layout>
