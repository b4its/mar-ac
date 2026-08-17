<x-bauhaus.layout title="Masuk">
    <div class="w-full max-w-md">
        <div class="bauhaus-card relative p-8 lg:p-10">
            <x-bauhaus.shape type="square" color="blue" class="absolute -right-8 -top-8 h-16 w-16" />
            <x-bauhaus.shape type="circle" color="red" class="absolute -bottom-8 -left-8 h-16 w-16" />

            <div class="mb-8 text-center">
                <img src="{{ asset('images/logoPolnes.png') }}" alt="Logo Polnes" class="mx-auto mb-4 h-12 object-contain">
                <p class="mb-2 text-xs font-bold uppercase tracking-[0.3em] text-bauhaus-blue">Politeknik Negeri Samarinda</p>
                <h1 class="bauhaus-title text-4xl lg:text-5xl">UPA.PP</h1>
                <p class="mt-2 text-sm uppercase tracking-widest text-bauhaus-ink">
                    Unit Pemeliharaan &amp; Perbaikan
                </p>
            </div>

            @if ($errors->any())
                <div class="mb-6 border border-bauhaus-red bg-bauhaus-paper p-4">
                    <ul class="space-y-1 text-sm font-semibold text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="email" class="mb-2 block font-display text-sm uppercase tracking-widest">Email</label>
                    <input
                        id="email"
                        type="email"
                        name="email" autocomplete="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="email"
                        placeholder="nama@email.com"
                        class="bauhaus-input"
                    >
                </div>

                <div x-data="{ show: false }">
                    <label for="password" class="mb-2 block font-display text-sm uppercase tracking-widest">Kata Sandi</label>
                    <div class="relative">
                        <input
                            id="password"
                            :type="show ? 'text' : 'password'"
                            name="password" autocomplete="current-password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="bauhaus-input pr-12"
                        >
                        <button
                            type="button"
                            @click="show = !show"
                            class="absolute right-1.5 top-1/2 -translate-y-1/2 border border-bauhaus-black bg-bauhaus-paper p-1.5 text-bauhaus-ink hover:bg-bauhaus-paper-dark dark:text-slate-300"
                            :aria-label="show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                            :title="show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                        >
                            <svg x-show="!show" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" data-eye-show>
                                <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg x-show="show" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" data-eye-hide>
                                <path d="M10.733 5.076a10.744 10.744 0 0 1 11.205 6.575 1 1 0 0 1 0 .696 10.747 10.747 0 0 1-1.444 2.49"/>
                                <path d="M14.084 14.158a3 3 0 0 1-4.242-4.242"/>
                                <path d="M17.479 17.499a10.75 10.75 0 0 1-15.417-5.151 1 1 0 0 1 0-.696 10.75 10.75 0 0 1 4.446-5.143"/>
                                <path d="m2 2 20 20"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <label class="flex cursor-pointer items-center gap-3">
                    <input type="checkbox" name="remember" value="1" class="h-5 w-5 border border-bauhaus-black bg-bauhaus-paper accent-bauhaus-blue">
                    <span class="text-sm font-semibold uppercase tracking-widest">Ingat saya</span>
                </label>

                <button type="submit" class="bauhaus-btn w-full bg-bauhaus-blue text-white hover:bg-bauhaus-blue-dark">
                    Masuk
                </button>
            </form>

            <div class="mt-8 flex items-center gap-4">
                <span class="h-1.5 w-1.5 bg-bauhaus-blue"></span>
                <span class="h-1.5 w-1.5 bg-bauhaus-yellow"></span>
                <span class="h-1.5 w-1.5 bg-bauhaus-blue"></span>
                <span class="h-1.5 w-1.5 bg-bauhaus-black"></span>
                <span class="h-px flex-1 bg-bauhaus-black"></span>
                <span class="text-xs font-bold uppercase tracking-widest text-bauhaus-ink">Sistem Informasi Aset</span>
            </div>
        </div>
    </div>
</x-bauhaus.layout>
