<x-bauhaus.layout :title="$title">
    <section class="w-full max-w-6xl space-y-5">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xl shadow-blue-100/50 dark:border-slate-800 dark:bg-slate-900 dark:shadow-black/20">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="font-display text-xs uppercase tracking-[0.35em] text-bauhaus-blue">Preview Formulir</p>
                    <h1 class="mt-2 font-display text-2xl text-slate-950 dark:text-white">{{ $title }}</h1>
                    <p class="mt-1 text-sm font-semibold text-slate-500 dark:text-slate-400">{{ $nomor }}</p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="history.back()" class="bauhaus-btn bg-white px-5 py-2.5 text-xs text-slate-700 dark:bg-slate-800 dark:text-slate-200">Kembali</button>
                    <button type="button" data-print-pdf class="bauhaus-btn bg-bauhaus-blue px-5 py-2.5 text-xs text-white">Print</button>
                    <a href="{{ $downloadUrl }}" class="bauhaus-btn bg-bauhaus-black px-5 py-2.5 text-xs text-white">Download PDF</a>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl shadow-blue-100/40 dark:border-slate-800 dark:bg-slate-900 dark:shadow-black/20">
            <iframe src="{{ $fileUrl }}" title="Preview PDF {{ $nomor }}" class="h-[78vh] w-full bg-white"></iframe>
        </div>
    </section>

    <script>
        document.querySelector('[data-print-pdf]')?.addEventListener('click', function () {
            var frame = document.querySelector('iframe');

            if (!frame) return;

            frame.contentWindow.focus();
            frame.contentWindow.print();
        });
    </script>
</x-bauhaus.layout>
