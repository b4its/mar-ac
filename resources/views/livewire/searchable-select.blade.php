<div class="relative" wire:click.outside="$set('open', false)">
    @if ($label)
        <label class="mb-2 block font-display text-sm uppercase tracking-widest">{{ $label }}</label>
    @endif

    <input type="hidden" name="{{ $name }}" value="{{ $selectedId }}" @if ($required) required @endif>

    <div class="relative">
        <input
            type="search"
            wire:model.live.debounce.250ms="search"
            wire:focus="$set('open', true)"
            placeholder="{{ $this->locationLocked ? 'Pilih ruangan terlebih dahulu...' : $placeholder }}"
            autocomplete="off"
            class="bauhaus-input pr-11"
            @if ($required) aria-required="true" @endif
            @if ($this->locationLocked) disabled @endif
        >
        <button
            type="button"
            wire:click="$set('open', {{ $open ? 'false' : 'true' }})"
            class="absolute right-2 top-1/2 inline-flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-md text-slate-400 hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-slate-100"
            aria-label="Buka pilihan"
            @if ($this->locationLocked) disabled @endif
        >
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="m6 9 6 6 6-6" />
            </svg>
        </button>
    </div>

    @if ($open)
        <div class="absolute z-30 mt-2 w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl shadow-slate-200/70 dark:border-slate-700 dark:bg-slate-900 dark:shadow-black/30">
            @if ($this->locationLocked)
                <div class="px-4 py-3 text-sm text-slate-500 dark:text-slate-400">
                    Pilih gedung, jurusan, lalu ruangan pada filter lokasi untuk memilih alat.
                </div>
            @elseif (! $creating)
                <div class="max-h-72 overflow-y-auto py-1">
                    @forelse ($this->options as $option)
                        <button
                            type="button"
                            wire:click="selectOption({{ $option['id'] }})"
                            class="block w-full px-4 py-3 text-left transition hover:bg-blue-50 dark:hover:bg-slate-800"
                        >
                            <span class="block text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $option['label'] }}</span>
                            @if ($option['description'])
                                <span class="mt-0.5 block text-xs text-slate-500 dark:text-slate-400">{{ $option['description'] }}</span>
                            @endif
                        </button>
                    @empty
                        <div class="px-4 py-3 text-sm text-slate-500 dark:text-slate-400">Data tidak ditemukan.</div>
                    @endforelse
                </div>

                @php
                    $addLabel = trim($search) !== '' && ! $this->exactMatch ? ' "'.$search.'"' : '';
                @endphp
                <div class="border-t border-slate-200 p-3 dark:border-slate-700">
                    <button type="button" wire:click="startCreate" class="bauhaus-btn w-full bg-bauhaus-blue px-4 py-2 text-xs text-white hover:bg-bauhaus-blue-dark">
                        + Tambah {{ $type === 'vendor' ? 'vendor' : 'aset' }} baru{{ $addLabel }}
                    </button>
                </div>
            @else
                <div class="space-y-3 p-4">
                    <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                        Tambah {{ $type === 'vendor' ? 'Vendor' : 'Aset' }} Baru
                    </p>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Nama {{ $type === 'vendor' ? 'Vendor' : 'Aset' }}</label>
                        <input type="text" wire:model="newName" class="bauhaus-input" placeholder="Nama" required>
                        @error('nama_vendor')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                        @error('nama_alat')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>

                    @if ($type === 'vendor')
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Kontak</label>
                                <input type="text" wire:model="newContact" class="bauhaus-input" placeholder="Nama kontak">
                                @error('kontak')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Telepon</label>
                                <input type="text" wire:model="newPhone" class="bauhaus-input" placeholder="Nomor telepon">
                                @error('telepon')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Alamat</label>
                            <textarea wire:model="newAddress" rows="3" class="bauhaus-input resize-y" placeholder="Alamat vendor"></textarea>
                            @error('alamat')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Keterangan</label>
                            <textarea wire:model="newVendorDescription" rows="3" class="bauhaus-input resize-y" placeholder="Opsional"></textarea>
                            @error('keterangan')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                        </div>
                    @else
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Jenis Alat</label>
                                <input type="text" wire:model="newAssetType" class="bauhaus-input" placeholder="Contoh: Pendingin Ruangan">
                                @error('jenis_alat')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Kode Alat</label>
                                <input type="text" wire:model="newCode" class="bauhaus-input" placeholder="Opsional">
                                @error('kode_alat')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">No. Inventaris</label>
                                <input type="text" wire:model="newInventory" class="bauhaus-input" placeholder="Opsional">
                                @error('no_inventaris')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Room</label>
                                <select wire:model="newRoomId" class="bauhaus-input">
                                    <option value="">Pilih room</option>
                                    @foreach ($this->roomOptions as $room)
                                        <option value="{{ $room['id'] }}">{{ $room['label'] }}</option>
                                    @endforeach
                                </select>
                                @error('room_id')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Department</label>
                                <select wire:model="newDepartmentId" class="bauhaus-input">
                                    <option value="">Pilih department</option>
                                    @foreach ($this->departmentOptions as $department)
                                        <option value="{{ $department['id'] }}">{{ $department['label'] }}</option>
                                    @endforeach
                                </select>
                                @error('department_id')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Kapasitas</label>
                                <input type="text" wire:model="newCapacity" class="bauhaus-input" placeholder="Opsional">
                                @error('kapasitas')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Merk</label>
                                <input type="text" wire:model="newBrand" class="bauhaus-input" placeholder="Opsional">
                                @error('merk')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Tahun Pemakaian</label>
                                <input type="number" wire:model="newYear" min="1900" max="{{ now()->format('Y') }}" class="bauhaus-input" placeholder="YYYY">
                                @error('tahun_pemakaian')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Status</label>
                                <select wire:model="newStatus" class="bauhaus-input" required>
                                    @foreach ($this->statusOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('status')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Last Maintenance Date</label>
                                <input type="date" wire:model="newLastMaintenanceDate" class="bauhaus-input">
                                @error('last_maintenance_date')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Keterangan</label>
                            <textarea wire:model="newDescription" rows="3" class="bauhaus-input resize-y" placeholder="Opsional"></textarea>
                            @error('keterangan')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                        </div>
                    @endif

                    <div class="flex flex-wrap justify-end gap-2">
                        <button type="button" wire:click="cancelCreate" class="bauhaus-btn bg-white px-4 py-2 text-xs dark:bg-slate-900">Batal</button>
                        <button type="button" wire:click="createOption" class="bauhaus-btn bg-bauhaus-blue px-4 py-2 text-xs text-white hover:bg-bauhaus-blue-dark" wire:loading.attr="disabled">
                            Simpan & Pilih
                        </button>
                    </div>
                </div>
            @endif
        </div>
    @endif

    @if ($this->locationLocked)
        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Lengkapi filter lokasi (gedung → jurusan → ruangan) terlebih dahulu untuk memilih alat.</p>
    @elseif ($required && ! $selectedId && trim($search) !== '')
        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Pilih data dari daftar atau tambahkan data baru.</p>
    @endif
</div>
