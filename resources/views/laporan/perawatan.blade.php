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

            {{-- Bagian 1 --}}
            <div class="space-y-6">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white border-b border-slate-200 pb-2 dark:border-slate-700">Bagian 1</h2>

                <div class="grid gap-6 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <livewire:searchable-select type="asset" name="asset_id" label="Alat / Mesin" placeholder="Cari nama alat..." :selected="old('asset_id') ? (int) old('asset_id') : null" required wire:key="maintenance-asset-select-section1" />
                        @error('asset_id')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <livewire:searchable-select type="vendor" name="vendor_id" label="Vendor / Pelaksana (opsional)" placeholder="Cari nama vendor..." :selected="old('vendor_id') ? (int) old('vendor_id') : null" wire:key="maintenance-vendor-select-section1" />
                    </div>
                    <div class="md:col-span-2">
                        <label for="jenis_pekerjaan_1" class="mb-2 block font-display text-sm uppercase tracking-widest">Jenis Pekerjaan</label>
                        <input type="text" id="jenis_pekerjaan_1" name="jenis_pekerjaan" value="{{ old('jenis_pekerjaan') }}" required placeholder="Contoh: Cleaning AC Split" class="bauhaus-input" />
                        @error('jenis_pekerjaan')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="uraian_pekerjaan_1" class="mb-2 block font-display text-sm uppercase tracking-widest">Uraian Pekerjaan</label>
                        <textarea id="uraian_pekerjaan_1" name="uraian_pekerjaan" rows="3" placeholder="Detail pekerjaan yang dilakukan..." class="bauhaus-input resize-y">{{ old('uraian_pekerjaan') }}</textarea>
                    </div>
                    <div>
                        <label for="tanggal_pelaksanaan_1" class="mb-2 block font-display text-sm uppercase tracking-widest">Tanggal Pelaksanaan</label>
                        <input type="date" id="tanggal_pelaksanaan_1" name="tanggal_pelaksanaan" value="{{ old('tanggal_pelaksanaan', now()->toDateString()) }}" class="bauhaus-input" />
                    </div>
                    <div class="hidden">
                        <label for="biaya_jasa_1" class="mb-2 block font-display text-sm uppercase tracking-widest">Biaya Jasa (Admin Only)</label>
                        <input type="text" id="biaya_jasa_1" name="biaya_jasa" inputmode="numeric" value="" class="bauhaus-input" data-money-input readonly />
                        @error('biaya_jasa')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Upload Foto Section 1 --}}
                <div class="border-t border-bauhaus-black pt-6">
                    <p class="mb-4 font-display text-sm uppercase tracking-widest">Foto Dokumentasi</p>
                    <div class="grid gap-6 md:grid-cols-2">
                        @php
                            $sections = [
                                ['key' => 'section1_indoor', 'name' => 'foto_indoor', 'caption' => 'caption_indoor', 'id' => 'foto_indoor_1', 'preview_class' => 'preview_section1_indoor', 'img_id' => 'img_section1_indoor', 'title' => 'Pencucian AC Indoor *', 'placeholder' => 'Kondisi sebelum cleaning...'],
                                ['key' => 'section1_outdoor', 'name' => 'foto_outdoor', 'caption' => 'caption_outdoor', 'id' => 'foto_outdoor_1', 'preview_class' => 'preview_section1_outdoor', 'img_id' => 'img_section1_outdoor', 'title' => 'Pencucian AC Outdoor *', 'placeholder' => 'Outdoor unit bersih...'],
                                ['key' => 'section1_kartu', 'name' => 'foto_kartu', 'caption' => 'caption_kartu', 'id' => 'foto_kartu_1', 'preview_class' => 'preview_section1_kartu', 'img_id' => 'img_section1_kartu', 'title' => 'Kartu Perawatan *', 'placeholder' => 'Kartu maintenance terisi...'],
                                ['key' => 'section1_extra', 'name' => 'foto_extra', 'caption' => 'caption_extra', 'id' => 'foto_extra_1', 'preview_class' => 'preview_section1_extra', 'img_id' => 'img_section1_extra', 'title' => 'Lampiran Tambahan', 'placeholder' => 'Lampiran tambahan'],
                            ];
                        @endphp

                        @foreach($sections as $section)
                            <div class="upload-container border-2 border-dashed border-slate-300 rounded-xl p-4 bg-slate-50 dark:bg-slate-900 dark:border-slate-700 space-y-3" data-upload-index="{{ $section['key'] }}">
                                <label for="{{ $section['id'] }}" class="block font-display text-xs uppercase tracking-widest mb-2">{{ $section['title'] }}</label>
                                
                                <input type="file" 
                                       id="{{ $section['id'] }}" 
                                       name="{{ $section['name'] }}" 
                                       accept="image/*" 
                                       {{ in_array($section['key'], ['section1_indoor','section1_outdoor','section1_kartu']) ? 'required' : '' }} 
                                       class="hidden" 
                                       onchange="handlePhotoUpload(this, '{{ $section['key'] }},{{ $section['preview_class'] }},{{ $section['img_id'] }},remove_{{ $section['key'] }}}')" 
                                       data-photo-input>
                                
                                <!-- Clickable Upload Area with better pointer events -->
                                <div id="{{ $section['preview_class'] }}" 
                                     class="upload-placeholder clickable-area border-2 border-dashed border-slate-200 rounded-lg p-6 text-center min-h-[150px] flex flex-col items-center justify-center transition-all cursor-pointer hover:border-bauhaus-blue hover:bg-blue-50 dark:hover:bg-blue-900/20"
                                     onclick="triggerFileClick('{{ $section['id'] }}')">
                                    <svg class="h-12 w-12 text-slate-400 group-hover:text-bauhaus-blue transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <p class="mt-2 text-sm font-medium text-slate-500 dark:text-slate-400 group-hover:text-bauhaus-blue transition-colors">Klik untuk upload foto</p>
                                </div>
                                
                                <!-- Image Preview -->
                                <img id="{{ $section['img_id'] }}" src="" alt="" class="hidden mx-auto h-40 object-cover rounded-lg border border-slate-200 dark:border-slate-700 shadow-md" />
                                
                                <!-- Caption Input -->
                                <input type="text" 
                                       name="{{ $section['caption'] }}" 
                                       placeholder="Caption (misal: {{ $section['placeholder'] }})" 
                                       class="bauhaus-input mt-2 text-sm w-full" 
                                       onblur="updateImageAlt(this, '{{ $section['img_id'] }}')"
                                       data-caption-input>
                                
                                <!-- Remove Button -->
                                <button type="button" 
                                        id="remove_{{ $section['key'] }}" 
                                        onclick="removePhoto('{{ $section['key'] }}', '{{ $section['preview_class'] }}')" 
                                        class="hidden mt-2 w-full bg-red-50 text-red-700 px-3 py-2 text-xs hover:bg-red-100 dark:bg-red-950 dark:text-red-300 dark:hover:bg-red-900 rounded-lg transition-colors flex items-center justify-center gap-2"
                                        data-remove-btn>
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Hapus Foto
                                </button>
                            </div>
                            @error($section['name'])<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Toggle Section 2 --}}
            <div class="mt-8 border-t border-bauhaus-black pt-6">
                <button type="button" id="toggle_section_2" class="bauhaus-btn bg-bauhaus-yellow px-6 py-3 text-xs w-full">+ Tampilkan Bagian 2 (Opsional)</button>
            </div>

            {{-- Section 2 --}}
            <div id="section_2" class="hidden space-y-6 mt-8">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white border-b border-slate-200 pb-2 dark:border-slate-700">Bagian 2 (Alat Kedua)</h2>
                <div class="grid gap-6 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <livewire:searchable-select type="asset" name="asset_id_2" label="Alat / Mesin" placeholder="Cari nama alat..." :selected="old('asset_id_2') ? (int) old('asset_id_2') : null" wire:key="maintenance-asset-select-section2" />
                        @error('asset_id_2')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="jenis_pekerjaan_2" class="mb-2 block font-display text-sm uppercase tracking-widest">Jenis Pekerjaan</label>
                        <input type="text" id="jenis_pekerjaan_2" name="jenis_pekerjaan_2" value="{{ old('jenis_pekerjaan_2') }}" required placeholder="Contoh: Servis kondensor" class="bauhaus-input" />
                        @error('jenis_pekerjaan_2')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="uraian_pekerjaan_2" class="mb-2 block font-display text-sm uppercase tracking-widest">Uraian Pekerjaan</label>
                        <textarea id="uraian_pekerjaan_2" name="uraian_pekerjaan_2" rows="3" placeholder="Detail pekerjaan..." class="bauhaus-input resize-y">{{ old('uraian_pekerjaan_2') }}</textarea>
                    </div>
                    <div>
                        <label for="tanggal_pelaksanaan_2" class="mb-2 block font-display text-sm uppercase tracking-widest">Tanggal Pelaksanaan</label>
                        <input type="date" id="tanggal_pelaksanaan_2" name="tanggal_pelaksanaan_2" value="{{ old('tanggal_pelaksanaan_2', now()->toDateString()) }}" class="bauhaus-input" />
                    </div>
                    <div class="hidden">
                        <label for="biaya_jasa_2" class="mb-2 block font-display text-sm uppercase tracking-widest">Biaya Jasa (Admin Only)</label>
                        <input type="text" id="biaya_jasa_2" name="biaya_jasa_2" inputmode="numeric" value="" class="bauhaus-input" data-money-input readonly />
                        @error('biaya_jasa_2')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Upload Section 2 --}}
                <div class="border-t border-bauhaus-black pt-6">
                    <p class="mb-4 font-display text-sm uppercase tracking-widest">Foto Dokumentasi Bagian 2</p>
                    <div class="grid gap-6 md:grid-cols-2">
                        @php
                            $sections2 = [
                                ['key' => 'section2_indoor', 'name' => 'foto_indoor_2', 'caption' => 'caption_indoor_2', 'id' => 'foto_indoor_2', 'preview_class' => 'preview_section2_indoor', 'img_id' => 'img_section2_indoor', 'title' => 'Pencucian AC Indoor *', 'placeholder' => 'Kondisi sebelum cleaning...', 'required' => 'required_with="asset_id_2"'],
                                ['key' => 'section2_outdoor', 'name' => 'foto_outdoor_2', 'caption' => 'caption_outdoor_2', 'id' => 'foto_outdoor_2', 'preview_class' => 'preview_section2_outdoor', 'img_id' => 'img_section2_outdoor', 'title' => 'Pencucian AC Outdoor *', 'placeholder' => 'Outdoor unit bersih...', 'required' => 'required_with="asset_id_2"'],
                                ['key' => 'section2_kartu', 'name' => 'foto_kartu_2', 'caption' => 'caption_kartu_2', 'id' => 'foto_kartu_2', 'preview_class' => 'preview_section2_kartu', 'img_id' => 'img_section2_kartu', 'title' => 'Kartu Perawatan *', 'placeholder' => 'Kartu maintenance terisi...', 'required' => 'required_with="asset_id_2"'],
                                ['key' => 'section2_extra', 'name' => 'foto_extra_2', 'caption' => 'caption_extra_2', 'id' => 'foto_extra_2', 'preview_class' => 'preview_section2_extra', 'img_id' => 'img_section2_extra', 'title' => 'Lampiran Tambahan', 'placeholder' => 'Lampiran tambahan', 'required' => ''],
                            ];
                        @endphp

                        @foreach($sections2 as $section)
                            <div class="upload-container border-2 border-dashed border-slate-300 rounded-xl p-4 bg-slate-50 dark:bg-slate-900 dark:border-slate-700 space-y-3" data-upload-index="{{ $section['key'] }}">
                                <label for="{{ $section['id'] }}" class="block font-display text-xs uppercase tracking-widest mb-2">{{ $section['title'] }}</label>
                                
                                <input type="file" 
                                       id="{{ $section['id'] }}" 
                                       name="{{ $section['name'] }}" 
                                       accept="image/*" 
                                       {{ !empty($section['required']) ? $section['required'] : '' }} 
                                       class="hidden" 
                                       onchange="handlePhotoUpload(this, '{{ $section['key'] }},{{ $section['preview_class'] }},{{ $section['img_id'] }},remove_{{ $section['key'] }}}')" 
                                       data-photo-input>
                                
                                <div id="{{ $section['preview_class'] }}" 
                                     class="upload-placeholder clickable-area border-2 border-dashed border-slate-200 rounded-lg p-6 text-center min-h-[150px] flex flex-col items-center justify-center transition-all cursor-pointer hover:border-bauhaus-blue hover:bg-blue-50 dark:hover:bg-blue-900/20"
                                     onclick="triggerFileClick('{{ $section['id'] }}')">
                                    <svg class="h-12 w-12 text-slate-400 group-hover:text-bauhaus-blue transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <p class="mt-2 text-sm font-medium text-slate-500 dark:text-slate-400 group-hover:text-bauhaus-blue transition-colors">Klik untuk upload foto</p>
                                </div>
                                
                                <img id="{{ $section['img_id'] }}" src="" alt="" class="hidden mx-auto h-40 object-cover rounded-lg border border-slate-200 dark:border-slate-700 shadow-md" />
                                
                                <input type="text" name="{{ $section['caption'] }}" placeholder="Caption (jika ada)" class="bauhaus-input mt-2 text-sm w-full" onblur="updateImageAlt(this, '{{ $section['img_id'] }}')" data-caption-input>
                                
                                <button type="button" id="remove_{{ $section['key'] }}" onclick="removePhoto('{{ $section['key'] }}', '{{ $section['preview_class'] }}')" class="hidden mt-2 w-full bg-red-50 text-red-700 px-3 py-2 text-xs hover:bg-red-100 dark:bg-red-950 dark:text-red-300 dark:hover:bg-red-900 rounded-lg transition-colors flex items-center justify-center gap-2" data-remove-btn>
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Hapus Foto
                                </button>
                            </div>
                            @error($section['name'])<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="mt-8 flex flex-wrap items-center justify-between gap-4 border-t border-bauhaus-black pt-6">
                <a href="{{ route('welcome') }}" class="bauhaus-btn bg-bauhaus-paper px-5 py-2.5 text-xs">← Kembali</a>
                <button type="submit" class="bauhaus-btn bg-bauhaus-blue px-8 py-3 text-white hover:bg-bauhaus-blue-dark">Kirim Laporan</button>
            </div>
        </form>
    </div>

    <script>
        // Trigger file input click ONLY when clicking placeholder, not other elements
        function triggerFileClick(inputId) {
            console.log('🖱️ Click triggered on upload area:', inputId);
            const input = document.getElementById(inputId);
            if (input) {
                input.click();
                return false;
            }
        }

        // Handle photo upload with preview
        function handlePhotoUpload(input, elementsString) {
            console.log('📁 File selected');
            
            const file = input.files && input.files[0];
            if (!file) {
                console.warn('⚠️ No file selected');
                return;
            }
            
            // Validate file type
            if (!file.type.match(/image\/(png|jpg|jpeg|webp)/i)) {
                alert('❌ File harus berupa gambar PNG, JPG, atau WEBP!');
                input.value = '';
                return;
            }
            
            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                const fileSizeMB = (file.size / 1024 / 1024).toFixed(2);
                alert('❌ Ukuran file terlalu besar! Maksimal 5MB (' + fileSizeMB + 'MB detected)');
                input.value = '';
                return;
            }
            
            // Parse elements string safely
            const elementArray = elementsString.split(',').map(s => s.trim()).filter(s => s);
            if (elementArray.length < 3) {
                console.error('❌ Invalid element IDs format');
                return;
            }
            
            const previewClass = elementArray[0];
            const imgId = elementArray[1];
            const removeBtnId = elementArray[2].replace('}', '');
            
            console.log('🔍 Elements found:', { previewClass, imgId, removeBtnId });
            
            const reader = new FileReader();
            
            reader.onload = function(e) {
                try {
                    const previewDiv = document.getElementById(previewClass);
                    const imgElement = document.getElementById(imgId);
                    const removeBtn = document.getElementById(removeBtnId);
                    
                    console.log('✅ Element lookup:', {
                        previewDiv: !!previewDiv,
                        imgElement: !!imgElement,
                        removeBtn: !!removeBtn
                    });
                    
                    if (imgElement && e.target.result) {
                        // Revoke previous blob URL to prevent memory leak
                        if (imgElement.src && imgElement.src.startsWith('blob:')) {
                            URL.revokeObjectURL(imgElement.src);
                        }
                        
                        imgElement.src = e.target.result;
                        imgElement.classList.remove('hidden');
                        
                        if (previewDiv) previewDiv.classList.add('hidden');
                        if (removeBtn) removeBtn.classList.remove('hidden');
                        
                        console.log('✅ Photo uploaded successfully!');
                    } else {
                        throw new Error('Elements not found or no image data');
                    }
                } catch (error) {
                    console.error('❌ Error processing image:', error);
                    alert('❌ Error loading image: ' + error.message);
                    input.value = '';
                }
            };
            
            reader.onerror = function() {
                console.error('❌ FileReader error');
                alert('❌ Error reading file. Please try again.');
                input.value = '';
            };
            
            reader.readAsDataURL(file);
        }
        
        // Update image alt text when caption changes
        function updateImageAlt(input, imgId) {
            const wrap = input.closest('.upload-container');
            if (!wrap) return;
            
            const img = document.getElementById(imgId);
            if (!img) return;
            
            const caption = input.value.trim();
            img.alt = caption || 'Photo';
            console.log('✓ Caption updated:', caption);
        }
        
        // Remove uploaded photo
        function removePhoto(prefix, previewClass) {
            console.log('🗑️ Removing photo:', prefix);
            
            const wrap = document.querySelector(`[data-upload-index="${prefix}"]`);
            if (!wrap) {
                console.error('❌ Upload container not found for:', prefix);
                return;
            }
            
            const input = wrap.querySelector('input[type="file"]');
            const previewDiv = document.getElementById(previewClass);
            const img = wrap.querySelector('img[id^="img_"]');
            const captionInput = wrap.querySelector('[data-caption-input]');
            const removeBtn = document.getElementById('remove_' + prefix);
            
            console.log('🔍 Elements:', {
                input: !!input,
                previewDiv: !!previewDiv,
                img: !!img,
                captionInput: !!captionInput,
                removeBtn: !!removeBtn
            });
            
            if (input) input.value = '';
            if (previewDiv) previewDiv.classList.remove('hidden');
            
            if (img && img.src.startsWith('blob:')) {
                URL.revokeObjectURL(img.src);
                img.src = '';
            }
            img?.classList.add('hidden');
            img?.removeAttribute('alt');
            
            if (captionInput) captionInput.value = '';
            if (removeBtn) removeBtn.classList.add('hidden');
            
            console.log('✅ Photo removal complete');
        }
        
        // Toggle section 2 visibility
        document.getElementById('toggle_section_2')?.addEventListener('click', function() {
            const section2 = document.getElementById('section_2');
            if (section2.classList.contains('hidden')) {
                section2.classList.remove('hidden');
                this.textContent = '− Sembunyikan Bagian 2';
                setTimeout(() => section2.scrollIntoView({ behavior: 'smooth', block: 'start' }), 100);
            } else {
                section2.classList.add('hidden');
                this.textContent = '+ Tampilkan Bagian 2 (Opsional)';
            }
        });
        
        // Initialize all photo upload areas on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Maintenance form initialized');
            
            // Add drag and drop support
            document.querySelectorAll('.upload-container').forEach(container => {
                container.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    this.style.borderColor = '#2563eb';
                    this.style.backgroundColor = '#eff6ff';
                });
                
                container.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    this.style.borderColor = '';
                    this.style.backgroundColor = '';
                });
                
                container.addEventListener('drop', function(e) {
                    e.preventDefault();
                    this.style.borderColor = '';
                    this.style.backgroundColor = '';
                    
                    const input = this.querySelector('input[type="file"]');
                    const files = e.dataTransfer.files;
                    
                    if (files.length > 0) {
                        const dt = new DataTransfer();
                        dt.items.add(files[0]);
                        input.files = dt.files;
                        
                        const event = new Event('change', { bubbles: true });
                        input.dispatchEvent(event);
                    }
                });
            });
        });
    </script>
</x-bauhaus.layout>
