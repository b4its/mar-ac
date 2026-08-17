@php
    $field = $field ?? 'foto_extra';
    $captionField = $captionField ?? 'caption_extra';
    $bagian = $bagian ?? 1;
    $suffix = $bagian === 1 ? '' : ' (Bagian 2)';
@endphp
<div class="md:col-span-2 border-t border-bauhaus-black pt-6">
    <p class="mb-4 font-display text-sm uppercase tracking-widest">
        Lampiran Tambahan
        <span class="text-xs normal-case tracking-normal text-bauhaus-ink">(opsional — maksimal satu gambar + caption)</span>
    </p>
    <div class="grid gap-4 sm:grid-cols-2">
        <div class="border border-dashed border-bauhaus-black bg-bauhaus-paper p-4" data-photo-preview-wrap>
            <label for="{{ $field }}" class="flex cursor-pointer flex-col items-center justify-center gap-2 border border-bauhaus-black bg-bauhaus-paper px-4 py-6 text-center hover:bg-bauhaus-paper-dark">
                <span class="sr-only">Pilih gambar{{ $suffix }}</span>
                <input type="file" id="{{ $field }}" name="{{ $field }}" accept="image/*" data-photo-preview class="sr-only">
                <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z" />
                    <circle cx="12" cy="13" r="4" />
                </svg>
                <span class="font-display text-xs uppercase tracking-widest" data-photo-placeholder>Pilih gambar…</span>
            </label>
            <img data-photo-image alt="Lampiran tambahan{{ $suffix }}" class="mt-3 hidden h-40 w-full border border-bauhaus-black object-cover">
            <input
                type="text"
                id="{{ $captionField }}"
                name="{{ $captionField }}"
                value="{{ old($captionField) }}"
                maxlength="255"
                placeholder="Keterangan gambar / caption{{ $suffix }}"
                class="bauhaus-input mt-3 text-sm"
            >
            @error($field)<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
            @error($captionField)<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>
</div>