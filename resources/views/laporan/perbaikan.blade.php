<x-bauhaus.layout title="Laporan Hasil Perbaikan">
    <div class="w-full max-w-3xl">
        <div class="mb-8 flex items-center gap-4">
            <x-bauhaus.shape type="triangle" color="yellow" class="h-12 w-12" />
            <div>
                <h1 class="bauhaus-title text-2xl lg:text-4xl">Laporan<br>Hasil Perbaikan</h1>
                <p class="mt-1 text-xs font-bold uppercase tracking-[0.25em] text-bauhaus-blue">Terhubung ke laporan kerusakan</p>
            </div>
        </div>

        <form method="POST" action="{{ route('laporan.perbaikan.store', $damage) }}" enctype="multipart/form-data" class="bauhaus-card relative p-8 lg:p-10">
            @csrf
            <x-bauhaus.shape type="circle" color="blue" class="absolute -right-6 -top-6 h-12 w-12" />

            @if ($damage->repairReport?->status === \App\Enums\RepairStatus::Revisi->value && $damage->repairReport->catatan)
                <div class="mb-6 border border-bauhaus-red bg-bauhaus-paper p-4">
                    <p class="font-display text-xs uppercase tracking-widest text-red-600">Catatan Revisi Admin</p>
                    <p class="mt-2 text-sm font-semibold">{{ $damage->repairReport->catatan }}</p>
                </div>
            @endif

            <div class="mb-6 grid gap-4 border border-bauhaus-black bg-bauhaus-paper p-4 text-sm md:grid-cols-2">
                <div><span class="block font-display text-xs uppercase tracking-widest">No. Kerusakan</span><span class="font-semibold">{{ $damage->nomor_laporan }}</span></div>
                <div><span class="block font-display text-xs uppercase tracking-widest">Aset</span><span class="font-semibold">{{ $damage->asset->nama_alat }}</span></div>
                <div><span class="block font-display text-xs uppercase tracking-widest">Lokasi</span><span class="font-semibold">{{ $damage->asset->room?->nama_ruangan ?: '-' }}</span></div>
                <div><span class="block font-display text-xs uppercase tracking-widest">Jenis Kerusakan</span><span class="font-semibold">{{ $damage->jenis_kerusakan }}</span></div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label for="jenis_pekerjaan" class="mb-2 block font-display text-sm uppercase tracking-widest">Jenis Pekerjaan</label>
                    <input id="jenis_pekerjaan" name="jenis_pekerjaan" type="text" value="{{ old('jenis_pekerjaan', $damage->repairReport?->jenis_pekerjaan) }}" required placeholder="Contoh: Penggantian kompresor" class="bauhaus-input">
                    @error('jenis_pekerjaan')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label for="uraian_pekerjaan" class="mb-2 block font-display text-sm uppercase tracking-widest">Uraian Pekerjaan</label>
                    <textarea id="uraian_pekerjaan" name="uraian_pekerjaan" rows="4" required placeholder="Detail pekerjaan perbaikan yang sudah dilakukan…" class="bauhaus-input resize-y">{{ old('uraian_pekerjaan', $damage->repairReport?->uraian_pekerjaan) }}</textarea>
                    @error('uraian_pekerjaan')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="tanggal_pelaksanaan" class="mb-2 block font-display text-sm uppercase tracking-widest">Tanggal Pelaksanaan</label>
                    <input id="tanggal_pelaksanaan" name="tanggal_pelaksanaan" type="date" value="{{ old('tanggal_pelaksanaan', $damage->repairReport?->tanggal_pelaksanaan?->toDateString() ?? now()->toDateString()) }}" class="bauhaus-input">
                </div>

                <div>
                    <livewire:searchable-select
                        type="vendor"
                        name="vendor_id"
                        label="Vendor / Pelaksana (opsional)"
                        placeholder="Cari nama vendor, kontak, atau telepon..."
                        :selected="old('vendor_id', $damage->repairReport?->vendor_id) ? (int) old('vendor_id', $damage->repairReport?->vendor_id) : null"
                        wire:key="repair-vendor-select"
                    />
                </div>

                <div>
                    <label for="biaya" class="mb-2 block font-display text-sm uppercase tracking-widest">Biaya Material (Rp)</label>
                    <input type="text" id="biaya" name="biaya" inputmode="numeric" value="{{ old('biaya', (int) ($damage->repairReport?->biaya ?? 0)) }}" class="bauhaus-input" data-money-input>
                    @error('biaya')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="biaya_jasa" class="mb-2 block font-display text-sm uppercase tracking-widest">Biaya Jasa (Rp)</label>
                    <input type="text" id="biaya_jasa" name="biaya_jasa" inputmode="numeric" value="{{ old('biaya_jasa', (int) ($damage->repairReport?->biaya_jasa ?? 0)) }}" class="bauhaus-input" data-money-input>
                    @error('biaya_jasa')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2 border-t border-bauhaus-black pt-6">
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                        <p class="font-display text-sm uppercase tracking-widest">Lampiran Foto Perbaikan</p>
                        <button type="button" id="add-attachment" class="bauhaus-btn bg-bauhaus-yellow px-4 py-2 text-xs">+ Tambah Foto</button>
                    </div>
                    @error('lampiran')<p class="mb-3 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                    <div id="attachments" class="space-y-4">
                        <div class="attachment-row grid gap-3 border border-bauhaus-black p-4 md:grid-cols-2" data-index="0">
                            <div>
                                <label class="mb-2 block font-display text-xs uppercase tracking-widest">Gambar</label>
                                <input type="file" name="lampiran[0][file]" accept="image/*" required class="bauhaus-input">
                            </div>
                            <div>
                                <label class="mb-2 block font-display text-xs uppercase tracking-widest">Caption</label>
                                <input type="text" name="lampiran[0][caption]" required placeholder="Contoh: Kondisi setelah perbaikan" class="bauhaus-input">
                            </div>
                        </div>
                    </div>
                    <p class="mt-3 text-xs font-semibold uppercase tracking-widest text-bauhaus-ink">Maksimal 10 foto, format JPG/PNG/WebP, 5MB per file.</p>
                </div>
            </div>

            <div class="mt-8 flex flex-wrap items-center justify-between gap-4 border-t border-bauhaus-black pt-6">
                <a href="{{ route('laporan.status', ['nomor' => $damage->nomor_laporan]) }}" class="bauhaus-btn bg-bauhaus-paper px-5 py-2.5 text-xs">← Kembali</a>
                <button type="submit" class="bauhaus-btn bg-bauhaus-blue px-8 py-3 text-white hover:bg-bauhaus-blue-dark">
                    Kirim Laporan Perbaikan
                </button>
            </div>
        </form>
    </div>

    <script>
        const button = document.getElementById('add-attachment');
        const container = document.getElementById('attachments');

        button?.addEventListener('click', () => {
            const count = container.querySelectorAll('.attachment-row').length;

            if (count >= 10) {
                return;
            }

            const row = document.createElement('div');
            row.className = 'attachment-row grid gap-3 border border-bauhaus-black p-4 md:grid-cols-2';
            row.innerHTML = `
                <div>
                    <label class="mb-2 block font-display text-xs uppercase tracking-widest">Gambar</label>
                    <input type="file" name="lampiran[${count}][file]" accept="image/*" required class="bauhaus-input">
                </div>
                <div>
                    <label class="mb-2 block font-display text-xs uppercase tracking-widest">Caption</label>
                    <input type="text" name="lampiran[${count}][caption]" required placeholder="Keterangan foto" class="bauhaus-input">
                </div>
            `;

            container.appendChild(row);
        });
    </script>
</x-bauhaus.layout>
