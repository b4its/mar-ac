@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="Sistem Informasi Pemeliharaan Aset - Politeknik Negeri Samarinda">
        <meta name="theme-color" content="#2563eb">

        <title>{{ $title ? $title.' — ' : '' }}{{ config('app.name') }}</title>

        <script>
            (function () {
                var stored = null;
                try {
                    stored = localStorage.getItem('theme');
                } catch (e) {}
                var dark = stored === 'dark' || ((stored === 'system' || stored === null) && window.matchMedia('(prefers-color-scheme: dark)').matches);
                if (dark) {
                    document.documentElement.classList.add('dark');
                }
            })();
        </script>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="relative flex min-h-screen flex-col overflow-x-hidden bg-slate-50 dark:bg-slate-950">
        <div class="bauhaus-grid absolute inset-0 -z-10"></div>

        <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/90 px-4 py-4 shadow-sm backdrop-blur lg:px-10 dark:border-slate-800 dark:bg-slate-950/90" role="banner">
            <div class="mx-auto flex w-full max-w-6xl flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <a href="{{ auth()->check() ? route('welcome') : route('login') }}" class="bauhaus-title group flex min-w-0 items-center gap-3 text-xl text-bauhaus-blue lg:text-2xl" aria-label="UPA.PP - UPA.PP System">
                <span class="relative inline-flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-bauhaus-blue shadow-lg shadow-blue-500/20 ring-2 ring-white/20 dark:ring-white/10">
                    <img src="{{ asset('images/logoPolnes.png') }}" alt="Logo Polnes" class="h-8 w-8 object-contain">
                </span>
                <span class="truncate font-semibold">UPA.PP</span>
            </a>

            <nav class="flex flex-wrap items-center gap-2 md:justify-end">
                @auth
                    <a href="{{ route('welcome') }}" class="bauhaus-btn flex-1 justify-center bg-white text-bauhaus-blue hover:bg-blue-50 sm:flex-none dark:bg-slate-900 dark:hover:bg-slate-800">Beranda</a>
                    @if (auth()->user()->hasRole('admin'))
                        <a href="{{ route('filament.admin.pages.dashboard') }}" class="bauhaus-btn flex-1 justify-center bg-bauhaus-blue text-white hover:bg-blue-700 sm:flex-none">Panel Admin</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" id="logout-form" class="hidden">
                        @csrf
                    </form>
                    <button type="submit" form="logout-form" class="bauhaus-btn bg-white text-slate-700 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">Keluar</button>
                    </form>
                @endauth
                <livewire:theme-switcher />
            </nav>
            </div>
        </header>

        @php
            $toastMessages = collect([
                session('success') ? ['type' => 'success', 'title' => 'Berhasil', 'message' => session('success')] : null,
                session('info') ? ['type' => 'info', 'title' => 'Informasi', 'message' => session('info')] : null,
                session('status') ? ['type' => 'success', 'title' => 'Status', 'message' => session('status')] : null,
                session('error') ? ['type' => 'error', 'title' => 'Gagal', 'message' => session('error')] : null,
                $errors->any() ? ['type' => 'error', 'title' => 'Periksa formulir', 'message' => 'Ada isian yang perlu diperbaiki sebelum dikirim.'] : null,
            ])->filter();
        @endphp

        @if ($toastMessages->isNotEmpty())
            <div class="fixed right-4 top-24 z-50 flex w-[calc(100%-2rem)] max-w-sm flex-col gap-3 sm:right-6" data-toast-stack>
                @foreach ($toastMessages as $toast)
                    <div class="rounded-xl border bg-white p-4 shadow-xl shadow-slate-200/70 dark:bg-slate-900 dark:shadow-black/30 {{ $toast['type'] === 'error' ? 'border-red-200 dark:border-red-900' : ($toast['type'] === 'info' ? 'border-blue-200 dark:border-blue-900' : 'border-emerald-200 dark:border-emerald-900') }}" data-toast>
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-bold {{ $toast['type'] === 'error' ? 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300' : ($toast['type'] === 'info' ? 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300') }}">
                                {{ $toast['type'] === 'error' ? '!' : '✓' }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $toast['title'] }}</p>
                                <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">{{ $toast['message'] }}</p>
                            </div>
                            <button type="button" class="text-slate-400 transition hover:text-slate-700 dark:hover:text-slate-200" data-toast-close aria-label="Tutup notifikasi">×</button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <main class="flex w-full flex-1 flex-col items-center px-4 py-8 sm:px-6 lg:px-10 lg:py-12">
            {{ $slot }}
        </main>

        <footer class="border-t border-slate-200 bg-white/70 px-4 py-5 text-sm text-slate-500 lg:px-10 dark:border-slate-800 dark:bg-slate-950/70 dark:text-slate-400">
            <div class="mx-auto flex w-full max-w-6xl flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <span>Politeknik Negeri Samarinda</span>
                <span>&copy; {{ date('Y') }} UPA.PP</span>
            </div>
        </footer>

        <script>
            (function () {
                var EMPTY_TEXT = 'Pilih gambar…';

                function setPreview(input, wrap) {
                    var img = wrap.querySelector('[data-photo-image]');
                    var placeholder = wrap.querySelector('[data-photo-placeholder]');
                    var file = input.files && input.files[0];
                    var url = img.dataset.url;

                    if (url) {
                        URL.revokeObjectURL(url);
                    }
                    delete img.dataset.url;

                    if (!file) {
                        img.classList.add('hidden');
                        img.removeAttribute('src');
                        if (placeholder) placeholder.textContent = EMPTY_TEXT;
                        return;
                    }

                    var objectUrl = URL.createObjectURL(file);
                    img.dataset.url = objectUrl;
                    img.src = objectUrl;
                    img.classList.remove('hidden');
                    if (placeholder) placeholder.textContent = file.name;
                }

                document.addEventListener('change', function (event) {
                    var input = event.target.closest('[data-photo-preview]');
                    if (!input) {
                        return;
                    }
                    var wrap = input.closest('[data-photo-preview-wrap]') || input.parentElement;
                    setPreview(input, wrap);
                });

                document.querySelectorAll('[data-photo-dynamic]').forEach(function (container) {
                    var template = container.querySelector('template[data-photo-template]');
                    var grid = container.querySelector('[data-photo-grid]');
                    var addButton = container.querySelector('[data-photo-add]');
                    var max = parseInt(container.dataset.photoMax || '10', 10);

                    function sync() {
                        if (addButton) {
                            addButton.disabled = grid.children.length >= max;
                        }
                    }

                    function addSlot() {
                        var node = template.content.firstElementChild.cloneNode(true);
                        var wrap = node;
                        var input = wrap.querySelector('[data-photo-input]');

                        input.addEventListener('change', function () {
                            setPreview(input, wrap);
                        });

                        node.querySelector('[data-photo-remove]').addEventListener('click', function () {
                            var img = wrap.querySelector('[data-photo-image]');
                            if (img && img.dataset.url) {
                                URL.revokeObjectURL(img.dataset.url);
                            }
                            node.remove();
                            if (grid.children.length === 0) {
                                addSlot();
                            }
                            sync();
                        });

                        grid.appendChild(node);
                        sync();
                    }

                    if (addButton) {
                        addButton.addEventListener('click', addSlot);
                    }

                    var initial = parseInt(container.dataset.photoCount || '1', 10);
                    for (var i = 0; i < initial; i++) {
                        addSlot();
                    }
                });

                function bindToast(toast) {
                    var close = toast.querySelector('[data-toast-close]');
                    var hide = function () {
                        toast.style.opacity = '0';
                        toast.style.transform = 'translateY(-8px)';
                        setTimeout(function () { toast.remove(); }, 180);
                    };

                    if (close) {
                        close.addEventListener('click', hide);
                    }

                    setTimeout(hide, 5500);
                }

                function ensureToastStack() {
                    var stack = document.querySelector('[data-toast-stack]');

                    if (!stack) {
                        stack = document.createElement('div');
                        stack.dataset.toastStack = '';
                        stack.className = 'fixed right-4 top-24 z-50 flex w-[calc(100%-2rem)] max-w-sm flex-col gap-3 sm:right-6';
                        document.body.appendChild(stack);
                    }

                    return stack;
                }

                window.addEventListener('toast', function (event) {
                    var detail = event.detail || {};
                    var type = detail.type || 'success';
                    var title = detail.title || 'Berhasil';
                    var message = detail.message || '';
                    var isError = type === 'error';
                    var iconClass = isError ? 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300';
                    var borderClass = isError ? 'border-red-200 dark:border-red-900' : 'border-emerald-200 dark:border-emerald-900';

                    var toast = document.createElement('div');
                    toast.dataset.toast = '';
                    toast.className = 'rounded-xl border bg-white p-4 shadow-xl shadow-slate-200/70 transition dark:bg-slate-900 dark:shadow-black/30 ' + borderClass;
                    toast.innerHTML = '<div class="flex items-start gap-3">'
                        + '<span class="mt-0.5 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-bold ' + iconClass + '">' + (isError ? '!' : '✓') + '</span>'
                        + '<div class="min-w-0 flex-1"><p class="text-sm font-semibold text-slate-900 dark:text-slate-100"></p><p class="mt-1 text-sm text-slate-600 dark:text-slate-300"></p></div>'
                        + '<button type="button" class="text-slate-400 transition hover:text-slate-700 dark:hover:text-slate-200" data-toast-close aria-label="Tutup notifikasi">×</button>'
                        + '</div>';
                    toast.querySelector('p:first-child').textContent = title;
                    toast.querySelector('p:last-child').textContent = message;

                    ensureToastStack().appendChild(toast);
                    bindToast(toast);
                });

                document.querySelectorAll('[data-toast]').forEach(function (toast) {
                    bindToast(toast);
                });

                function onlyDigits(value) {
                    return String(value || '').replace(/\D/g, '');
                }

                function formatMoney(value) {
                    var digits = onlyDigits(value).replace(/^0+(?=\d)/, '');

                    if (!digits) {
                        return '0';
                    }

                    return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                }

                document.querySelectorAll('[data-money-input]').forEach(function (input) {
                    input.value = formatMoney(input.value);

                    input.addEventListener('input', function () {
                        input.value = formatMoney(input.value);
                    });

                    input.form?.addEventListener('submit', function () {
                        input.value = onlyDigits(input.value) || '0';
                    });
                });
            })();
        </script>
    </body>
</html>
