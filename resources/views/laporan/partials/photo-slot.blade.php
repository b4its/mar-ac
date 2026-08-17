@php
    $field = $field ?? 'foto_indoor';
    $label = $label ?? 'Pilih gambar';
    $caption = $caption ?? $label;
    $bagian = $bagian ?? 1;
    $required = $required ?? false;
    $suffix = $bagian === 1 ? '' : ' (Bagian 2)';
@endphp
<div>
    <p class="mb-2 font-display text-xs uppercase tracking-widest">{{ $caption }}{{ $suffix }}</p>
    <label for="{{ $field }}" class="block cursor-pointer border border-bauhaus-black bg-bauhaus-paper p-3 hover:bg-bauhaus-paper-dark" data-photo-preview-wrap>
        <span class="sr-only">Pilih gambar</span>
        <input type="file" id="{{ $field }}" name="{{ $field }}" accept="image/*" @if ($required) required @endif data-photo-preview class="sr-only">
        <span class="flex flex-col items-center justify-center gap-2 py-4 text-center">
            <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z" />
                <circle cx="12" cy="13" r="4" />
            </svg>
            <span class="font-display text-xs uppercase tracking-widest" data-photo-placeholder>Pilih gambar…</span>
        </span>
        <img data-photo-image alt="{{ $caption }}{{ $suffix }}" class="hidden h-40 w-full border border-bauhaus-black object-cover">
    </label>
    <span class="mt-2 block text-center text-xs font-semibold text-slate-600 dark:text-slate-300">{{ $caption }}{{ $suffix }}</span>
    @error($field)<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
</div>
