<x-bauhaus.layout title="Laporan Hasil Perawatan">
    <div class="w-full max-w-4xl">
        <div class="mb-8 flex items-center gap-4">
            <x-bauhaus.shape type="circle-hole" color="red" class="h-12 w-12" />
            <div>
                <h1 class="bauhaus-title text-2xl lg:text-4xl">Kartu Pelaporan<br>Hasil Perawatan</h1>
                <p class="mt-1 text-xs font-bold uppercase tracking-[0.25em] text-bauhaus-blue">FM-Polnes-11-12-11/R3</p>
            </div>
        </div>

        <form method="POST" action="{{ route('laporan.perawatan.store') }}" enctype="multipart/form-data" class="bauhaus-card relative p-8 lg:p-10" id="maintenance-form">
            @csrf
            <x-bauhaus.shape type="square" color="yellow" class="absolute -right-6 -top-6 h-12 w-12" />

            {{-- Bagian 1: Form Utama --}}
            <div class="space-y-6">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white border-b border-slate-200 pb-2 dark:border-slate-700">Bagian 1</h2>

                <div class="grid gap-6 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <livewire:searchable-select
                            type="asset"
                            name="asset_id"
                            label="Alat / Mesin"
                            placeholder="Cari nama alat, kode alat, atau no. inventaris..."
                            :selected="old('asset_id') ? (int) old('asset_id') : null"
                            required
                            wire:key="maintenance-asset-select-section1"
                        />
                        @error('asset_id')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <livewire:searchable-select
                            type="vendor"
                            name="vendor_id"
                            label="Vendor / Pelaksana (opsional)"
                            placeholder="Cari nama vendor, kontak, atau telepon..."
                            :selected="old('vendor_id') ? (int) old('vendor_id') : null"
                            wire:key="maintenance-vendor-select-section1"
                        />
                    </div>

                    <div class="md:col-span-2">
                        <label for="jenis_pekerjaan_1" class="mb-2 block font-display text-sm uppercase tracking-widest">Jenis Pekerjaan</label>
                        <input
                            type="text"
                            id="jenis_pekerjaan_1"
                            name="jenis_pekerjaan"
                            value="{{ old('jenis_pekerjaan') }}"
                            required
                            placeholder="Contoh: Cleaning AC Split, Service Outdoor Unit"
                            class="bauhaus-input"
                        >
                        @error('jenis_pekerjaan')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="uraian_pekerjaan_1" class="mb-2 block font-display text-sm uppercase tracking-widest">Uraian Pekerjaan</label>
                        <textarea
                            id="uraian_pekerjaan_1"
                            name="uraian_pekerjaan"
                            rows="3"
                            placeholder="Detail pekerjaan yang dilakukan..."
                            class="bauhaus-input resize-y"
                        >{{ old('uraian_pekerjaan') }}</textarea>
                    </div>

                    <div>
                        <label for="tanggal_pelaksanaan_1" class="mb-2 block font-display text-sm uppercase tracking-widest">Tanggal Pelaksanaan</label>
                        <input
                            type="date"
                            id="tanggal_pelaksanaan_1"
                            name="tanggal_pelaksanaan"
                            value="{{ old('tanggal_pelaksanaan', now()->toDateString()) }}"
                            class="bauhaus-input"
                        >
                    </div>

                    <div>
                        <label for="biaya_1" class="mb-2 block font-display text-sm uppercase tracking-widest">Biaya Material (Rp)</label>
                        <input
                            type="text"
                            id="biaya_1"
                            name="biaya"
                            inputmode="numeric"
                            value="{{ old('biaya') }}"
                            class="bauhaus-input"
                            data-money-input
                            placeholder="0"
                        >
                        @error('biaya')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="biaya_jasa_1" class="mb-2 block font-display text-sm uppercase tracking-widest">Biaya Jasa (Rp)</label>
                        <input
                            type="text"
                            id="biaya_jasa_1"
                            name="biaya_jasa"
                            inputmode="numeric"
                            value="{{ old('biaya_jasa') }}"
                            class="bauhaus-input"
                            data-money-input
                            placeholder="0"
                        >
                        @error('biaya_jasa')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Upload Foto Section 1 --}}
                <div class="border-t border-bauhaus-black pt-6">
                    <p class="mb-4 font-display text-sm uppercase tracking-widest">Foto Dokumentasi</p>
                    
                    <div class="grid gap-6 md:grid-cols-2">
                        {{-- Foto Indoor --}}
                        <div data-photo-upload-wrap data-upload-index="section1_indoor">
                            <label for="foto_indoor_1" class="block font-display text-xs uppercase tracking-widest mb-2">Pencucian AC Indoor *</label>
                            <div 
                                class="upload-area cursor-pointer border-dashed border-2 border-slate-300 rounded-xl p-6 hover:border-bauhaus-blue transition-all bg-slate-50 hover:bg-blue-50 dark:bg-slate-900 dark:border-slate-700 hover:dark:bg-blue-900/20 group min-h-[200px] flex flex-col items-center justify-center text-center"
                            >
                                <input
                                    type="file"
                                    id="foto_indoor_1"
                                    name="foto_indoor"
                                    accept="image/*"
                                    required
                                    class="hidden"
                                    data-photo-input
                                    onchange="previewPhoto(this)"
                                >
                                
                                <!-- Placeholder -->
                                <div id="preview_section1_indoor" class="group-hover:scale-110 transition-transform">
                                    <svg class="mx-auto h-16 w-16 text-slate-400 group-hover:text-bauhaus-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <p class="mt-3 text-base font-semibold text-slate-500 dark:text-slate-400 group-hover:text-bauhaus-blue">Click untuk upload foto</p>
                                    <p class="mt-1 text-xs text-slate-400">PNG, JPG, WEBP hingga 5MB</p>
                                </div>
                                
                                <!-- Preview Image -->
                                <img id="img_section1_indoor" src="" alt="Preview indoor" class="hidden mx-auto mt-4 max-h-80 rounded-lg shadow-lg object-cover border-2 border-slate-200 dark:border-slate-700">
                                
                                <!-- Caption Input -->
                                <input
                                    type="text"
                                    name="caption_indoor"
                                    placeholder="Caption (misal: Kondisi sebelum cleaning...)"
                                    class="bauhaus-input mt-3 text-sm w-full"
                                    onblur="updateLabel(this)"
                                >
                                
                                <!-- Remove Button -->
                                <button type="button" onclick="removePhoto('section1_indoor')" class="hidden mt-3 w-full border border-red-500 bg-red-50 text-red-700 px-3 py-2 text-xs hover:bg-red-100 dark:bg-red-950 dark:text-red-300 dark:hover:bg-red-900 rounded-lg transition-colors flex items-center justify-center gap-2">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Hapus Foto
                                </button>
                            </div>
                            @error('foto_indoor')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                        </div>

                        {{-- Foto Outdoor --}}
                        <div data-photo-upload-wrap data-upload-index="section1_outdoor">
                            <label for="foto_outdoor_1" class="block font-display text-xs uppercase tracking-widest mb-2">Pencucian AC Outdoor *</label>
                            <div 
                                class="upload-area cursor-pointer border-dashed border-2 border-slate-300 rounded-xl p-6 hover:border-bauhaus-blue transition-all bg-slate-50 hover:bg-blue-50 dark:bg-slate-900 dark:border-slate-700 hover:dark:bg-blue-900/20 group min-h-[200px] flex flex-col items-center justify-center text-center"
                            >
                                <input
                                    type="file"
                                    id="foto_outdoor_1"
                                    name="foto_outdoor"
                                    accept="image/*"
                                    required
                                    class="hidden"
                                    data-photo-input
                                    onchange="previewPhoto(this)"
                                >
                                <div id="preview_section1_outdoor" class="group-hover:scale-110 transition-transform">
                                    <svg class="mx-auto h-16 w-16 text-slate-400 group-hover:text-bauhaus-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <p class="mt-3 text-base font-semibold text-slate-500 dark:text-slate-400 group-hover:text-bauhaus-blue">Click untuk upload foto</p>
                                    <p class="mt-1 text-xs text-slate-400">PNG, JPG, WEBP hingga 5MB</p>
                                </div>
                                <img id="img_section1_outdoor" src="" alt="Preview outdoor" class="hidden mx-auto mt-4 max-h-80 rounded-lg shadow-lg object-cover border-2 border-slate-200 dark:border-slate-700">
                                <input
                                    type="text"
                                    name="caption_outdoor"
                                    placeholder="Caption (misal: Outdoor unit bersih...)"
                                    class="bauhaus-input mt-3 text-sm w-full"
                                    onblur="updateLabel(this)"
                                >
                                <button type="button" onclick="removePhoto('section1_outdoor')" class="hidden mt-3 w-full border border-red-500 bg-red-50 text-red-700 px-3 py-2 text-xs hover:bg-red-100 dark:bg-red-950 dark:text-red-300 dark:hover:bg-red-900 rounded-lg transition-colors flex items-center justify-center gap-2">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Hapus Foto
                                </button>
                            </div>
                            @error('foto_outdoor')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                        </div>

                        {{-- Foto Kartu Maintenance --}}
                        <div data-photo-upload-wrap data-upload-index="section1_kartu">
                            <label for="foto_kartu_1" class="block font-display text-xs uppercase tracking-widest mb-2">Kartu Perawatan *</label>
                            <div 
                                class="upload-area cursor-pointer border-dashed border-2 border-slate-300 rounded-xl p-6 hover:border-bauhaus-blue transition-all bg-slate-50 hover:bg-blue-50 dark:bg-slate-900 dark:border-slate-700 hover:dark:bg-blue-900/20 group min-h-[200px] flex flex-col items-center justify-center text-center"
                            >
                                <input
                                    type="file"
                                    id="foto_kartu_1"
                                    name="foto_kartu"
                                    accept="image/*"
                                    required
                                    class="hidden"
                                    data-photo-input
                                    onchange="previewPhoto(this)"
                                >
                                <div id="preview_section1_kartu" class="group-hover:scale-110 transition-transform">
                                    <svg class="mx-auto h-16 w-16 text-slate-400 group-hover:text-bauhaus-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <p class="mt-3 text-base font-semibold text-slate-500 dark:text-slate-400 group-hover:text-bauhaus-blue">Click untuk upload foto</p>
                                    <p class="mt-1 text-xs text-slate-400">PNG, JPG, WEBP hingga 5MB</p>
                                </div>
                                <img id="img_section1_kartu" src="" alt="Preview kartu" class="hidden mx-auto mt-4 max-h-80 rounded-lg shadow-lg object-cover border-2 border-slate-200 dark:border-slate-700">
                                <input
                                    type="text"
                                    name="caption_kartu"
                                    placeholder="Caption (misal: Kartu maintenance terisi...)"
                                    class="bauhaus-input mt-3 text-sm w-full"
                                    onblur="updateLabel(this)"
                                >
                                <button type="button" onclick="removePhoto('section1_kartu')" class="hidden mt-3 w-full border border-red-500 bg-red-50 text-red-700 px-3 py-2 text-xs hover:bg-red-100 dark:bg-red-950 dark:text-red-300 dark:hover:bg-red-900 rounded-lg transition-colors flex items-center justify-center gap-2">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Hapus Foto
                                </button>
                            </div>
                            @error('foto_kartu')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                        </div>

                        {{-- Foto Tambahan --}}
                        <div data-photo-upload-wrap data-upload-index="section1_extra">
                            <label for="foto_extra_1" class="block font-display text-xs uppercase tracking-widest mb-2">Lampiran Tambahan</label>
                            <div 
                                class="upload-area cursor-pointer border-dashed border-2 border-slate-300 rounded-xl p-6 hover:border-bauhaus-blue transition-all bg-slate-50 hover:bg-blue-50 dark:bg-slate-900 dark:border-slate-700 hover:dark:bg-blue-900/20 group min-h-[200px] flex flex-col items-center justify-center text-center"
                            >
                                <input
                                    type="file"
                                    id="foto_extra_1"
                                    name="foto_extra"
                                    accept="image/*"
                                    class="hidden"
                                    data-photo-input
                                    onchange="previewPhoto(this)"
                                >
                                <div id="preview_section1_extra" class="group-hover:scale-110 transition-transform">
                                    <svg class="mx-auto h-16 w-16 text-slate-400 group-hover:text-bauhaus-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <p class="mt-3 text-base font-semibold text-slate-500 dark:text-slate-400 group-hover:text-bauhaus-blue">Click untuk upload foto</p>
                                    <p class="mt-1 text-xs text-slate-400">PNG, JPG, WEBP hingga 5MB</p>
                                </div>
                                <img id="img_section1_extra" src="" alt="Preview extra" class="hidden mx-auto mt-4 max-h-80 rounded-lg shadow-lg object-cover border-2 border-slate-200 dark:border-slate-700">
                                <input
                                    type="text"
                                    name="caption_extra"
                                    placeholder="Caption (jika ada)"
                                    class="bauhaus-input mt-3 text-sm w-full"
                                    onblur="updateLabel(this)"
                                >
                                <button type="button" onclick="removePhoto('section1_extra')" class="hidden mt-3 w-full border border-red-500 bg-red-50 text-red-700 px-3 py-2 text-xs hover:bg-red-100 dark:bg-red-950 dark:text-red-300 dark:hover:bg-red-900 rounded-lg transition-colors flex items-center justify-center gap-2">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Hapus Foto
                                </button>
                            </div>
                            @error('foto_extra')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Toggle Bagian 2 --}}
            <div class="mt-8 border-t border-bauhaus-black pt-6">
                <button
                    type="button"
                    id="toggle_section_2"
                    class="bauhaus-btn bg-bauhaus-yellow px-6 py-3 text-xs w-full"
                >
                    + Tampilkan Bagian 2 (Opsional)
                </button>
            </div>

            {{-- Bagian 2 --}}
            <div id="section_2" class="hidden space-y-6 mt-8">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white border-b border-slate-200 pb-2 dark:border-slate-700">Bagian 2 (Alat Kedua)</h2>

                <div class="grid gap-6 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <livewire:searchable-select
                            type="asset"
                            name="asset_id_2"
                            label="Alat / Mesin"
                            placeholder="Cari nama alat, kode alat, atau no. inventaris..."
                            :selected="old('asset_id_2') ? (int) old('asset_id_2') : null"
                            wire:key="maintenance-asset-select-section2"
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
                            required
                            placeholder="Contoh: Servis kondensor"
                            class="bauhaus-input"
                        >
                        @error('jenis_pekerjaan_2')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="uraian_pekerjaan_2" class="mb-2 block font-display text-sm uppercase tracking-widest">Uraian Pekerjaan</label>
                        <textarea
                            id="uraian_pekerjaan_2"
                            name="uraian_pekerjaan_2"
                            rows="3"
                            placeholder="Detail pekerjaan yang dilakukan..."
                            class="bauhaus-input resize-y"
                        >{{ old('uraian_pekerjaan_2') }}</textarea>
                    </div>

                    <div>
                        <label for="tanggal_pelaksanaan_2" class="mb-2 block font-display text-sm uppercase tracking-widest">Tanggal Pelaksanaan</label>
                        <input
                            type="date"
                            id="tanggal_pelaksanaan_2"
                            name="tanggal_pelaksanaan_2"
                            value="{{ old('tanggal_pelaksanaan_2', now()->toDateString()) }}"
                            class="bauhaus-input"
                        >
                    </div>

                    <div>
                        <label for="biaya_2" class="mb-2 block font-display text-sm uppercase tracking-widest">Biaya Material (Rp)</label>
                        <input
                            type="text"
                            id="biaya_2"
                            name="biaya_2"
                            inputmode="numeric"
                            value="{{ old('biaya_2') }}"
                            class="bauhaus-input"
                            data-money-input
                            placeholder="0"
                        >
                        @error('biaya_2')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="biaya_jasa_2" class="mb-2 block font-display text-sm uppercase tracking-widest">Biaya Jasa (Rp)</label>
                        <input
                            type="text"
                            id="biaya_jasa_2"
                            name="biaya_jasa_2"
                            inputmode="numeric"
                            value="{{ old('biaya_jasa_2') }}"
                            class="bauhaus-input"
                            data-money-input
                            placeholder="0"
                        >
                        @error('biaya_jasa_2')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Upload Foto Section 2 --}}
                <div class="border-t border-bauhaus-black pt-6">
                    <p class="mb-4 font-display text-sm uppercase tracking-widest">Foto Dokumentasi Bagian 2</p>
                    
                    <div class="grid gap-6 md:grid-cols-2">
                        {{-- Repeat similar structure for section 2 --}}
                        @foreach(['indoor' => 'Pencucian AC Indoor *', 'outdoor' => 'Pencucian AC Outdoor *', 'kartu' => 'Kartu Perawatan *', 'extra' => 'Lampiran Tambahan'] as $key => $label)
                            <div data-photo-upload-wrap data-upload-index="section2_{{ $key }}">
                                <label for="foto_{{ $key }}_2" class="block font-display text-xs uppercase tracking-widest mb-2">{{ $label }}</label>
                                <div 
                                    class="upload-area cursor-pointer border-dashed border-2 border-slate-300 rounded-xl p-6 hover:border-bauhaus-blue transition-all bg-slate-50 hover:bg-blue-50 dark:bg-slate-900 dark:border-slate-700 hover:dark:bg-blue-900/20 group min-h-[200px] flex flex-col items-center justify-center text-center"
                                >
                                    <input
                                        type="file"
                                        id="foto_{{ $key }}_2"
                                        name="foto_{{ $key }}_2"
                                        accept="image/*"
                                        {{ in_array($key, ['indoor', 'outdoor', 'kartu']) ? 'required_with="asset_id_2"' : '' }}
                                        class="hidden"
                                        data-photo-input
                                        onchange="previewPhoto(this)"
                                    >
                                    <div id="preview_section2_{{ $key }}" class="group-hover:scale-110 transition-transform">
                                        <svg class="mx-auto h-16 w-16 text-slate-400 group-hover:text-bauhaus-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        <p class="mt-3 text-base font-semibold text-slate-500 dark:text-slate-400 group-hover:text-bauhaus-blue">Click untuk upload foto</p>
                                        <p class="mt-1 text-xs text-slate-400">PNG, JPG, WEBP hingga 5MB</p>
                                    </div>
                                    <img id="img_section2_{{ $key }}" src="" alt="Preview {{ $key }}" class="hidden mx-auto mt-4 max-h-80 rounded-lg shadow-lg object-cover border-2 border-slate-200 dark:border-slate-700">
                                    <input
                                        type="text"
                                        name="caption_{{ $key }}_2"
                                        placeholder="Caption (jika ada)"
                                        class="bauhaus-input mt-3 text-sm w-full"
                                        onblur="updateLabel(this)"
                                    >
                                    <button type="button" onclick="removePhoto('section2_{{ $key }}')" class="hidden mt-3 w-full border border-red-500 bg-red-50 text-red-700 px-3 py-2 text-xs hover:bg-red-100 dark:bg-red-950 dark:text-red-300 dark:hover:bg-red-900 rounded-lg transition-colors flex items-center justify-center gap-2">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Hapus Foto
                                    </button>
                                </div>
                                @error("foto_{$key}_2")<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Footer Buttons --}}
            <div class="mt-8 flex flex-wrap items-center justify-between gap-4 border-t border-bauhaus-black pt-6">
                <a href="{{ route('welcome') }}" class="bauhaus-btn bg-bauhaus-paper px-5 py-2.5 text-xs w-auto">← Kembali</a>
                <button type="submit" class="bauhaus-btn bg-bauhaus-blue px-8 py-3 text-white hover:bg-bauhaus-blue-dark w-full sm:w-auto">
                    Kirim Laporan
                </button>
            </div>
        </form>
    </div>

    <script>
        // Function to preview photo
        function previewPhoto(input) {
            const file = input.files && input.files[0];
            const wrap = input.closest('[data-photo-upload-wrap]');
            if (!wrap || !file) return;
            
            // Validate file type
            if (!file.type.match('image.*')) {
                alert('File harus berupa gambar (PNG, JPG, WEBP)');
                input.value = '';
                return;
            }
            
            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran file terlalu besar! Maksimal 5MB');
                input.value = '';
                return;
            }
            
            const previewDiv = wrap.querySelector('[id^="preview_"]');
            const img = wrap.querySelector('[id^="img_"]');
            const button = wrap.querySelector('button[type="button"]');
            
            if (file) {
                try {
                    const url = URL.createObjectURL(file);
                    
                    if (previewDiv) previewDiv.classList.add('hidden');
                    img.src = url;
                    img.dataset.url = url;
                    img.classList.remove('hidden');
                    button?.classList.remove('hidden');
                    
                    console.log('✅ Photo uploaded successfully:', file.name);
                } catch (error) {
                    console.error('❌ Error uploading photo:', error);
                    alert('Gagal memuat gambar: ' + error.message);
                }
            }
        }

        // Function to update caption label
        function updateLabel(input) {
            const wrap = input.closest('[data-photo-upload-wrap]');
            if (!wrap) return;
            
            const img = wrap.querySelector('img');
            if (!img) return;
            
            const captionText = input.value.trim();
            if (captionText) {
                img.alt = captionText;
                console.log('✓ Caption updated:', captionText);
            }
        }

        // Function to remove photo
        function removePhoto(prefix) {
            const wrap = document.querySelector(`[data-upload-index="${prefix}"]`);
            if (!wrap) return;
            
            const input = wrap.querySelector('[data-photo-input]');
            const previewDiv = wrap.querySelector('[id^="preview_"]');
            const img = wrap.querySelector('[id^="img_"]');
            const captionInput = wrap.querySelector('input[name*="caption_"]');
            const button = wrap.querySelector('button[type="button"]');
            
            if (img && img.dataset.url) {
                URL.revokeObjectURL(img.dataset.url);
            }
            
            if (input) input.value = '';
            if (previewDiv) previewDiv.classList.remove('hidden');
            img.classList.add('hidden');
            img.removeAttribute('src');
            img.removeAttribute('alt');
            if (button) button.classList.add('hidden');
            if (captionInput) captionInput.value = '';
            
            console.log('🗑️ Photo removed:', prefix);
        }

        // Toggle section 2 visibility
        document.getElementById('toggle_section_2')?.addEventListener('click', function() {
            const section2 = document.getElementById('section_2');
            if (section2.classList.contains('hidden')) {
                section2.classList.remove('hidden');
                this.textContent = '− Sembunyikan Bagian 2';
                // Scroll to section 2
                setTimeout(() => {
                    section2.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 100);
            } else {
                section2.classList.add('hidden');
                this.textContent = '+ Tampilkan Bagian 2 (Opsional)';
            }
        });

        // Auto-trigger file input on area click
        document.addEventListener('click', function(e) {
            const wrap = e.target.closest('[data-photo-upload-wrap]');
            if (wrap && e.target.tagName !== 'BUTTON') {
                const input = wrap.querySelector('[data-photo-input]');
                if (input) {
                    e.preventDefault();
                    input.click();
                }
            }
        });
        
        // Drag and drop support
        document.addEventListener('dragover', function(e) {
            const uploadArea = e.target.closest('.upload-area');
            if (uploadArea) {
                e.preventDefault();
                uploadArea.style.borderColor = '#2563eb';
                uploadArea.style.backgroundColor = '#eff6ff';
            }
        });
        
        document.addEventListener('dragleave', function(e) {
            const uploadArea = e.target.closest('.upload-area');
            if (uploadArea) {
                uploadArea.style.borderColor = '';
                uploadArea.style.backgroundColor = '';
            }
        });
        
        document.addEventListener('drop', function(e) {
            e.preventDefault();
            const uploadArea = e.target.closest('.upload-area');
            if (uploadArea) {
                uploadArea.style.borderColor = '';
                uploadArea.style.backgroundColor = '';
                
                const wrap = uploadArea.closest('[data-photo-upload-wrap]');
                if (!wrap) return;
                
                const input = wrap.querySelector('[data-photo-input]');
                const files = e.dataTransfer.files;
                
                if (files.length > 0) {
                    // Create a FileList-like object
                    const dt = new DataTransfer();
                    dt.items.add(files[0]);
                    input.files = dt.files;
                    
                    // Trigger the change event manually
                    const event = new Event('change', { bubbles: true });
                    input.dispatchEvent(event);
                }
            }
        });
    </script>
</x-bauhaus.layout>
